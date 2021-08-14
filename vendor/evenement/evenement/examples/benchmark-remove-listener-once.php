<?php

declare (strict_types=1);
namespace ECSPrefix20210814;

/*
 * This file is part of Evenement.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
\ini_set('memory_limit', '512M');
const ITERATIONS = 100000;
use ECSPrefix20210814\Evenement\EventEmitter;
require __DIR__ . '/../vendor/autoload.php';
$emitter = new \ECSPrefix20210814\Evenement\EventEmitter();
$listeners = [];
for ($i = 0; $i < \ITERATIONS; $i++) {
    $listeners[] = function ($a, $b, $c) {
    };
}
$start = \microtime(\true);
foreach ($listeners as $listener) {
    $emitter->once('event', $listener);
}
$time = \microtime(\true) - $start;
echo 'Adding ', \number_format(\ITERATIONS), ' once listeners took: ', \number_format($time, 2), 's', \PHP_EOL;
$start = \microtime(\true);
foreach ($listeners as $listener) {
    $emitter->removeListener('event', $listener);
}
$time = \microtime(\true) - $start;
echo 'Removing ', \number_format(\ITERATIONS), ' once listeners took: ', \number_format($time, 2), 's', \PHP_EOL;
