<?php

namespace ECSPrefix20210509;

use PHP_CodeSniffer\Fixer;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Differ\UnifiedDiffer;
use PhpCsFixer\WhitespacesFixerConfig;
use ECSPrefix20210509\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix20210509\Symfony\Component\Console\Terminal;
use ECSPrefix20210509\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Bootstrap\NoCheckersLoaderReporter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyleFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\FixerRunner\WhitespacesFixerConfigFactory;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\SmartFileSystem\FileSystemFilter;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileSystem;
use function ECSPrefix20210509\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\ECSPrefix20210509\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->import(__DIR__ . '/services/services_cache.php');
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('Symplify\\EasyCodingStandard\\', __DIR__ . '/../src')->exclude([
        // onyl for "bin/ecs" file, where containre does not exist yet
        __DIR__ . '/../src/Bundle',
        __DIR__ . '/../src/Bootstrap',
        __DIR__ . '/../src/DependencyInjection',
        __DIR__ . '/../src/HttpKernel',
        __DIR__ . '/../src/Exception',
        __DIR__ . '/../src/ValueObject',
    ]);
    $services->set(\ECSPrefix20210509\Symfony\Component\Console\Terminal::class);
    $services->set(\Symplify\SmartFileSystem\FileSystemGuard::class);
    $services->set(\Symplify\SmartFileSystem\Finder\FinderSanitizer::class);
    $services->set(\Symplify\SmartFileSystem\SmartFileSystem::class);
    $services->set(\Symplify\SmartFileSystem\Finder\SmartFinder::class);
    $services->set(\Symplify\SmartFileSystem\FileSystemFilter::class);
    $services->set(\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class);
    $services->set(\ECSPrefix20210509\Symfony\Component\Console\Style\SymfonyStyle::class)->factory([\ECSPrefix20210509\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class), 'create']);
    $services->set(\Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle::class)->factory([\ECSPrefix20210509\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyleFactory::class), 'create']);
    $services->set(\PhpCsFixer\WhitespacesFixerConfig::class)->factory([\ECSPrefix20210509\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCodingStandard\FixerRunner\WhitespacesFixerConfigFactory::class), 'create']);
    $services->set(\Symplify\EasyCodingStandard\Bootstrap\NoCheckersLoaderReporter::class);
    // code sniffer
    $services->set(\PHP_CodeSniffer\Fixer::class);
    // fixer
    $services->set(\PhpCsFixer\Differ\UnifiedDiffer::class);
    $services->alias(\PhpCsFixer\Differ\DifferInterface::class, \PhpCsFixer\Differ\UnifiedDiffer::class);
    $services->set(\Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor::class);
};
