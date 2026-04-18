<?php

namespace types;

abstract class AbstractType
{
    abstract public function generate($generalOptions, $invoiceOptions);

    abstract public function getGeneralOptions();
    abstract public function getInvoiceOptions();
    abstract public function validateGeneralOptions($generalOptions);

    abstract public function validateInvoiceOptions($invoiceOptions);
}
