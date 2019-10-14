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
     * @param string $source
     */
    public function source(string $source): void
    {
        $this->source = $source;
    }

    /**
     * @param string $file
     */
    public function addFile(string $file): void
    {
        $this->addPages($file);
    }

    /**
     * @param string $file
     * @param array|null $pages Example page ranges: 1,3,5-9,15-12: pages 1, 3, 5, 6, 7, 8, 9, 15, 14, 13, and 12 in that order. z-1: all pages in the document in reverse r3-r1: the last three pages of the document. r1-r3: the last three pages of the document in reverse order
     */
    public function addPages(string $file, ?array $pages = null): void
    {
        $this->pages[] = $file . ($pages?' '. $pages:null);
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
    private function buildCommand(?string $path): string
    {
        return 'qpdf '. ($this->source?:'--empty') .' '. ($this->pages?'--pages '. implode(' ', $this->pages) .' --':'') .' '. ($path ?: null);
    }
}
