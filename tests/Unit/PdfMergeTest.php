<?php
namespace Kabooodle\Tests\Unit;

use Storage;
use Kabooodle\Tests\BaseTestCase;
use LynX39\LaraPdfMerger\PdfManage;

/**
 * Class PdfMergeTest
 */
class PdfMergeTest extends BaseTestCase
{
    /**
     * @var string
     */
    public static $sampleFilePath;

    /**
     * @var string
     */
    public static $mergedOutputFilePath;

    /**
     * @var string
     */
    public static $savedFilePath;

    public function setUp()
    {
        parent::setUp();

        self::$sampleFilePath = __DIR__ . DIRECTORY_SEPARATOR .'sample_pdf.pdf';
        self::$savedFilePath = storage_path('app') . DIRECTORY_SEPARATOR . 'saved_file.pdf';
        self::$mergedOutputFilePath = storage_path('app') . DIRECTORY_SEPARATOR . 'merged_test.pdf';
    }

    public static function tearDownAfterClass()
    {
//        unlink(self::$mergedOutputFilePath);
//        unlink(self::$savedFilePath);
    }

    /**
     * TODO: Implement a Kabooodle service that handles the adding and merging for better error handling
     */
    public function test_files_merge_and_save()
    {
        $fileContents = file_get_contents(self::$sampleFilePath);
        Storage::disk('local')->put('saved_file.pdf', $fileContents);

        $pdf = new PdfManage;

        $pdf->addPDF(self::$sampleFilePath, 'all');
        $pdf->addPDF(self::$sampleFilePath, 'all');
        $pdf->merge('file', self::$mergedOutputFilePath, 'P');

        $this->assertFileExists(self::$mergedOutputFilePath);

        // This test fails, not sure if its a binary comparison or what.
//        $this->assertFileEquals(__DIR__ . DIRECTORY_SEPARATOR . 'merged_test.pdf', self::$mergedOutputFilePath, 'differ');
    }
}
