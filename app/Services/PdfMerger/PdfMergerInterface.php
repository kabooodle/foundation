<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\PdfMerger;

use LynX39\LaraPdfMerger\PdfManage;

/**
 * Interface PdfMergerInterface
 */
interface PdfMergerInterface
{
    const OUTPUT_MODE_BROWSER = 'browser';
    const OUTPUT_MODE_FILE = 'file';
    const OUTPUT_MODE_DOWNLOAD = 'download';
    const OUTPUT_MODE_STRING = 'string';

    const ORIENTATION_PORTRAIT = 'P';
    const ORIENTATION_LANDSCAPE = 'L';

    /**
     * @param string      $filepath
     * @param string      $pages
     * @param string|null $orientation
     * @return PdfManage
     *
     * @throws FileNotFoundException
     */
    public function addPdf(string $filepath, string $pages = 'all', string $orientation = null);

    /**
     * @param string $outputmode
     * @param string $outputpath
     * @param string $orientation
     *
     * @return \LynX39\LaraPdfMerger\PDFManage
     */
    public function merge(string $outputmode = self::OUTPUT_MODE_BROWSER, string $outputpath, string $orientation = SELF::ORIENTATION_PORTRAIT);
}