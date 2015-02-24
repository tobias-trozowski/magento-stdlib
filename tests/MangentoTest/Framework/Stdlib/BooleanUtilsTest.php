<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoTest\Framework\Stdlib;

use Magento\Framework\Stdlib\BooleanUtils;

/**
 * @coversDefaultClass \Magento\Framework\Stdlib\BooleanUtils
 */
class BooleanUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BooleanUtils
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new BooleanUtils();
    }

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        // array $trueValues = array(true, 1, 'true', '1'), array $falseValues = array(false, 0, 'false', '0')
        $bu = new BooleanUtils();

        $this->assertTrue($bu->toBoolean(true));
        $this->assertTrue($bu->toBoolean(1));
        $this->assertTrue($bu->toBoolean('true'));
        $this->assertTrue($bu->toBoolean('1'));

        $this->assertFalse($bu->toBoolean(false));
        $this->assertFalse($bu->toBoolean(0));
        $this->assertFalse($bu->toBoolean('false'));
        $this->assertFalse($bu->toBoolean('0'));

        $bu = new BooleanUtils(['foo'], ['bar']);

        $this->assertTrue($bu->toBoolean('foo'));
        $this->assertFalse($bu->toBoolean('bar'));
    }

    /**
     * @covers ::toBoolean
     * @dataProvider toBooleanDataProvider
     */
    public function testToBoolean($input, $expected, $exceptionName = null, $exceptionMessage = '')
    {
        $this->setExpectedException($exceptionName, $exceptionMessage);
        $actual = $this->object->toBoolean($input);
        $this->assertSame($expected, $actual);
    }

    public function toBooleanDataProvider()
    {
        // @formatter:off
        return [
            'boolean "true"'            => [true,       true],
            'boolean "false"'           => [false,      false],
            'boolean string "true"'     => ['true',     true],
            'boolean string "false"'    => ['false',    false],
            'boolean numeric "1"'       => [1,          true],
            'boolean numeric "0"'       => [0,          false],
            'boolean string "1"'        => ['1',        true],
            'boolean string "0"'        => ['0',        false],
            // causing exceptions
            'boolean string "on"'       => ['on',       false, 'InvalidArgumentException', 'Boolean value is expected, supported values: '],
            'boolean string "off"'      => ['off',      false, 'InvalidArgumentException', 'Boolean value is expected, supported values: '],
            'boolean string "yes"'      => ['yes',      false, 'InvalidArgumentException', 'Boolean value is expected, supported values: '],
            'boolean string "no"'       => ['no',       false, 'InvalidArgumentException', 'Boolean value is expected, supported values: '],
            'boolean string "TRUE"'     => ['TRUE',     false, 'InvalidArgumentException', 'Boolean value is expected, supported values: '],
            'boolean string "FALSE"'    => ['FALSE',    false, 'InvalidArgumentException', 'Boolean value is expected, supported values: '],
            'empty string'              => ['',         false, 'InvalidArgumentException', 'Boolean value is expected, supported values: '],
            'null'                      => [null,       false, 'InvalidArgumentException', 'Boolean value is expected, supported values: '],
        ];
        // @formatter:on
    }
}
