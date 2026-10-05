<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Provider-neutral RFC 3161 timestamp authority client.
 *
 * The returned token remains opaque to the archive registry. The lightweight
 * verifier checks the SHA-256 message imprint; qualification and trust-chain
 * validation remain properties of the configured TSA and its trust policy.
 */
final class Rfc3161TimestampAuthority
{
    private const SHA256_OID = "\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x01";

    public function __construct(
        private string $endpoint,
        private ?string $username = null,
        private ?string $password = null,
        private int $timeout = 30
    ) {
        $this->endpoint = trim($this->endpoint);
        if (filter_var($this->endpoint, FILTER_VALIDATE_URL) === false
            || parse_url($this->endpoint, PHP_URL_SCHEME) !== 'https') {
            throw new InvalidArgumentException('The timestamp authority endpoint must use HTTPS.');
        }
        $this->timeout = max(5, min(300, $this->timeout));
    }

    /**
     * @return array{token: string, token_sha256: string, message_imprint_sha256: string}
     */
    public function timestamp(string $documentSha256): array
    {
        $documentSha256 = strtolower(trim($documentSha256));
        if (preg_match('/^[a-f0-9]{64}$/', $documentSha256) !== 1) {
            throw new InvalidArgumentException('The timestamp subject SHA-256 is invalid.');
        }

        $nonce = random_bytes(16);
        $query = $this->timestampQuery(hex2bin($documentSha256), $nonce);
        $response = $this->send($query);
        $token = $this->extractToken($response);
        if ( ! $this->verify($token, $documentSha256)) {
            throw new RuntimeException('The timestamp token does not contain the requested SHA-256 imprint.');
        }

        return [
            'token' => $token,
            'token_sha256' => hash('sha256', $token),
            'message_imprint_sha256' => $documentSha256,
        ];
    }

    public function verify(string $token, string $documentSha256): bool
    {
        $documentSha256 = strtolower(trim($documentSha256));
        if (preg_match('/^[a-f0-9]{64}$/', $documentSha256) !== 1 || $token === '') {
            return false;
        }

        return $this->containsMessageImprint($token, hex2bin($documentSha256), 0);
    }

    private function send(string $query): string
    {
        $curl = curl_init($this->endpoint);
        if ($curl === false) {
            throw new RuntimeException('The timestamp authority connection could not be initialized.');
        }

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_HTTPHEADER => [
                'Accept: application/timestamp-reply',
                'Content-Type: application/timestamp-query',
            ],
            CURLOPT_CONNECTTIMEOUT => min(10, $this->timeout),
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];
        if ($this->username !== null && $this->password !== null) {
            $options[CURLOPT_USERPWD] = $this->username . ':' . $this->password;
        }
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($response === false || $status < 200 || $status >= 300) {
            throw new RuntimeException('The timestamp authority request failed.');
        }

        return $response;
    }

    private function timestampQuery(string $digest, string $nonce): string
    {
        $algorithm = $this->sequence(self::SHA256_OID . "\x05\x00");
        $imprint = $this->sequence($algorithm . $this->tlv("\x04", $digest));
        $request = $this->integer("\x01")
            . $imprint
            . $this->integer($nonce)
            . "\x01\x01\xff";

        return $this->sequence($request);
    }

    private function extractToken(string $response): string
    {
        $outer = $this->readTlv($response, 0);
        if ($outer === null || $outer['tag'] !== 0x30) {
            throw new RuntimeException('The timestamp authority response is malformed.');
        }
        $status = $this->readTlv($outer['value'], 0);
        if ($status === null || $status['tag'] !== 0x30) {
            throw new RuntimeException('The timestamp authority status is malformed.');
        }
        $statusCode = $this->readTlv($status['value'], 0);
        if ($statusCode === null || $statusCode['tag'] !== 0x02 || ltrim($statusCode['value'], "\0") !== '') {
            throw new RuntimeException('The timestamp authority rejected the request.');
        }

        $token = $this->readTlv($outer['value'], $status['next']);
        if ($token === null || $token['tag'] !== 0x30) {
            throw new RuntimeException('The timestamp authority returned no token.');
        }

        return $token['raw'];
    }

    private function containsMessageImprint(string $data, string $digest, int $depth): bool
    {
        if ($depth > 20) {
            return false;
        }
        $offset = 0;
        while ($offset < strlen($data)) {
            $tlv = $this->readTlv($data, $offset);
            if ($tlv === null) {
                return false;
            }
            if ($tlv['tag'] === 0x30) {
                $algorithm = $this->readTlv($tlv['value'], 0);
                $hash = $algorithm === null ? null : $this->readTlv($tlv['value'], $algorithm['next']);
                if ($algorithm !== null && $algorithm['tag'] === 0x30
                    && str_contains($algorithm['value'], self::SHA256_OID)
                    && $hash !== null && $hash['tag'] === 0x04
                    && hash_equals($digest, $hash['value'])) {
                    return true;
                }
                if ($this->containsMessageImprint($tlv['value'], $digest, $depth + 1)) {
                    return true;
                }
            }
            $offset = $tlv['next'];
        }

        return false;
    }

    /** @return array{tag: int, value: string, raw: string, next: int}|null */
    private function readTlv(string $data, int $offset): ?array
    {
        $length = strlen($data);
        if ($offset >= $length) {
            return null;
        }
        $tag = ord($data[$offset]);
        $position = $offset + 1;
        if ($position >= $length) {
            return null;
        }
        $firstLength = ord($data[$position++]);
        if (($firstLength & 0x80) === 0) {
            $valueLength = $firstLength;
        } else {
            $octets = $firstLength & 0x7f;
            if ($octets < 1 || $octets > 4 || $position + $octets > $length) {
                return null;
            }
            $valueLength = 0;
            for ($i = 0; $i < $octets; $i++) {
                $valueLength = ($valueLength << 8) | ord($data[$position++]);
            }
        }
        $end = $position + $valueLength;
        if ($end > $length) {
            return null;
        }

        return [
            'tag' => $tag,
            'value' => substr($data, $position, $valueLength),
            'raw' => substr($data, $offset, $end - $offset),
            'next' => $end,
        ];
    }

    private function sequence(string $value): string
    {
        return $this->tlv("\x30", $value);
    }

    private function integer(string $value): string
    {
        if ((ord($value[0]) & 0x80) !== 0) {
            $value = "\0" . $value;
        }

        return $this->tlv("\x02", ltrim($value, "\0") === '' ? "\0" : $value);
    }

    private function tlv(string $tag, string $value): string
    {
        $length = strlen($value);
        if ($length < 128) {
            $encodedLength = chr($length);
        } else {
            $encoded = ltrim(pack('N', $length), "\0");
            $encodedLength = chr(0x80 | strlen($encoded)) . $encoded;
        }

        return $tag . $encodedLength . $value;
    }
}
