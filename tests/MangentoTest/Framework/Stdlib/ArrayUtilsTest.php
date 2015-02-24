<?php
namespace MagentoTest\Framework\Stdlib;

use Magento\Framework\Stdlib\ArrayUtils;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Magento\Framework\Stdlib\ArrayUtils
 */
class ArrayUtilsTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var \Magento\Framework\Stdlib\ArrayUtils
     */
    protected $arrayUtils;

    protected function setUp()
    {
        $this->arrayUtils = new ArrayUtils();
    }

    /**
     * Data provider for ksortMultibyteDataProvider
     *
     * @todo implement provider with values which different depends on locale
     */
    public function ksortMultibyteDataProvider()
    {
        // @formatter:off
        return [
            [['б' => 2,'в' => 3,'а' => 1],'ru_RU'],
            [[],'ru_RU', false],
        ];
        // @formatter:on
    }

    /**
     * @covers ::ksortMultibyte
     * @dataProvider ksortMultibyteDataProvider
     */
    public function testKsortMultibyte($input, $locale, $expectedResult = null)
    {
        $result = $this->arrayUtils->ksortMultibyte($input, $locale);
        if ($expectedResult !== null) {
            $this->assertSame($expectedResult, $result);
        }
        $iterator = 0;
        foreach ($input as $value) {
            $iterator ++;
            $this->assertEquals($iterator, $value);
        }
    }

    public function toArrayDataProvider()
    {
        $expected = [
            'foo' => 'bar',
            'baz' => 'foo',
            'subfoo' => [
                'foo' => 'baz',
                'bar' => 'foo',
            ],
        ];
        $stdClass = new \stdClass();
        $stdClass->foo = 'bar';
        $stdClass->baz = 'foo';

        $stdClass2 = new \stdClass();
        $stdClass2->foo = 'baz';
        $stdClass2->bar = 'foo';

        $stdClass->subfoo = $stdClass2;
        // @formatter:off
        return [
            [$stdClass, $expected],
        ];
        // @formatter:on
    }

    /**
     * @covers ::toArray
     * @dataProvider toArrayDataProvider
     */
    public function testToArray($data, $expected)
    {
        $this->assertSame($expected, ArrayUtils::toArray($data));
    }
}
