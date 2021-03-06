<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Stdlib;

/**
 * Magento methods to work with string.
 *
 * Declared abstract, as we have no need for instantiation.
 * @todo shall we use abstract classes or add private constructors to prevent instantiation?
 */
abstract class StringUtils
{
    /**
     * Default charset
     */
    const ICONV_CHARSET = 'UTF-8';

    /**
     * Capitalize first letters and convert separators if needed
     *
     * @param string $str
     * @param string $sourceSeparator
     * @param string $destinationSeparator
     *
     * @return string
     */
    public static function upperCaseWords($str, $sourceSeparator = '_', $destinationSeparator = '_')
    {
        return str_replace(' ', $destinationSeparator, ucwords(str_replace($sourceSeparator, ' ', $str)));
    }

    /**
     * Split string and appending $insert string after $needle
     *
     * @param string  $str
     * @param integer $length
     * @param string  $needle
     * @param string  $insert
     *
     * @return string
     */
    public static function splitInjection($str, $length = 50, $needle = '-', $insert = ' ')
    {
        $str = self::split($str, $length);
        $newStr = '';
        foreach ($str as $part) {
            if (self::strlen($part) >= $length) {
                $lastDelimiter = self::strpos(self::strrev($part), $needle);
                $tmpNewStr = self::substr(self::strrev($part), 0,
                        $lastDelimiter) . $insert . self::substr(self::strrev($part), $lastDelimiter);
                $newStr .= self::strrev($tmpNewStr);
            } else {
                $newStr .= $part;
            }
        }
        return trim($newStr);
    }

    /**
     * Binary-safe variant of strSplit()
     * + option not to break words
     * + option to trim spaces (between each word)
     * + option to set character(s) (pcre pattern) to be considered as words separator
     *
     * @param string $value
     * @param int    $length
     * @param bool   $keepWords
     * @param bool   $trim
     * @param string $wordSeparatorRegex
     *
     * @return string[]
     */
    public static function split($value, $length = 1, $keepWords = false, $trim = false, $wordSeparatorRegex = '\s')
    {
        $result = [];
        if ($trim) {
            $value = trim(preg_replace('/\s{2,}/siu', ' ', $value));
        }
        $strLen = self::strlen($value);
        if (!$strLen || !is_int($length) || $length <= 0) {
            return $result;
        }
        // do a usual str_split, but safe for our encoding
        if (!$keepWords || $length < 2) {
            for ($offset = 0; $offset < $strLen; $offset += $length) {
                $result[] = self::substr($value, $offset, $length);
            }
        } else {
            // split smartly, keeping words
            $split = preg_split('/(' . $wordSeparatorRegex . '+)/siu', $value, null, PREG_SPLIT_DELIM_CAPTURE);
            $index = 0;
            $space = '';
            $spaceLen = 0;
            foreach ($split as $key => $part) {
                if ($trim) {
                    // ignore spaces (even keys)
                    if ($key % 2) {
                        continue;
                    }
                    $space = ' ';
                    $spaceLen = 1;
                }
                if (empty($result[$index])) {
                    $currentLength = 0;
                    $result[$index] = '';
                    $space = '';
                    $spaceLen = 0;
                } else {
                    $currentLength = self::strlen($result[$index]);
                }
                $partLength = self::strlen($part);
                // add part to current last element
                if ($currentLength + $spaceLen + $partLength <= $length) {
                    $result[$index] .= $space . $part;
                } elseif ($partLength <= $length) {
                    // add part to new element
                    // @todo: check if "$index++;$result[$index]=$part;" is faster
                    $result[++$index] = $part;
                } else {
                    // break too long part recursively
                    foreach (self::split($part, $length, false, $trim, $wordSeparatorRegex) as $subPart) {
                        // @todo: check if "$index++;$result[$index]=$subPart;" is faster
                        $result[++$index] = $subPart;
                    }
                }
            }
        }
        // remove last element, if empty
        $count = count($result);
        if ($count) {
            if ($result[$count - 1] === '') {
                unset($result[$count - 1]);
            }
        }
        // remove first element, if empty
        if (isset($result[0]) && $result[0] === '') {
            array_shift($result);
        }
        return $result;
    }

    /**
     * Retrieve string length using default charset
     *
     * @param string $string
     *
     * @return int
     */
    public static function strlen($string)
    {
        return iconv_strlen($string, self::ICONV_CHARSET);
    }

    /**
     * Clean non UTF-8 characters
     *
     * @param string $string
     *
     * @return string
     */
    public static function cleanString($string)
    {
        if ('"libiconv"' == ICONV_IMPL) {
            return iconv(self::ICONV_CHARSET, self::ICONV_CHARSET . '//IGNORE', $string);
        } else {
            return $string;
        }
    }

    /**
     * Pass through to iconv_substr()
     *
     * @param string $string
     * @param int    $offset
     * @param int    $length
     *
     * @return string
     */
    public static function substr($string, $offset, $length = null)
    {
        $string = self::cleanString($string);
        if (is_null($length)) {
            $length = self::strlen($string) - $offset;
        }
        return iconv_substr($string, $offset, $length, self::ICONV_CHARSET);
    }

    /**
     * Binary-safe strrev()
     *
     * @param string $str
     *
     * @return string
     */
    public static function strrev($str)
    {
        $result = '';
        $strLen = self::strlen($str);
        if (!$strLen) {
            return $result;
        }
        for ($i = $strLen - 1; $i >= 0; $i--) {
            $result .= self::substr($str, $i, 1);
        }
        return $result;
    }

    /**
     * Find position of first occurrence of a string
     *
     * @param string $haystack
     * @param string $needle
     * @param int    $offset
     *
     * @return int|bool
     */
    public static function strpos($haystack, $needle, $offset = null)
    {
        return iconv_strpos($haystack, $needle, $offset, self::ICONV_CHARSET);
    }
}
