<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching;

use SplFileInfo;
use Symplify\EasyCodingStandard\FileSystem\StaticRelativeFilePathHelper;
use Webmozart\Assert\Assert;

/**
 * @see \Symplify\EasyCodingStandard\Tests\ChangedFilesDetector\ChangedFilesDetector\ChangedFilesDetectorTest
 */
final class ChangedFilesDetector
{
    /**
     * @var string
     */
    private const CONFIGURATION_HASH_KEY = 'configuration_hash';

    /**
     * @var string
     */
    private const FILE_HASH = 'file_hash';

    public function __construct(
        private readonly FileHashComputer $fileHashComputer,
        private readonly Cache $cache
    ) {
    }

    /**
     * @api For tests
     */
    public function changeConfigurationFile(string $configurationFile): void
    {
        $this->storeConfigurationDataHash($this->fileHashComputer->computeConfig($configurationFile));
    }

    public function addFileInfo(SplFileInfo $fileInfo): void
    {
        $cacheKey = $this->fileInfoToKey($fileInfo);
        $currentValue = $this->fileHashComputer->compute($fileInfo->getRealPath());
        $this->cache->save($cacheKey, self::FILE_HASH, $currentValue);
    }

    public function invalidateFileInfo(SplFileInfo $fileInfo): void
    {
        $cacheKey = $this->fileInfoToKey($fileInfo);
        $this->cache->clean($cacheKey);
    }

    public function hasFileInfoChanged(SplFileInfo $fileInfo): bool
    {
        $newFileHash = $this->fileHashComputer->compute($fileInfo->getRealPath());

        $cacheKey = $this->fileInfoToKey($fileInfo);
        $cachedValue = $this->cache->load($cacheKey, self::FILE_HASH);

        return $newFileHash !== $cachedValue;
    }

    public function clearCache(): void
    {
        // clear cache only for changed files group
        $this->cache->clear();
    }

    /**
     * For cache invalidation
     *
     * @param string[] $configFiles
     * @api
     */
    public function setUsedConfigs(array $configFiles): void
    {
        if ($configFiles === []) {
            return;
        }

        Assert::allString($configFiles);
        Assert::allFile($configFiles);

        // the first config is core to all → if it was changed, just invalidate it
        $firstConfigFile = $configFiles[0];
        $this->storeConfigurationDataHash($this->fileHashComputer->computeConfig($firstConfigFile));
    }

    private function storeConfigurationDataHash(string $configurationHash): void
    {
        $this->invalidateCacheIfConfigurationChanged($configurationHash);
        $this->cache->save(self::CONFIGURATION_HASH_KEY, self::FILE_HASH, $configurationHash);
    }

    private function fileInfoToKey(SplFileInfo $fileInfo): string
    {
        $relativeFilePath = StaticRelativeFilePathHelper::resolveFromCwd($fileInfo->getRealPath());

        return sha1($relativeFilePath);
    }

    private function invalidateCacheIfConfigurationChanged(string $configurationHash): void
    {
        $cachedValue = $this->cache->load(self::CONFIGURATION_HASH_KEY, self::FILE_HASH);
        if ($cachedValue === null) {
            return;
        }

        if ($configurationHash === $cachedValue) {
            return;
        }

        $this->clearCache();
    }
}
