<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202303\Symfony\Component\HttpFoundation\Session;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
final class SessionBagProxy implements SessionBagInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionBagInterface
     */
    private $bag;
    /**
     * @var mixed[]
     */
    private $data;
    /**
     * @var int|null
     */
    private $usageIndex;
    /**
     * @var \Closure|null
     */
    private $usageReporter;
    public function __construct(SessionBagInterface $bag, array &$data, ?int &$usageIndex, ?callable $usageReporter)
    {
        $this->bag = $bag;
        $this->data =& $data;
        $this->usageIndex =& $usageIndex;
        $this->usageReporter = null === $usageReporter ? null : \Closure::fromCallable($usageReporter);
    }
    public function getBag() : SessionBagInterface
    {
        ++$this->usageIndex;
        if ($this->usageReporter && 0 <= $this->usageIndex) {
            ($this->usageReporter)();
        }
        return $this->bag;
    }
    public function isEmpty() : bool
    {
        if (!isset($this->data[$this->bag->getStorageKey()])) {
            return \true;
        }
        ++$this->usageIndex;
        if ($this->usageReporter && 0 <= $this->usageIndex) {
            ($this->usageReporter)();
        }
        return empty($this->data[$this->bag->getStorageKey()]);
    }
    public function getName() : string
    {
        return $this->bag->getName();
    }
    public function initialize(array &$array) : void
    {
        ++$this->usageIndex;
        if ($this->usageReporter && 0 <= $this->usageIndex) {
            ($this->usageReporter)();
        }
        $this->data[$this->bag->getStorageKey()] =& $array;
        $this->bag->initialize($array);
    }
    public function getStorageKey() : string
    {
        return $this->bag->getStorageKey();
    }
    /**
     * @return mixed
     */
    public function clear()
    {
        return $this->bag->clear();
    }
}
