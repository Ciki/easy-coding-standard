<?php

declare (strict_types=1);
namespace ECSPrefix20210811\Symplify\ConsoleColorDiff\Bundle;

use ECSPrefix20210811\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210811\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension;
final class ConsoleColorDiffBundle extends \ECSPrefix20210811\Symfony\Component\HttpKernel\Bundle\Bundle
{
    protected function createContainerExtension() : ?\ECSPrefix20210811\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \ECSPrefix20210811\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension();
    }
}
