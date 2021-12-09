<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Webstack\QPDF\QPDF;

class VersionTest extends TestCase
{
    public function test(): void
    {
        $output = QPDF::createInstance()->getVersion();

        self::assertStringContainsString('qpdf version', $output);
        self::assertMatchesRegularExpression('|\d.\d.\d|', $output);
    }
}
