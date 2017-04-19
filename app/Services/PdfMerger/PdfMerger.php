<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\PdfMerger;

use Storage;
use Exception;
use Illuminate\Support\Str;
use InvalidArgumentException;
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
     * @param string      $url
     * @param string      $pages
     * @param string|null $orientation
     *
     * @return PdfManage|void
     * @throws FileNotFoundException|InvalidArgumentException
     */
    public function addRemotePdf(string $url, string $pages = 'all', string $orientation = null)
    {
        $stream = file_get_contents($url);

        if (! $stream){
            throw new FileNotFoundException('Invalid file');
        }

        $localFile = str_random(32);
        Storage::disk('local')->put($localFile, $stream);

        return $this->addPdf($localFile, $pages, $orientation);
    }

    /**
     * @param string $filePath
     *
     * @throws InvalidArgumentException
     */
    public function assertFileIsPdf(string $filePath)
    {
        $mimeType = mime_content_type($filePath);
        \Log::info($mimeType . ' '. $filePath);

        if (! Str::contains(strtolower($mimeType), 'pdf')) {
            throw new InvalidArgumentException('File is not a valid PDF');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addPdf(string $filepath, string $pages = 'all', string $orientation = null)
    {
        $this->assertFileIsPdf($filepath);

        return $this->client->addPDF($filepath, $pages, $orientation);
    }

    /**
     * {@inheritdoc}
     */
    public function merge(string $outputmode = self::OUTPUT_MODE_BROWSER, string $outputpath, string $orientation = SELF::ORIENTATION_PORTRAIT)
    {
        return $this->client->merge($outputmode, $outputpath, $orientation);
    }
}