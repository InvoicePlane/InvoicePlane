<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Mandatory and optional French invoice lifecycle codes (DGFiP v3.2).
 */
enum FrenchInvoiceLifecycleStatus: int
{
    case Deposited             = 200;
    case PlatformIssued        = 201;
    case PlatformReceived      = 202;
    case Available             = 203;
    case TakenInCharge         = 204;
    case Approved              = 205;
    case PartiallyApproved     = 206;
    case Disputed              = 207;
    case Suspended             = 208;
    case Completed             = 209;
    case Refused               = 210;
    case PaymentTransmitted    = 211;
    case Collected             = 212;
    case Rejected              = 213;
    case RegulatoryDeposited   = 250;
    case RegulatoryRejected    = 251;
    case ReportingDeposited    = 300;
    case ReportingRejected     = 301;
    case MandatoryStatusReject = 601;

    public function isMandatory(): bool
    {
        return in_array($this, [
            self::Deposited,
            self::Refused,
            self::Collected,
            self::Rejected,
            self::RegulatoryDeposited,
            self::RegulatoryRejected,
            self::ReportingDeposited,
            self::ReportingRejected,
            self::MandatoryStatusReject,
        ], true);
    }

    public function merchantStatus(): MerchantResponseStatus
    {
        return match ($this) {
            self::Deposited, self::PlatformIssued                               => MerchantResponseStatus::Sent,
            self::PlatformReceived, self::TakenInCharge                         => MerchantResponseStatus::Received,
            self::Available                                                     => MerchantResponseStatus::Delivered,
            self::Approved, self::RegulatoryDeposited, self::ReportingDeposited => MerchantResponseStatus::Accepted,
            self::PartiallyApproved                                             => MerchantResponseStatus::PartiallyAccepted,
            self::Disputed                                                      => MerchantResponseStatus::Disputed,
            self::Suspended                                                     => MerchantResponseStatus::Suspended,
            self::Completed                                                     => MerchantResponseStatus::Completed,
            self::Refused                                                       => MerchantResponseStatus::Refused,
            self::PaymentTransmitted, self::Collected                           => MerchantResponseStatus::Paid,
            self::Rejected, self::RegulatoryRejected, self::ReportingRejected,
            self::MandatoryStatusReject => MerchantResponseStatus::Rejected,
        };
    }
}

enum MerchantResponseStatus: string
{
    case Draft             = 'draft';
    case Sent              = 'sent';
    case Pending           = 'pending';
    case Delivered         = 'delivered';
    case Accepted          = 'accepted';
    case PartiallyAccepted = 'partially_accepted';
    case Disputed          = 'disputed';
    case Suspended         = 'suspended';
    case Completed         = 'completed';
    case Refused           = 'refused';
    case Paid              = 'paid';
    case Rejected          = 'rejected';
    case Received          = 'received';
    case Error             = 'error';
    case Unknown           = 'unknown';

    public static function fromExternal(mixed $status, self $default = self::Unknown): self
    {
        if (is_int($status) || is_string($status) && preg_match('/^[0-9]{3}$/', $status) === 1) {
            $frenchStatus = FrenchInvoiceLifecycleStatus::tryFrom((int) $status);
            if ($frenchStatus !== null) {
                return $frenchStatus->merchantStatus();
            }
        }

        if ( ! is_string($status)) {
            return $default;
        }

        $normalized = mb_strtolower(trim($status));

        return match ($normalized) {
            'processing', 'in_progress' => self::Pending,
            'submitted', 'deposited'    => self::Sent,
            'available'                 => self::Delivered,
            'approved'                  => self::Accepted,
            'partially_approved'        => self::PartiallyAccepted,
            'collected'                 => self::Paid,
            default                     => self::tryFrom($normalized) ?? $default,
        };
    }

    public function isSuccessful(): ?bool
    {
        return match ($this) {
            self::Accepted, self::PartiallyAccepted, self::Completed, self::Delivered,
            self::Paid, self::Received, self::Sent     => true,
            self::Refused, self::Rejected, self::Error => false,
            default                                    => null,
        };
    }
}
