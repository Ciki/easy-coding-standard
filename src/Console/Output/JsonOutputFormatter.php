<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Output;

use ECSPrefix20210723\Nette\Utils\Json;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
use ECSPrefix20210723\Symplify\PackageBuilder\Console\ShellCode;
/**
 * @see \Symplify\EasyCodingStandard\Tests\Console\Output\JsonOutputFormatterTest
 */
final class JsonOutputFormatter implements \Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface
{
    /**
     * @var string
     */
    const NAME = 'json';
    /**
     * @var string
     */
    const FILES = 'files';
    /**
     * @var \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;
    public function __construct(\Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle)
    {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }
    /**
     * @param \Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult $errorAndDiffResult
     * @param \Symplify\EasyCodingStandard\ValueObject\Configuration $configuration
     */
    public function report($errorAndDiffResult, $configuration) : int
    {
        $json = $this->createJsonContent($errorAndDiffResult);
        $this->easyCodingStandardStyle->writeln($json);
        $errorCount = $errorAndDiffResult->getErrorCount();
        return $errorCount === 0 ? \ECSPrefix20210723\Symplify\PackageBuilder\Console\ShellCode::SUCCESS : \ECSPrefix20210723\Symplify\PackageBuilder\Console\ShellCode::ERROR;
    }
    public function getName() : string
    {
        return self::NAME;
    }
    /**
     * @param \Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult $errorAndDiffResult
     */
    public function createJsonContent($errorAndDiffResult) : string
    {
        $errorsArray = $this->createBaseErrorsArray($errorAndDiffResult);
        $codingStandardErrors = $errorAndDiffResult->getErrors();
        foreach ($codingStandardErrors as $codingStandardError) {
            $errorsArray[self::FILES][$codingStandardError->getRelativeFilePath()]['errors'][] = ['line' => $codingStandardError->getLine(), 'file_path' => $codingStandardError->getRelativeFilePath(), 'message' => $codingStandardError->getMessage(), 'source_class' => $codingStandardError->getCheckerClass()];
        }
        $fileDiffs = $errorAndDiffResult->getFileDiffs();
        foreach ($fileDiffs as $fileDiff) {
            $errorsArray[self::FILES][$fileDiff->getRelativeFilePath()]['diffs'][] = ['diff' => $fileDiff->getDiff(), 'applied_checkers' => $fileDiff->getAppliedCheckers()];
        }
        return \ECSPrefix20210723\Nette\Utils\Json::encode($errorsArray, \ECSPrefix20210723\Nette\Utils\Json::PRETTY);
    }
    /**
     * @return mixed[]
     */
    private function createBaseErrorsArray(\Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult $errorAndDiffResult) : array
    {
        return ['totals' => ['errors' => $errorAndDiffResult->getErrorCount(), 'diffs' => $errorAndDiffResult->getFileDiffsCount()], self::FILES => []];
    }
}
