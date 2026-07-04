<?php

defined('BASEPATH') || exit('No direct script access allowed');

enum MerchantResponseStatus: string
{
    case Draft    = 'draft';
    case Sent     = 'sent';
    case Pending  = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Received = 'received';
    case Error    = 'error';
    case Unknown  = 'unknown';

    public function isSuccessful(): ?bool
    {
        return match ($this) {
            self::Accepted, self::Received, self::Sent => true,
            self::Rejected, self::Error                => false,
            default                                    => null,
        };
    }
}
