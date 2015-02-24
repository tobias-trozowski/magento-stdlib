<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Stdlib;

/**
 * Class ArrayUtils
 *
 */
class ArrayUtils
{
    /**
     * Sorts array with multibyte string keys
     *
     * @param  array  $sort
     * @param  string $locale
     *
     * @return array|bool
     */
    public static function ksortMultibyte(array &$sort, $locale)
    {
        if (empty($sort)) {
            return false;
        }
        $oldLocale = setlocale(LC_COLLATE, "0");
        // use fallback locale if $localeCode is not available
        if (strpos($locale, '.UTF8') === false) {
            $locale .= '.UTF8';
        }
        setlocale(LC_COLLATE, $locale, 'C.UTF-8', 'en_US.utf8');
        ksort($sort, SORT_LOCALE_STRING);
        setlocale(LC_COLLATE, $oldLocale);
        return $sort;
    }

    /**
     *
     * @param object|array|\Traversable $arg0
     * @return object|array|\Traversable
     */
    public static function toArray($arg0)
    {
        if (! is_object($arg0) && ! is_array($arg0) && ! $arg0 instanceof \Traversable) {
            return $arg0;
        }

        $array = [];
        foreach ($arg0 as $key => $v) {
            $value = $v;
            $array[$key] = static::toArray($v);
        }
        return $array;
    }
}
