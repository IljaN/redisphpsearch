<?php

/*
 * This file is part of the RedisPhpSearch Library
 *
 * (c) Ilja Neumann <https://github.com/IljaN>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class SimpleTermTransformerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \IljaN\RedisPhpSearch\Transformer\Term\SimpleTermTransformer
     */
    private $sut;

    const TEST_INPUT = 'Foo Bar Baz';

    public function setUp()
    {
        $this->sut = new \IljaN\RedisPhpSearch\Transformer\Term\SimpleTermTransformer();
    }

    public function testTransformReturnsArray()
    {
        $result = $this->sut->transform(self::TEST_INPUT);
        $this->assertInternalType('array', $result);

        $result = $this->sut->transform('');
        $this->assertInternalType('array', $result);
    }

    /**
     * @depends testTransformReturnsArray
     */
    public function testTransformExplodesInput()
    {
        $result = $this->sut->transform(self::TEST_INPUT);
        $this->assertCount(3, $result);
    }

    /**
     * @depends testTransformReturnsArray
     */
    public function testTransformLowerCasesInput()
    {
        $expected = array('foo', 'bar', 'baz');
        $result = $this->sut->transform(self::TEST_INPUT);
        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTransformThrowsOnNonStringInput()
    {
        $this->sut->transform(null);
        $this->sut->transform(22);
    }
}
