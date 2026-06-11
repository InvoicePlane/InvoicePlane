<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class PdpStatusMapper
{
    const DRAFT = 'draft';
    const READY = 'ready';
    const SENT = 'sent';
    const DEPOSITED = 'deposited';
    const ACCEPTED = 'accepted';
    const REJECTED = 'rejected';
    const AVAILABLE = 'available';
    const PAID = 'paid';
    const CANCELLED = 'cancelled';
    const ERROR = 'error';

    public static function normalize($rawStatus, int $httpCode = 200): string
    {
        if ($httpCode < 200 || $httpCode >= 300) {
            return self::ERROR;
        }

        $s = strtolower(trim((string) $rawStatus));
        $map = array(
            'draft' => self::DRAFT,
            'ready' => self::READY,
            'created' => self::READY,
            'sent' => self::SENT,
            'submitted' => self::SENT,
            'deposited' => self::DEPOSITED,
            'deposee' => self::DEPOSITED,
            'accepted' => self::ACCEPTED,
            'approved' => self::ACCEPTED,
            'validated' => self::ACCEPTED,
            'rejected' => self::REJECTED,
            'refused' => self::REJECTED,
            'failed' => self::ERROR,
            'available' => self::AVAILABLE,
            'delivered' => self::AVAILABLE,
            'paid' => self::PAID,
            'cancelled' => self::CANCELLED,
            'canceled' => self::CANCELLED,
        );

        return $map[$s] ?? self::SENT;
    }
}
