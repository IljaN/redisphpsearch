<?php
/*
 * This file is part of the RedisPhpSearch Library
 *
 * (c) Ilja Neumann <https://github.com/IljaN>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class IndexerTest extends PHPUnit_Framework_TestCase {

    const TEST_TEXT = "Lorem Ipsum";
    const TEST_KEY = 'key22test';
    const TEST_PREFIX = 'prefix_';

    /**
     * @var \IljaN\RedisPhpSearch\Indexer
     */
    private $sut;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $clientMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $tokenizerMock;

    public function setUp()
    {
        $this->clientMock = $this->getMockBuilder('IljaN\RedisPhpSearch\ClientInterface')
            ->setMethods(array('sInter', 'sAdd', 'close', 'getClient'))
            ->getMock();

        $this->tokenizerMock = $this->getMockBuilder('IljaN\RedisPhpSearch\TokenizerInterface')
            ->setMethods(array('tokenize'))
            ->getMock();

        $this->sut = new \IljaN\RedisPhpSearch\Indexer($this->clientMock, $this->tokenizerMock);
    }

    public function testIndexCallsTokenizerWithText()
    {
        $this->tokenizerMock->expects($this->once())
            ->method('tokenize')
            ->with(self::TEST_TEXT)
            ->will($this->returnValue(array()));

        $this->sut->index(self::TEST_TEXT, '');
    }

    public function testIndexAddsAllTokensReturnedByTokenizer()
    {
        $tokenizerResult = array('foo', 'bar', 'baz', 'qux');
        $tokenCount = count($tokenizerResult);

        $this->tokenizerMock->expects($this->any())
            ->method('tokenize')
            ->will($this->returnValue($tokenizerResult));

        $this->clientMock->expects($this->exactly($tokenCount))
            ->method('sAdd')
            ->withConsecutive(
                array($this->equalTo(self::TEST_PREFIX . 'foo'), $this->equalTo(self::TEST_KEY)),
                array($this->equalTo(self::TEST_PREFIX . 'bar'), $this->equalTo(self::TEST_PREFIX . self::TEST_KEY)),
                array($this->equalTo(self::TEST_PREFIX . 'baz'), $this->equalTo(self::TEST_PREFIX . self::TEST_KEY)),
                array($this->equalTo(self::TEST_PREFIX . 'qux'), $this->equalTo(self::TEST_PREFIX . self::TEST_KEY))
            );

        $this->sut->index(self::TEST_TEXT, self::TEST_KEY, self::TEST_PREFIX);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testTokenizerReturnsNoArray()
    {
        $this->tokenizerMock->expects($this->any())
            ->method('tokenize')
            ->will($this->returnValue('string'));

        $this->sut->index('','');
    }

    public function testGettersSetters()
    {
        $mock = $this->getMockBuilder('IljaN\RedisPhpSearch\ClientInterface')
            ->getMock();

        $this->sut->setClient($mock);
        $result = $this->sut->getClient();

        $this->assertEquals($mock, $result);

        $mock = $this->getMockBuilder('IljaN\RedisPhpSearch\TokenizerInterface')
            ->getMock();

        $this->sut->setTokenizer($mock);
        $result = $this->sut->getTokenizer();

        $this->assertEquals($mock, $result);

    }

}
