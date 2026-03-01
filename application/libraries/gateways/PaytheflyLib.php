<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane - PayTheFly Pro Integration Library
 *
 * Handles EIP-712 signature generation and webhook verification
 * for PayTheFly Pro crypto payment gateway.
 *
 * @author      PayTheFly Integration
 * @link        https://pro.paythefly.com
 */

class PaytheflyLib
{
    /**
     * PayTheFly Pro base URL
     */
    const BASE_URL = 'https://pro.paythefly.com';

    /**
     * Supported chains with their configurations
     */
    const CHAINS = [
        'BSC' => [
            'chainId'       => 56,
            'decimals'      => 18,
            'nativeToken'   => '0x0000000000000000000000000000000000000000',
            'symbol'        => 'BNB',
        ],
        'TRON' => [
            'chainId'       => 728126428,
            'decimals'      => 6,
            'nativeToken'   => 'T9yD14Nj9j7xAB4dbGeiX9h8unkKHxuWwb',
            'symbol'        => 'TRX',
        ],
    ];

    /**
     * EIP-712 Domain name
     */
    const DOMAIN_NAME = 'PayTheFlyPro';

    /**
     * EIP-712 Domain version
     */
    const DOMAIN_VERSION = '1';

    /**
     * @var string Project ID from PayTheFly Pro dashboard
     */
    protected $projectId;

    /**
     * @var string Private key for EIP-712 signing
     */
    protected $privateKey;

    /**
     * @var string Project key for webhook HMAC verification
     */
    protected $projectKey;

    /**
     * @var string Contract address for EIP-712 domain
     */
    protected $contractAddress;

    /**
     * @var string Default chain identifier (BSC or TRON)
     */
    protected $defaultChain;

    /**
     * @var int Payment deadline offset in seconds (default: 30 minutes)
     */
    protected $deadlineOffset;

    /**
     * Constructor
     *
     * @param array $params Configuration parameters
     */
    public function __construct(array $params = [])
    {
        $this->projectId       = $params['project_id'] ?? '';
        $this->privateKey      = $params['private_key'] ?? '';
        $this->projectKey      = $params['project_key'] ?? '';
        $this->contractAddress = $params['contract_address'] ?? '';
        $this->defaultChain    = $params['default_chain'] ?? 'BSC';
        $this->deadlineOffset  = (int) ($params['deadline_offset'] ?? 1800);
    }

    /**
     * Generate a payment URL for a given invoice.
     *
     * @param string      $serialNo     Unique serial number (e.g., invoice number)
     * @param float       $amount       Human-readable amount (e.g., 10.50)
     * @param string|null $chain        Chain identifier (BSC or TRON), defaults to config
     * @param string|null $tokenAddress Token contract address, null for native token
     * @param int|null    $deadline     Unix timestamp deadline, null for auto-calculated
     *
     * @return array ['url' => string, 'deadline' => int, 'signature' => string]
     */
    public function generatePaymentUrl(
        string $serialNo,
        float $amount,
        ?string $chain = null,
        ?string $tokenAddress = null,
        ?int $deadline = null
    ): array {
        $chain    = $chain ?: $this->defaultChain;
        $chainCfg = self::CHAINS[$chain] ?? self::CHAINS['BSC'];

        $tokenAddress = $tokenAddress ?: $chainCfg['nativeToken'];
        $deadline     = $deadline ?: (time() + $this->deadlineOffset);

        // Convert human-readable amount to raw amount for signing
        $rawAmount = $this->toRawAmount($amount, $chainCfg['decimals']);

        // Generate EIP-712 signature
        $signature = $this->signPaymentRequest(
            $this->projectId,
            $tokenAddress,
            $rawAmount,
            $serialNo,
            $deadline,
            $chainCfg['chainId']
        );

        // Build human-readable payment URL
        $params = [
            'chainId'   => $chainCfg['chainId'],
            'projectId' => $this->projectId,
            'amount'    => $this->formatHumanAmount($amount),
            'serialNo'  => $serialNo,
            'deadline'  => $deadline,
            'signature' => $signature,
            'token'     => $tokenAddress,
        ];

        $url = self::BASE_URL . '/pay?' . http_build_query($params);

        return [
            'url'       => $url,
            'deadline'  => $deadline,
            'signature' => $signature,
            'chain'     => $chain,
            'chainId'   => $chainCfg['chainId'],
            'rawAmount' => $rawAmount,
        ];
    }

    /**
     * Verify a webhook signature.
     *
     * @param string $data      The "data" field from webhook JSON body
     * @param string $sign      The "sign" field from webhook JSON body (hex)
     * @param int    $timestamp The "timestamp" field from webhook JSON body
     *
     * @return bool True if signature is valid
     */
    public function verifyWebhookSignature(string $data, string $sign, int $timestamp): bool
    {
        // Verify timestamp is within acceptable window (5 minutes)
        if (abs(time() - $timestamp) > 300) {
            log_message('warning', 'PayTheFly webhook: timestamp too old/new. Diff: ' . abs(time() - $timestamp) . 's');
            return false;
        }

        $payload  = $data . '.' . $timestamp;
        $expected = hash_hmac('sha256', $payload, $this->projectKey);

        return hash_equals($expected, $sign);
    }

    /**
     * Parse webhook data JSON string into an associative array.
     *
     * @param string $dataJson The "data" field (JSON string)
     *
     * @return array|null Parsed data or null on failure
     */
    public function parseWebhookData(string $dataJson): ?array
    {
        $data = json_decode($dataJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'PayTheFly webhook: invalid JSON in data field: ' . json_last_error_msg());
            return null;
        }

        // Validate required fields
        $required = ['project_id', 'serial_no', 'tx_hash', 'value', 'tx_type', 'confirmed'];
        foreach ($required as $field) {
            if ( ! isset($data[$field])) {
                log_message('error', 'PayTheFly webhook: missing required field: ' . $field);
                return null;
            }
        }

        return $data;
    }

    /**
     * Convert a human-readable amount to raw blockchain amount.
     *
     * @param float $amount   Human-readable amount (e.g., 10.50)
     * @param int   $decimals Number of decimal places for the token
     *
     * @return string Raw amount as string (to handle large numbers)
     */
    public function toRawAmount(float $amount, int $decimals): string
    {
        // Use bcmath for precision with large numbers
        if (function_exists('bcmul')) {
            return bcmul((string) $amount, bcpow('10', (string) $decimals, 0), 0);
        }

        // Fallback for environments without bcmath
        return number_format($amount * pow(10, $decimals), 0, '', '');
    }

    /**
     * Convert a raw blockchain amount to human-readable amount.
     *
     * @param string $rawAmount Raw amount string
     * @param int    $decimals  Number of decimal places for the token
     *
     * @return float Human-readable amount
     */
    public function fromRawAmount(string $rawAmount, int $decimals): float
    {
        if (function_exists('bcdiv')) {
            return (float) bcdiv($rawAmount, bcpow('10', (string) $decimals, 0), $decimals);
        }

        return (float) ($rawAmount / pow(10, $decimals));
    }

    /**
     * Format amount for URL parameter (human-readable).
     *
     * @param float $amount
     *
     * @return string
     */
    protected function formatHumanAmount(float $amount): string
    {
        // Remove trailing zeros but keep at least 2 decimal places
        $formatted = number_format($amount, 18, '.', '');
        $formatted = rtrim($formatted, '0');
        $formatted = rtrim($formatted, '.');

        // Ensure at least one decimal place for clarity
        if (strpos($formatted, '.') === false) {
            $formatted .= '.0';
        }

        return $formatted;
    }

    /**
     * Sign a PaymentRequest using EIP-712 typed data signing.
     *
     * Since PHP cannot directly sign with Ethereum private keys without
     * external libraries, this method constructs the EIP-712 hash that
     * should be signed. For production use, the signing should be done
     * server-side with a proper Ethereum signing library or delegated
     * to the PayTheFly API.
     *
     * @param string $projectId
     * @param string $token
     * @param string $amount     Raw amount
     * @param string $serialNo
     * @param int    $deadline
     * @param int    $chainId
     *
     * @return string Signature hex string prefixed with 0x
     */
    protected function signPaymentRequest(
        string $projectId,
        string $token,
        string $amount,
        string $serialNo,
        int $deadline,
        int $chainId
    ): string {
        // EIP-712 type hashes
        $domainSeparator = $this->hashEIP712Domain($chainId);
        $structHash      = $this->hashPaymentRequest($projectId, $token, $amount, $serialNo, $deadline);

        // EIP-712 final hash: keccak256("\x19\x01" || domainSeparator || structHash)
        $message = "\x19\x01" . $domainSeparator . $structHash;
        $hash    = $this->keccak256($message);

        // Sign with private key
        return $this->ecSign($hash, $this->privateKey);
    }

    /**
     * Compute EIP-712 domain separator hash.
     *
     * @param int $chainId
     *
     * @return string 32-byte binary hash
     */
    protected function hashEIP712Domain(int $chainId): string
    {
        $typeHash = $this->keccak256(
            'EIP712Domain(string name,string version,uint256 chainId,address verifyingContract)'
        );

        $nameHash    = $this->keccak256(self::DOMAIN_NAME);
        $versionHash = $this->keccak256(self::DOMAIN_VERSION);

        $encoded = $typeHash
            . $nameHash
            . $versionHash
            . str_pad($this->encodeUint256($chainId), 32, "\0", STR_PAD_LEFT)
            . str_pad($this->encodeAddress($this->contractAddress), 32, "\0", STR_PAD_LEFT);

        return $this->keccak256($encoded);
    }

    /**
     * Compute PaymentRequest struct hash.
     *
     * @param string $projectId
     * @param string $token
     * @param string $amount
     * @param string $serialNo
     * @param int    $deadline
     *
     * @return string 32-byte binary hash
     */
    protected function hashPaymentRequest(
        string $projectId,
        string $token,
        string $amount,
        string $serialNo,
        int $deadline
    ): string {
        $typeHash = $this->keccak256(
            'PaymentRequest(string projectId,address token,uint256 amount,string serialNo,uint256 deadline)'
        );

        $encoded = $typeHash
            . $this->keccak256($projectId)
            . str_pad($this->encodeAddress($token), 32, "\0", STR_PAD_LEFT)
            . str_pad($this->encodeUint256($amount), 32, "\0", STR_PAD_LEFT)
            . $this->keccak256($serialNo)
            . str_pad($this->encodeUint256($deadline), 32, "\0", STR_PAD_LEFT);

        return $this->keccak256($encoded);
    }

    /**
     * Keccak-256 hash function.
     *
     * Uses the keccak256 from kornrunner/keccak if available,
     * falls back to sha3-256 (note: SHA3-256 != Keccak-256 for Ethereum).
     *
     * @param string $data Binary data to hash
     *
     * @return string 32-byte binary hash
     */
    protected function keccak256(string $data): string
    {
        // IMPORTANT: Ethereum uses Keccak-256, NOT SHA3-256 (FIPS 202).
        // They differ in padding (Keccak: 0x01, SHA3: 0x06) and produce different hashes.
        // The kornrunner/keccak library is REQUIRED for correct EIP-712 signatures.

        if (!class_exists('kornrunner\Keccak')) {
            throw new \RuntimeException(
                'PayTheFly requires the kornrunner/keccak package for Ethereum-compatible hashing. '
                . 'Install it with: composer require kornrunner/keccak'
            );
        }

        return hex2bin(\kornrunner\Keccak::hash($data, 256));
    }

    /**
     * ECDSA sign a 32-byte hash with a private key.
     *
     * Uses the elliptic-php or simplito/elliptic-php library.
     *
     * @param string $hash       32-byte binary hash
     * @param string $privateKey Hex-encoded private key (with or without 0x prefix)
     *
     * @return string Hex-encoded signature with 0x prefix
     */
    protected function ecSign(string $hash, string $privateKey): string
    {
        // Strip 0x prefix if present
        $privateKey = ltrim($privateKey, '0x');

        // Try using simplito/elliptic-php
        if (class_exists('Elliptic\EC')) {
            $ec        = new \Elliptic\EC('secp256k1');
            $ecPrivKey = $ec->keyFromPrivate($privateKey, 'hex');
            $signature = $ecPrivKey->sign(bin2hex($hash), ['canonical' => true]);

            $r = str_pad($signature->r->toString(16), 64, '0', STR_PAD_LEFT);
            $s = str_pad($signature->s->toString(16), 64, '0', STR_PAD_LEFT);
            $v = dechex($signature->recoveryParam + 27);

            return '0x' . $r . $s . $v;
        }

        // Fallback: construct a placeholder signature for testing
        // In production, you MUST have the elliptic-php library installed
        log_message('error', 'PayTheFly: No ECDSA library available. Install simplito/elliptic-php.');
        return '0x' . str_repeat('0', 130);
    }

    /**
     * Encode a uint256 value to 32-byte binary.
     *
     * @param int|string $value
     *
     * @return string 32-byte binary
     */
    protected function encodeUint256($value): string
    {
        if (function_exists('gmp_init')) {
            $hex = gmp_strval(gmp_init((string) $value), 16);
            $hex = str_pad($hex, 64, '0', STR_PAD_LEFT);
            return hex2bin($hex);
        }

        $hex = dechex((int) $value);
        $hex = str_pad($hex, 64, '0', STR_PAD_LEFT);
        return hex2bin($hex);
    }

    /**
     * Encode an Ethereum address to 20-byte binary.
     *
     * @param string $address Hex address with 0x prefix
     *
     * @return string 20-byte binary
     */
    protected function encodeAddress(string $address): string
    {
        $address = ltrim($address, '0x');
        $address = str_pad($address, 40, '0', STR_PAD_LEFT);
        return hex2bin($address);
    }

    /**
     * Get chain configuration by chain symbol.
     *
     * @param string $chain Chain symbol (BSC or TRON)
     *
     * @return array Chain configuration
     */
    public function getChainConfig(string $chain): array
    {
        return self::CHAINS[$chain] ?? self::CHAINS['BSC'];
    }

    /**
     * Get all supported chains.
     *
     * @return array
     */
    public function getSupportedChains(): array
    {
        return self::CHAINS;
    }

    /**
     * Get the project ID.
     *
     * @return string
     */
    public function getProjectId(): string
    {
        return $this->projectId;
    }
}
