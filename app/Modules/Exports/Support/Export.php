<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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