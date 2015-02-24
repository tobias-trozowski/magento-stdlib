<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoTest\Framework\Stdlib;

use Magento\Framework\Stdlib\StringUtils;

/**
 * @coversDefaultClass \Magento\Framework\Stdlib\StringUtils
 */
class StringUtilsTest extends \PHPUnit_Framework_TestCase
{

    public function splitDataProvider()
    {

        return [
            'empty string'                      => ['',                        1, false,   false,  []],
            'no whitespaces'                    => ['1234',                    1, false,   false,  ['1','2','3','4']],
            'whitespace at beginning and end'   => [' 1234 ',                  1, false,   false,  [' ','1','2','3','4',' ']],
            'whitespace in the middle'          => ['12  34',                 1, false,   true,   ['1','2',' ','3','4']],
            'several whitespaces'               => ['12345  123    123456789', 5, true,    true,   ['12345','123','12345','6789']],
        ];
    }

    public function cleanDataProvider()
    {
        return [
            ['Iñtërnâtiônàlizætiøn', 'Iñtërnâtiônàlizætiøn'],
        ];
    }

    /**
     * @dataProvider splitDataProvider
     * @covers ::split
     */
    public function testSplit($value, $length, $keepWords, $trim, $expected)
    {
        $actual = StringUtils::split($value, $length, $keepWords, $trim);
        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::splitInjection
     */
    public function testSplitInjection()
    {
        $string = '123456789';
        $this->assertEquals('1234 5678 9', StringUtils::splitInjection($string, 4));
        $this->assertEquals('123 456 789', StringUtils::splitInjection($string, 3));
        $this->assertEquals('1 2 3 4 5 6 7 8 9', StringUtils::splitInjection($string, 1));
    }

    /**
     * @dataProvider cleanDataProvider
     * @covers ::cleanString
     */
    public function testCleanString($string, $expected)
    {
        $actual = StringUtils::cleanString($string);
        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::strlen
     */
    public function testStrlen()
    {
        $this->assertSame(iconv_strlen('teststring', 'UTF-8'), StringUtils::strlen('teststring'));
    }

    /**
     * @covers ::strrev
     */
    public function testStrrev()
    {
        $this->assertSame('', StringUtils::strrev(''));
        $this->assertSame('654321', StringUtils::strrev('123456'));
    }

    /**
     * @covers ::strpos
     */
    public function testStrpos()
    {
        $this->assertEquals(1, StringUtils::strpos('123', 2));
    }

    /**
     * @covers ::substr
     */
    public function testSubstr()
    {
        $this->assertSame('456', StringUtils::substr('123 456', 4));
        $this->assertSame('4', StringUtils::substr('123 456', 4, 1));
    }

    public function upperCaseWordsDataProvider()
    {

        return [
            ['test test2',                      '_',    '_',    'Test_Test2'],
            ['test_test2 test3',                '_',    '_',    'Test_Test2_Test3'],
            ['test test2_test3\test4|test5',    '|',    '\\',   'Test\Test2_test3\test4\Test5'],
        ];
    }

    /**
     * @dataProvider upperCaseWordsDataProvider
     * @covers ::upperCaseWords
     */
    public function testUpperCaseWords($testString, $sourceSeparator, $destinationSeparator, $expected)
    {
        $actual = StringUtils::upperCaseWords($testString, $sourceSeparator, $destinationSeparator);
        $this->assertSame($expected, $actual);
    }
}
