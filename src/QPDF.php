<?php

namespace Webstack\QPDF;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class QPDF
 *
 * @link http://qpdf.sourceforge.net/files/qpdf-manual.html
 */
class QPDF
{
    /**
     * @var null|string
     */
    private $source;

    /**
     * @var array
     */
    private $pages = [];

    /**
     * @var int|null
     */
    private $timeout = 60;

    /**
     * @param int|null $timeout
     */
    public function setTimeout(?int $timeout): QPDF
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @param string $source
     */
    public function source(string $source): QPDF
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @param string $file
     */
    public function addFile(string $file): QPDF
    {
        $this->addPages($file);
        return $this;
    }

    /**
     * @param string $file
     * @param string|null $pages Example page ranges: 1,3,5-9,15-12: pages 1, 3, 5, 6, 7, 8, 9, 15, 14, 13, and 12 in that order. z-1: all pages in the document in reverse r3-r1: the last three pages of the document. r1-r3: the last three pages of the document in reverse order
     */
    public function addPages(string $file, ?string $pages = null): QPDF
    {
        $this->pages[] = $file . ($pages ? ' ' . $pages : null);
        return $this;
    }

    /**
     * @param string $path
     */
    public function write(string $path): void
    {
        $this->run($path);
    }

    /**
     * @return string
     */
    public function output(): string
    {
        return $this->run();
    }

    /**
     * @param string|null $path
     * @return string
     */
    private function run(?string $path = null): string
    {
        $command = $this->buildCommand($path);

        $process = Process::fromShellCommandline($command);
        $process->setTimeout($this->timeout);
        $process->setIdleTimeout(null);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * @param string|null $path
     * @return string
     */
    private function buildCommand(?string $path = null): string
    {
        return 'qpdf ' . ($this->source ?: '--empty') . ' ' . ($this->pages ? '--pages ' . implode(' ', $this->pages) . ' --' : '') . ' ' . ($path ?: '-');
    }
}
