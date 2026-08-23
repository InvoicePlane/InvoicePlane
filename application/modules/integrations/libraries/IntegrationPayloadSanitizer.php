<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Removes credentials, signed URLs, and document bodies from audit payloads.
 */
final class IntegrationPayloadSanitizer
{
    private const MAX_DEPTH = 8;

    private const MAX_ITEMS = 100;

    private const MAX_JSON_BYTES = 64 * 1024;

    private const MAX_STRING_BYTES = 4000;

    private const REDACTED = '[REDACTED]';

    public static function json(array $payload): string
    {
        $json = json_encode(
            self::sanitize($payload),
            JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_SLASHES
        );

        if (mb_strlen($json, '8bit') <= self::MAX_JSON_BYTES) {
            return $json;
        }

        return json_encode([
            '_truncated' => true,
            'sha256'     => hash('sha256', $json),
        ], JSON_THROW_ON_ERROR);
    }

    public static function sanitize(array $payload): array
    {
        return self::sanitizeArray($payload, 0);
    }

    public static function text(mixed $value, int $maximumBytes = 1000): ?string
    {
        if ( ! is_scalar($value) || $value === '') {
            return null;
        }

        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', (string) $value) ?? '';
        $text = preg_replace('/\bBearer\s+[A-Za-z0-9._~+\/=:-]+/i', 'Bearer ' . self::REDACTED, $text) ?? '';
        $text = preg_replace(
            '/\b(access[_-]?token|refresh[_-]?token|client[_-]?secret|api[_-]?key|password)\s*[:=]\s*[^\s,;]+/i',
            '$1=' . self::REDACTED,
            $text
        ) ?? '';
        $text = preg_replace_callback(
            '~https://[^\s<>"\']+~i',
            static fn (array $matches): string => self::sanitizeUrl($matches[0]),
            $text
        ) ?? '';

        return mb_strlen($text, '8bit') <= $maximumBytes
            ? $text
            : mb_substr($text, 0, $maximumBytes, '8bit') . '[TRUNCATED]';
    }

    private static function sanitizeArray(array $payload, int $depth): array
    {
        if ($depth >= self::MAX_DEPTH) {
            return ['_truncated' => true];
        }

        $sanitized = [];
        $count     = 0;

        foreach ($payload as $key => $value) {
            if ($count >= self::MAX_ITEMS) {
                $sanitized['_truncated'] = true;
                break;
            }
            $count++;

            $normalizedKey = mb_strtolower((string) $key);
            if (self::isSecretKey($normalizedKey)
                || self::isPersonalDataKey($normalizedKey)
                || self::isDocumentBodyKey($normalizedKey)) {
                $sanitized[$key] = self::REDACTED;
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value, $depth + 1);
            } elseif (is_string($value)) {
                $sanitized[$key] = self::isUrlKey($normalizedKey)
                    ? self::sanitizeUrl($value)
                    : self::boundedString($value);
            } elseif (is_scalar($value) || $value === null) {
                $sanitized[$key] = $value;
            } else {
                $sanitized[$key] = '[UNSUPPORTED]';
            }
        }

        return $sanitized;
    }

    private static function isSecretKey(string $key): bool
    {
        return preg_match('/(?:authorization|credential|password|secret|token|api[_-]?key)/', $key) === 1;
    }

    private static function isDocumentBodyKey(string $key): bool
    {
        return in_array($key, [
            'body',
            'content',
            'content_base64',
            'document_content',
            'document_path',
            'file_content',
            'file_path',
        ], true);
    }

    private static function isPersonalDataKey(string $key): bool
    {
        return preg_match('/(?:^|_)(?:address|bic|buyer_name|email|iban|invoice_number|issuer_name|participant_id|phone|postal_code|receiver|sender|siren|siret|street|supplier_name|tin|vat_number)(?:_|$)/', $key) === 1;
    }

    private static function isUrlKey(string $key): bool
    {
        return $key === 'url' || str_ends_with($key, '_url');
    }

    private static function sanitizeUrl(string $url): string
    {
        $parts = parse_url($url);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return self::boundedString($url);
        }

        $safe = $parts['scheme'] . '://' . $parts['host'];
        if (isset($parts['port'])) {
            $safe .= ':' . $parts['port'];
        }

        return self::boundedString($safe . ($parts['path'] ?? ''));
    }

    private static function boundedString(string $value): string
    {
        if (preg_match('//u', $value) !== 1) {
            return self::REDACTED;
        }

        if (mb_strlen($value, '8bit') > 512
            && mb_strlen($value, '8bit') % 4 === 0
            && preg_match('/^[A-Za-z0-9+\/]+={0,2}$/', $value) === 1) {
            return self::REDACTED;
        }

        if (mb_strlen($value, '8bit') <= self::MAX_STRING_BYTES) {
            return $value;
        }

        return mb_substr($value, 0, self::MAX_STRING_BYTES, '8bit') . '[TRUNCATED]';
    }
}
