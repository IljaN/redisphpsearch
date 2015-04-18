<?php
/*
 * This file is part of the PhpRedisSearch Library
 *
 * (c) Ilja Neumann <https://github.com/IljaN>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class BasicTokenizerTest extends PHPUnit_Framework_TestCase {
    /**
     * @var \IljaN\RedisPhpSearch\Tokenizer\BasicTokenizer
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new IljaN\RedisPhpSearch\Tokenizer\BasicTokenizer();
    }

    public function testTokenizeReturnsUniqueValuesAndSplitsOnSpace()
    {
        $result =$this->sut->tokenize("foo bar bar foo");
        $this->assertEquals(array('foo', 'bar'), $result);
    }

    public function testTokenizeSplitsOnNonAlNum()
    {
        $expected = array('foo', 'bar');

        $result =$this->sut->tokenize("foo.bar");
        $this->assertEquals($expected, $result);

        $result = $this->sut->tokenize("foo. bar");
        $this->assertEquals($expected, $result);

        $result = $this->sut->tokenize("foo! bar");
        $this->assertEquals($expected, $result);

        $result = $this->sut->tokenize("foo-bar");
        $this->assertEquals($expected, $result);

        $result = $this->sut->tokenize("  foo-!  bar");
        $this->assertEquals($expected, $result);
    }

    public function testTokenizeKeepsNumbers()
    {
        $expected = array('foo', 'bar12', '42');
        $result =$this->sut->tokenize("foo. bar12-42");
        $this->assertEquals($expected, $result);

    }



}
