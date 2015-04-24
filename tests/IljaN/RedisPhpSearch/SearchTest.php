<?php
/*
 * This file is part of the RedisPhpSearch Library
 *
 * (c) Ilja Neumann <https://github.com/IljaN>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class SearchTest extends PHPUnit_Framework_TestCase
{
    const TEST_SEARCH_TERM = 'foo bar baz';

    /**
     * @var \IljaN\RedisPhpSearch\Search
     */
    private $sut;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $clientMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $termTransformerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $resultTransformerMock;

    public function setUp()
    {
        $this->clientMock = $this->getMockBuilder('IljaN\RedisPhpSearch\ClientInterface')
            ->setMethods(array('sInter', 'sAdd', 'close', 'getClient'))
            ->getMock();

        $this->termTransformerMock = $this->getMockBuilder('IljaN\RedisPhpSearch\TransformerInterface')
            ->setMethods(array('transform'))
            ->getMock();

        $this->resultTransformerMock = $this->getMockBuilder('IljaN\RedisPhpSearch\TransformerInterface')
            ->setMethods(array('transform'))
            ->getMock();

        $this->sut = new \IljaN\RedisPhpSearch\Search($this->clientMock, $this->termTransformerMock);
    }


    public function testSearchCallsSearchTermFilter()
    {
        $this->termTransformerMock->expects($this->once())
            ->method('transform')
            ->will($this->returnValue(array('foo', 'bar', 'baz')));

        $this->clientMock->expects($this->any())
            ->method('sInter')
            ->will($this->returnValue(array()));


        $this->sut->search(self::TEST_SEARCH_TERM);
    }

    public function testSearchCallsResultFilter()
    {
        $this->termTransformerMock->expects($this->once())
            ->method('transform')
            ->will($this->returnValue(array()));

        $this->resultTransformerMock->expects($this->once())
            ->method('transform')
            ->will($this->returnValue(array()));

        $this->clientMock->expects($this->any())
            ->method('sInter')
            ->will($this->returnValue(array()));

        $this->sut->setResultTransformer($this->resultTransformerMock);


        $this->sut->search('');
    }


    /**
     * @depends testSearchCallsSearchTermFilter
     */
    public function testSearchTermFilterReceivesSearchTerm()
    {
        $this->termTransformerMock->expects($this->any())
            ->method('transform')
            ->with(self::TEST_SEARCH_TERM)
            ->will($this->returnValue(array()));

        $this->sut->setSearchTermTransformer($this->termTransformerMock);
        $this->sut->search(self::TEST_SEARCH_TERM);
    }


    /**
     * @depends testSearchCallsResultFilter
     */
    public function testSearchResultFilterReceivesRedisResult()
    {
        $redisResult = array('bla', 'blub');

        $this->clientMock->expects($this->any())
            ->method('sInter')
            ->with($this->anything())
            ->will($this->returnValue(array('foo')));

        $this->termTransformerMock->expects($this->any())
            ->method('transform')
            ->will($this->returnValue($redisResult));

        $this->sut->setResultTransformer($this->termTransformerMock);

        $this->sut->search('');
    }

    /**
     * @depends testSearchCallsSearchTermFilter
     */
    public function testSearchReturnsResultFilterResult()
    {
        $redisResult = array('some result');

        $this->clientMock->expects($this->any())
            ->method('sInter')
            ->with($this->anything())
            ->will($this->returnValue($redisResult));

        $this->termTransformerMock->expects($this->any())
            ->method('transform')
            ->will($this->returnValue($redisResult));

        $this->sut->setResultTransformer($this->termTransformerMock);
        $this->sut->search('');

        $searchResult = $this->sut->search('');

        $this->assertEquals($redisResult, $searchResult, 'Did not return output transform result');
    }



    /**
     * @depends testSearchCallsSearchTermFilter
     */
    public function testSearchRedisReceivesSearchTermFilterResult()
    {
        $inputFilterResult = 'some result';

        $this->clientMock->expects($this->once())
            ->method('sInter')
            ->with(array($inputFilterResult));

        $this->termTransformerMock->expects($this->any())
            ->method('transform')
            ->will($this->returnValue(array($inputFilterResult)));

        $this->sut->setSearchTermTransformer($this->termTransformerMock);
        $this->sut->search('');

    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Search termParts transform must return array, string given.
     */
    public function testExceptionIfSearchTermFilterDoesNotReturnArray()
    {
        $this->termTransformerMock->expects($this->any())
            ->method('transform')
            ->will($this->returnValue('string'));

        $this->sut->setSearchTermTransformer($this->termTransformerMock);
        $this->sut->search('');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Result transform must return array, string given.
     */
    public function testExceptionIfResultFilterDoesNotReturnArray()
    {
        $this->termTransformerMock->expects($this->any())
            ->method('transform')
            ->will($this->returnValue(array('blafuzzle')));

        $this->clientMock->expects($this->any())
            ->method('sInter')
            ->will($this->returnValue(array()));

        $this->resultTransformerMock->expects($this->any())
            ->method('transform')
            ->will($this->returnValue('string'));

        $this->sut->setResultTransformer($this->resultTransformerMock);
        $this->sut->search('');
    }

    public function testGettersSetters()
    {
        $mock = $this->getMockBuilder('IljaN\RedisPhpSearch\ClientInterface')
            ->getMock();

        $this->sut->setClient($mock);
        $result = $this->sut->getClient();

        $this->assertEquals($mock, $result);

        $mock = $this->getMockBuilder('IljaN\RedisPhpSearch\TransformerInterface')
            ->getMock();

        $this->sut->setSearchTermTransformer($mock);
        $result = $this->sut->getSearchTermTransformer();

        $this->assertEquals($mock, $result);


        $this->sut->setResultTransformer($mock);
        $result = $this->sut->getResultTransformer();

        $this->assertEquals($mock, $result);
    }
}
