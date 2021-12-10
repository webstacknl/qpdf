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
    private ?string $source = null;

    /**
     * @var array<int, string> $pages
     */
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

    /**
     * @throws FileNotFoundException
     */
    public function source(string $source): QPDF
    {
        if (false === file_exists($source)) {
            throw new FileNotFoundException($source);
        }

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
        if (false === file_exists($file)) {
            throw new FileNotFoundException($file);
        }

        $this->pages[] = $file . ($pages ? ' ' . $pages : null);

        return $this;
    }

    /**
     * Runs the command and write the result to the given path
     */
    public function write(string $path): void
    {
        $this->run($path);
    }

    /**
     * Runs the command and returns the result as a string
     */
    public function output(): string
    {
        return $this->run();
    }

    private function run(?string $path = null): string
    {
        $command = $this->buildCommand($path);

        $script = sprintf('%s/qpdf-%s.sh', sys_get_temp_dir(), md5($command));

        // Create a tmp file with the generated command because very long commands will fail otherwise
        file_put_contents($script, '#!/bin/bash' . PHP_EOL . $command);
        chmod($script, 0744);

        $process = Process::fromShellCommandline($script);
        $process->setTimeout($this->timeout);
        $process->setIdleTimeout(null);
        $process->mustRun();

        // Cleanup the tmp file
        @unlink($script);

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
