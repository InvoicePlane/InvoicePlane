<?php

defined('BASEPATH') || exit('No direct script access allowed');

use Aws\Exception\AwsException;
use Aws\S3\S3Client;

/**
 * S3 Object Lock connector for WORM archive packages.
 *
 * The target bucket must have versioning and Object Lock enabled. Objects
 * are written in COMPLIANCE mode and cannot be shortened or deleted before
 * their retention date by a bucket administrator.
 */
final class S3ObjectLockArchiveConnector
{
    private const MAX_METADATA_VALUE_LENGTH = 500;

    private S3Client $client;

    private string $bucket;

    public function __construct(array $settings, ?S3Client $client = null)
    {
        $this->bucket = trim((string) ($settings['bucket'] ?? ''));
        if ($this->bucket === '' || preg_match('/^[a-z0-9][a-z0-9.-]{1,61}[a-z0-9]$/', $this->bucket) !== 1) {
            throw new InvalidArgumentException('A valid S3 bucket is required.');
        }

        if ($client !== null) {
            $this->client = $client;

            return;
        }

        $region = trim((string) ($settings['region'] ?? ''));
        if ($region === '' || preg_match('/^[a-z0-9-]+$/', $region) !== 1) {
            throw new InvalidArgumentException('A valid S3 region is required.');
        }

        $clientOptions = [
            'version' => 'latest',
            'region' => $region,
            'http' => [
                'connect_timeout' => 10,
                'timeout' => 120,
                'allow_redirects' => false,
                'verify' => true,
            ],
        ];
        if (isset($settings['endpoint']) && trim((string) $settings['endpoint']) !== '') {
            $endpoint = trim((string) $settings['endpoint']);
            if (filter_var($endpoint, FILTER_VALIDATE_URL) === false
                || parse_url($endpoint, PHP_URL_SCHEME) !== 'https') {
                throw new InvalidArgumentException('The S3 endpoint must use HTTPS.');
            }
            $clientOptions['endpoint'] = rtrim($endpoint, '/');
            $clientOptions['use_path_style_endpoint'] = (bool) ($settings['path_style'] ?? false);
        }

        $accessKey = trim((string) ($settings['access_key'] ?? ''));
        $secretKey = (string) ($settings['secret_key'] ?? '');
        if ($accessKey !== '' || $secretKey !== '') {
            if ($accessKey === '' || $secretKey === '') {
                throw new InvalidArgumentException('Both S3 access and secret keys are required.');
            }
            $clientOptions['credentials'] = [
                'key' => $accessKey,
                'secret' => $secretKey,
                'token' => isset($settings['session_token']) ? (string) $settings['session_token'] : null,
            ];
        }

        $this->client = new S3Client($clientOptions);
    }

    /**
     * @return array{versioning: string, object_lock: string}
     */
    public function assertWormBucket(): array
    {
        try {
            $versioning = $this->client->getBucketVersioning(['Bucket' => $this->bucket]);
            $lock = $this->client->getObjectLockConfiguration(['Bucket' => $this->bucket]);
        } catch (AwsException $exception) {
            throw new RuntimeException('The S3 bucket WORM configuration could not be verified.', 0, $exception);
        }

        if (($versioning['Status'] ?? null) !== 'Enabled') {
            throw new RuntimeException('S3 versioning must be enabled for WORM storage.');
        }
        if (($lock['ObjectLockConfiguration']['ObjectLockEnabled'] ?? null) !== 'Enabled') {
            throw new RuntimeException('S3 Object Lock must be enabled for WORM storage.');
        }

        return [
            'versioning' => (string) $versioning['Status'],
            'object_lock' => (string) $lock['ObjectLockConfiguration']['ObjectLockEnabled'],
        ];
    }

    /**
     * @return array{bucket: string, key: string, version_id: string|null, sha256: string, retain_until: string, mode: string, legal_hold: bool}
     */
    public function store(
        string $packagePath,
        string $objectKey,
        DateTimeInterface $retainUntil,
        bool $legalHold = false,
        array $metadata = [],
        string $contentType = 'application/octet-stream'
    ): array {
        $this->assertWormBucket();
        if ( ! is_file($packagePath) || ! is_readable($packagePath)) {
            throw new RuntimeException('The archive package is unavailable for S3 storage.');
        }

        $objectKey = $this->validateObjectKey($objectKey);
        $this->assertObjectAbsent($objectKey);
        $retainUntilUtc = (new DateTimeImmutable($retainUntil->format('Y-m-d H:i:s'), $retainUntil->getTimezone()))
            ->setTimezone(new DateTimeZone('UTC'));
        if ($retainUntilUtc <= new DateTimeImmutable('now', new DateTimeZone('UTC'))) {
            throw new InvalidArgumentException('S3 Object Lock retention must be in the future.');
        }

        $sha256 = hash_file('sha256', $packagePath);
        if ( ! is_string($sha256)) {
            throw new RuntimeException('The archive package hash could not be calculated.');
        }

        $stream = fopen($packagePath, 'rb');
        if ($stream === false) {
            throw new RuntimeException('The archive package could not be opened.');
        }

        try {
            $parameters = [
                'Bucket' => $this->bucket,
                'Key' => $objectKey,
                'Body' => $stream,
                'ContentType' => $contentType,
                'ChecksumSHA256' => base64_encode(hex2bin($sha256)),
                'ObjectLockMode' => 'COMPLIANCE',
                'ObjectLockRetainUntilDate' => $retainUntilUtc->format(DateTimeInterface::ATOM),
                'Metadata' => $this->metadata($metadata),
            ];
            if ($legalHold) {
                $parameters['ObjectLockLegalHoldStatus'] = 'ON';
            }
            $result = $this->client->putObject($parameters);
        } catch (AwsException $exception) {
            throw new RuntimeException('The archive package could not be stored in S3 Object Lock.', 0, $exception);
        } finally {
            fclose($stream);
        }

        return [
            'bucket' => $this->bucket,
            'key' => $objectKey,
            'version_id' => isset($result['VersionId']) ? (string) $result['VersionId'] : null,
            'sha256' => $sha256,
            'retain_until' => $retainUntilUtc->format('Y-m-d H:i:s'),
            'mode' => 'COMPLIANCE',
            'legal_hold' => $legalHold,
        ];
    }

    public function verifyObject(string $objectKey, ?string $versionId = null): bool
    {
        $parameters = [
            'Bucket' => $this->bucket,
            'Key' => $this->validateObjectKey($objectKey),
        ];
        if ($versionId !== null && $versionId !== '') {
            $parameters['VersionId'] = $versionId;
        }

        try {
            $result = $this->client->headObject($parameters);
        } catch (AwsException $exception) {
            throw new RuntimeException('The S3 archive object could not be verified.', 0, $exception);
        }

        return ($result['ObjectLockMode'] ?? null) === 'COMPLIANCE'
            && isset($result['ObjectLockRetainUntilDate']);
    }

    private function validateObjectKey(string $objectKey): string
    {
        $objectKey = trim($objectKey);
        if ($objectKey === '' || str_starts_with($objectKey, '/')
            || str_contains($objectKey, "\0")
            || str_contains($objectKey, '../')
            || str_contains($objectKey, '..\\')) {
            throw new InvalidArgumentException('The S3 object key is invalid.');
        }

        return $objectKey;
    }

    private function assertObjectAbsent(string $objectKey): void
    {
        try {
            $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $objectKey,
            ]);
        } catch (AwsException $exception) {
            if ($exception->getStatusCode() === 404) {
                return;
            }

            throw new RuntimeException('The S3 archive object could not be checked.', 0, $exception);
        }

        throw new RuntimeException('The S3 archive object already exists and cannot be overwritten.');
    }

    private function metadata(array $metadata): array
    {
        $result = [];
        foreach ($metadata as $key => $value) {
            $key = strtolower(trim((string) $key));
            if (preg_match('/^[a-z0-9-]{1,64}$/', $key) !== 1 || ! is_scalar($value)) {
                continue;
            }
            $result[$key] = mb_substr(trim((string) $value), 0, self::MAX_METADATA_VALUE_LENGTH);
        }

        return $result;
    }
}
