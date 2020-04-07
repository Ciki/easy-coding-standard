<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Application\DualRunAwareFileProcessorInterface;
use Symplify\EasyCodingStandard\FileSystem\FileFilter;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class EasyCodingStandardApplication
{
    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var SourceFinder
     */
    private $sourceFinder;

    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var FileFilter
     */
    private $fileFilter;

    /**
     * @var SingleFileProcessor
     */
    private $singleFileProcessor;

    /**
     * @var FileProcessorCollector
     */
    private $fileProcessorCollector;

    public function __construct(
        EasyCodingStandardStyle $easyCodingStandardStyle,
        SourceFinder $sourceFinder,
        ChangedFilesDetector $changedFilesDetector,
        Configuration $configuration,
        FileFilter $fileFilter,
        SingleFileProcessor $singleFileProcessor,
        FileProcessorCollector $fileProcessorCollector
    ) {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->sourceFinder = $sourceFinder;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->configuration = $configuration;
        $this->fileFilter = $fileFilter;
        $this->singleFileProcessor = $singleFileProcessor;
        $this->fileProcessorCollector = $fileProcessorCollector;
    }

    public function run(): int
    {
        // 1. find files in sources
        $files = $this->sourceFinder->find($this->configuration->getSources());

        // 2. clear cache
        if ($this->configuration->shouldClearCache() || $this->isDualRunEnabled()) {
            $this->changedFilesDetector->clearCache();
        } else {
            $files = $this->fileFilter->filterOnlyChangedFiles($files);
        }

        // no files found
        if (count($files) === 0) {
            return 0;
        }

        // 3. start progress bar
        if ($this->configuration->shouldShowProgressBar() && ! $this->easyCodingStandardStyle->isVerbose()) {
            $this->easyCodingStandardStyle->progressStart(count($files) * ($this->isDualRunEnabled() ? 2 : 1));
        }

        // 4. process found files by each processors
        $this->processFoundFiles($files);

        // 5. process files with DualRun
        if ($this->isDualRunEnabled()) {
            $this->processFoundFilesSecondRun($files);
        }

        return count($files);
    }

    public function getCheckerCount(): int
    {
        $checkerCount = 0;

        foreach ($this->fileProcessorCollector->getFileProcessors() as $fileProcessor) {
            $checkerCount += count($fileProcessor->getCheckers());
        }

        return $checkerCount;
    }

    private function isDualRunEnabled(): bool
    {
        foreach ($this->fileProcessorCollector->getFileProcessors() as $fileProcessor) {
            if (! $fileProcessor instanceof DualRunAwareFileProcessorInterface) {
                continue;
            }

            if ($fileProcessor->getDualRunCheckers() !== []) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    private function processFoundFiles(array $fileInfos): void
    {
        foreach ($fileInfos as $fileInfo) {
            $this->processFileInfoWithCallable($fileInfo, function (SmartFileInfo $smartFileInfo): void {
                $this->singleFileProcessor->processFileInfo($smartFileInfo);
            });
        }
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    private function processFoundFilesSecondRun(array $fileInfos): void
    {
        foreach ($fileInfos as $fileInfo) {
            $this->processFileInfoWithCallable($fileInfo, function (SmartFileInfo $smartFileInfo): void {
                foreach ($this->fileProcessorCollector->getFileProcessors() as $fileProcessor) {
                    if ($fileProcessor instanceof DualRunAwareFileProcessorInterface) {
                        $fileProcessor->processFileSecondRun($smartFileInfo);
                    }
                }
            });
        }
    }

    private function processFileInfoWithCallable(SmartFileInfo $smartFileInfo, callable $callable): void
    {
        if ($this->easyCodingStandardStyle->isVerbose()) {
            $this->easyCodingStandardStyle->writeln($smartFileInfo->getRealPath());
        }

        $callable($smartFileInfo);

        if (! $this->easyCodingStandardStyle->isVerbose() && $this->configuration->shouldShowProgressBar()) {
            $this->easyCodingStandardStyle->progressAdvance();
        }
    }
}
