<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Tests\Unit\Services\PdfMerger;

use Storage;
use Mockery;
use InvalidArgumentException;
use Kabooodle\Tests\BaseTestCase;
use LynX39\LaraPdfMerger\PdfManage;
use Kabooodle\Services\PdfMerger\PdfMerger;
use Kabooodle\Services\PdfMerger\PdfMergerInterface;

/**
 * Class PdfMergeTest
 */
class PdfMergeTest extends BaseTestCase
{
    /**
     * @var string
     */
    public $sampleFilePath;

    /**
     * @var string
     */
    public $expectedMergeResult;

    /**
     * @var string
     */
    public $mergedOutputFilePath;

    /**
     * @var string
     */
    public $savedFilePath;

    public function setUp()
    {
        parent::setUp();

        $this->sampleFilePath = __DIR__ . DIRECTORY_SEPARATOR .'sample_pdf.pdf';
        $this->expectedMergeResult = __DIR__ . DIRECTORY_SEPARATOR .'merged_test.pdf';
        $this->savedFilePath = storage_path('app') . DIRECTORY_SEPARATOR . 'saved_file.pdf';
        $this->mergedOutputFilePath = storage_path('app') . DIRECTORY_SEPARATOR . 'merged_test.pdf';
    }

    public function test_methods()
    {
        $fileContents = file_get_contents($this->sampleFilePath);
        Storage::disk('local')->put('saved_file.pdf', $fileContents);

        $pdf = new PdfMerger($mock = Mockery::mock(PDFManage::class));

        $mock->shouldReceive('addPDF')
            ->twice()
            ->with($this->sampleFilePath, 'all', null)
            ->andReturn($this->returnSelf());

         $mock->shouldReceive('merge')
             ->once()
             ->with('file', $this->mergedOutputFilePath, 'P')
             ->andReturn(true);

        $pdf->addPdf($this->sampleFilePath, 'all');
        $pdf->addPdf($this->sampleFilePath, 'all');
        $pdf->merge('file', $this->mergedOutputFilePath, 'P');
    }

    public function test_merging_two_into_one()
    {
        $pdf = app()->make(PdfMergerInterface::class);

        $pdf->addPdf($this->sampleFilePath, 'all');
        $pdf->addPdf($this->sampleFilePath, 'all');
        $pdf->merge('file', $this->mergedOutputFilePath, 'P');

        $this->assertFileExists($this->mergedOutputFilePath);
        $this->assertEquals(mime_content_type($this->expectedMergeResult), mime_content_type($this->mergedOutputFilePath));
        $this->assertEquals(filesize($this->expectedMergeResult), filesize($this->mergedOutputFilePath));
        unlink($this->savedFilePath);
        unlink($this->mergedOutputFilePath);
    }

    public function test_throws_exception_when_file_not_found()
    {
        $this->expectException(InvalidArgumentException::class);

        $pdf = app()->make(PdfMergerInterface::class);

        $pdf->addPdf(__DIR__ . DIRECTORY_SEPARATOR . 'not_a_pdf.txt');
    }
}
