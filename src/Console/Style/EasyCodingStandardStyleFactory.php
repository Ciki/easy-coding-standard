<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Style;

use ECSPrefix20210610\Symfony\Component\Console\Application;
use ECSPrefix20210610\Symfony\Component\Console\Input\ArgvInput;
use ECSPrefix20210610\Symfony\Component\Console\Output\ConsoleOutput;
use ECSPrefix20210610\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20210610\Symfony\Component\Console\Terminal;
use ECSPrefix20210610\Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
use ECSPrefix20210610\Symplify\PackageBuilder\Reflection\PrivatesCaller;
final class EasyCodingStandardStyleFactory
{
    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;
    /**
     * @var Terminal
     */
    private $terminal;
    public function __construct(\ECSPrefix20210610\Symfony\Component\Console\Terminal $terminal)
    {
        $this->privatesCaller = new \ECSPrefix20210610\Symplify\PackageBuilder\Reflection\PrivatesCaller();
        $this->terminal = $terminal;
    }
    public function create() : \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
    {
        $argvInput = new \ECSPrefix20210610\Symfony\Component\Console\Input\ArgvInput();
        $consoleOutput = new \ECSPrefix20210610\Symfony\Component\Console\Output\ConsoleOutput();
        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        $this->privatesCaller->callPrivateMethod(new \ECSPrefix20210610\Symfony\Component\Console\Application(), 'configureIO', [$argvInput, $consoleOutput]);
        // --debug is called
        if ($argvInput->hasParameterOption('--debug')) {
            $consoleOutput->setVerbosity(\ECSPrefix20210610\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_DEBUG);
        }
        // disable output for tests
        if (\ECSPrefix20210610\Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment::isPHPUnitRun()) {
            $consoleOutput->setVerbosity(\ECSPrefix20210610\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_QUIET);
        }
        return new \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle($argvInput, $consoleOutput, $this->terminal);
    }
}
