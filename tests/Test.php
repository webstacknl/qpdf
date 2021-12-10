<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Webstack\QPDF\Exceptions\FileNotFoundException;
use Webstack\QPDF\QPDF;

class Test extends TestCase
{
    public function testVersion(): void
    {
        $output = QPDF::createInstance()->getVersion();

        self::assertMatchesRegularExpression('|\d.\d.\d|', $output);
    }

    /**
     * @throws FileNotFoundException
     */
    public function testSource(): void
    {
        $path = sys_get_temp_dir() .'/output.pdf';

        QPDF::createInstance()
            ->source('tests/files/source-1.pdf')
            ->write($path);

        self::assertFileExists($path);
        self::assertEquals(4, QPDF::createInstance()->getNumberOfPages($path));
    }

    /**
     * @throws FileNotFoundException
     */
    public function testFiles(): void
    {
        $path = sys_get_temp_dir() .'/output.pdf';

        QPDF::createInstance()
          ->addFile('tests/files/source-1.pdf')
          ->addFile('tests/files/source-2.pdf')
          ->write($path);

        self::assertFileExists($path);
        self::assertEquals(9, QPDF::createInstance()->getNumberOfPages($path));
    }

    /**
     * @throws FileNotFoundException
     */
    public function testPages(): void
    {
        $path = sys_get_temp_dir() .'/output.pdf';

        QPDF::createInstance()
            ->addPages('tests/files/source-1.pdf', '1-3')
            ->addPages('tests/files/source-2.pdf', '4,5')
            ->write($path);

        self::assertFileExists($path);
        self::assertEquals(5, QPDF::createInstance()->getNumberOfPages($path));
    }

    /**
     * @throws FileNotFoundException
     */
    public function testOutput(): void
    {
        $output = QPDF::createInstance()
            ->addPages('tests/files/source-1.pdf', '1')
            ->addPages('tests/files/source-2.pdf', '2')
            ->output();

        // Check if the out is larger than 10 KB
        self::assertGreaterThan(1024 * 10, strlen($output));
    }
}
