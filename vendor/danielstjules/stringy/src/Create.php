<?php

namespace ECSPrefix20211206\Stringy;

if (!\function_exists('ECSPrefix20211206\\Stringy\\create')) {
    /**
     * Creates a Stringy object and returns it on success.
     *
     * @param  mixed   $str      Value to modify, after being cast to string
     * @param  string  $encoding The character encoding
     * @return Stringy A Stringy object
     * @throws \InvalidArgumentException if an array or object without a
     *         __toString method is passed as the first argument
     */
    function create($str, $encoding = null)
    {
        return new \ECSPrefix20211206\Stringy\Stringy($str, $encoding);
    }
}
