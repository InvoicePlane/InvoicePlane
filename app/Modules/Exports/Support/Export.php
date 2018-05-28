<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

namespace FI\Modules\Exports\Support;

use Exporter\Handler;
use Exporter\Source\ArraySourceIterator;

class Export
{
    protected $exportType;

    protected $fileName;

    protected $storagePath;

    protected $writerType;

    public function __construct($exportType, $writerType)
    {
        $this->exportType  = $exportType;
        $this->storagePath = storage_path('app');
        $this->writerType  = $writerType;
    }

    public function writeFile()
    {
        $resultsClass = 'FI\Modules\Exports\Support\Results\\' . $this->exportType;
        $writerClass  = 'Exporter\Writer\\' . $this->writerType;

        $fileExtension  = strtolower(str_replace('Writer', '', $this->writerType));
        $this->fileName = $this->exportType . 'Export.' . $fileExtension;

        if (file_exists($this->storagePath . '/' . $this->fileName))
        {
            unlink($this->storagePath . '/' . $this->fileName);
        }

        $results = new $resultsClass;

        $source = new ArraySourceIterator($results->getResults());

        $writer = new $writerClass($this->storagePath . '/' . $this->fileName);

        Handler::create($source, $writer)->export();
    }

    public function getDownloadPath()
    {
        return $this->storagePath . '/' . $this->fileName;
    }
}