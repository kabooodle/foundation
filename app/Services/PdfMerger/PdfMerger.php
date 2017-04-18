<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\PdfMerger;

use Exception;
use LynX39\LaraPdfMerger\PdfManage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/**
 * Class PdfMerger
 */
class PdfMerger implements PdfMergerInterface
{
    /**
     * @var PdfManage
     */
    public $client;

    /**
     * @param PdfManage $client
     */
    public function __construct(PdfManage $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function addPdf(string $filepath, string $pages = 'all', string $orientation = null)
    {
        try {
            return $this->client->addPDF($filepath, $pages, $orientation);
        } catch (Exception $e) {
            throw new FileNotFoundException;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function merge(string $outputmode = self::OUTPUT_MODE_BROWSER, string $outputpath, string $orientation = SELF::ORIENTATION_PORTRAIT)
    {
        return $this->client->merge($outputmode, $outputpath, $orientation);
    }
}