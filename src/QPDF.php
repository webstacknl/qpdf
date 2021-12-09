<?php

declare(strict_types=1);

namespace Webstack\QPDF;

use Symfony\Component\Process\Process;
use Webstack\QPDF\Exceptions\FileNotFoundException;

/**
 * @link http://qpdf.sourceforge.net/files/qpdf-manual.html
 */
class QPDF
{
    private ?string $source;

    private array $pages = [];

    private ?int $timeout = 60;

    public static function createInstance(): QPDF
    {
        return new self();
    }

    public function setTimeout(?int $timeout): QPDF
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function source(string $source): QPDF
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @throws FileNotFoundException
     */
    public function addFile(string $file): QPDF
    {
        $this->addPages($file);

        return $this;
    }

    /**
     * Example page ranges: 1,3,5-9,15-12: pages 1, 3, 5, 6, 7, 8, 9, 15, 14, 13, and 12 in that order. z-1: all pages in the document in reverse r3-r1: the last three pages of the document. r1-r3: the last three pages of the document in reverse order
     *
     * @throws FileNotFoundException
     */
    public function addPages(string $file, ?string $pages = null): QPDF
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException($file);
        }

        $this->pages[] = $file . ($pages ? ' ' . $pages : null);

        return $this;
    }

    public function write(string $path): void
    {
        $this->run($path);
    }

    public function output(): string
    {
        return $this->run();
    }

    private function run(?string $path = null): string
    {
        $command = $this->buildCommand($path);

        $process = Process::fromShellCommandline($command);
        $process->setTimeout($this->timeout);
        $process->setIdleTimeout(null);
        $process->mustRun();

        return $process->getOutput();
    }

    private function buildCommand(?string $path = null): string
    {
        return 'qpdf ' . ($this->source ?: '--empty') . ' ' . ($this->pages ? '--pages ' . implode(' ', $this->pages) . ' --' : '') . ' ' . ($path ?: '-');
    }

    public function getVersion(): string
    {
        return Process::fromShellCommandline('qpdf --version')->mustRun()->getOutput();
    }
}
