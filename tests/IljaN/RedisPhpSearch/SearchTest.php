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
    const TEST_SEARCH_EXPECTED_RESULT = array('result1', 'result2');

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
    private $filterMock;

    public function setUp()
    {
        $this->clientMock = $this->getMockBuilder('IljaN\RedisPhpSearch\ClientInterface')
            ->setMethods(array('sInter', 'sAdd', 'close', 'getClient'))
            ->getMock();

        $this->filterMock = $this->getMockBuilder('IljaN\RedisPhpSearch\FilterInterface')
            ->setMethods(array('filter'))
            ->getMock();

        $this->sut = new \IljaN\RedisPhpSearch\Search($this->clientMock);
    }

    public function testSearchWithoutFilters()
    {
        $this->clientMock->expects($this->once())
            ->method('sInter')
            ->with($this->equalTo(array('foo', 'bar', 'baz')))
            ->will($this->returnValue(self::TEST_SEARCH_EXPECTED_RESULT));

        $result = $this->sut->search(self::TEST_SEARCH_TERM);

        $this->assertEquals(self::TEST_SEARCH_EXPECTED_RESULT, $result);
    }


    public function testSearchCallsSearchTermFilter()
    {
        $this->filterMock->expects($this->once())
            ->method('filter')
            ->will($this->returnValue(array()));

        $this->sut->setSearchTermFilter($this->filterMock);
        $this->sut->search('');
    }

    public function testSearchCallsResultFilter()
    {
        $this->filterMock->expects($this->once())
            ->method('filter')
            ->will($this->returnValue(array()));

        $this->clientMock->expects($this->any())
            ->method('sInter')
            ->will($this->returnValue(array()));

        $this->sut->setResultFilter($this->filterMock);
        $this->sut->search('');
    }


    /**
     * @depends testSearchCallsSearchTermFilter
     */
    public function testSearchTermFilterReceivesSearchTerm()
    {
        $this->filterMock->expects($this->any())
            ->method('filter')
            ->with(array(self::TEST_SEARCH_TERM))
            ->will($this->returnValue(array()));

        $this->sut->setSearchTermFilter($this->filterMock);
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

        $this->filterMock->expects($this->any())
            ->method('filter')
            ->will($this->returnValue($redisResult));

        $this->sut->setResultFilter($this->filterMock);

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

        $this->filterMock->expects($this->any())
            ->method('filter')
            ->will($this->returnValue($redisResult));

        $this->sut->setResultFilter($this->filterMock);
        $this->sut->search('');

        $searchResult = $this->sut->search('');

        $this->assertEquals($redisResult, $searchResult, 'Did not return output filter result');
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

        $this->filterMock->expects($this->any())
            ->method('filter')
            ->will($this->returnValue(array($inputFilterResult)));

        $this->sut->setSearchTermFilter($this->filterMock);
        $this->sut->search('');

    }

    public function testSearchRedisReceivesExplodedTermWithoutSearchTermFilter()
    {
        $searchTerm = "hello world foo bar";
        $expectedRedisInput = array('hello', 'world', 'foo', 'bar');

        $this->clientMock->expects($this->once())
            ->method('sInter')
            ->with($expectedRedisInput);

        $this->sut->search($searchTerm);
    }


    /**
     * @expectedException RuntimeException
     */
    public function testExceptionIfSearchTermFilterDoesNotReturnArray()
    {
        $this->filterMock->expects($this->any())
            ->method('filter')
            ->will($this->returnValue('string'));

        $this->sut->setSearchTermFilter($this->filterMock);
        $this->sut->search('');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testExceptionIfResultFilterDoesNotReturnArray()
    {
        $this->filterMock->expects($this->any())
            ->method('filter')
            ->will($this->returnValue('string'));

        $this->clientMock->expects($this->any())
            ->method('sInter')
            ->will($this->returnValue(array()));

        $this->sut->setResultFilter($this->filterMock);
        $this->sut->search('');
    }

    public function testGettersSetters()
    {
        $mock = $this->getMockBuilder('IljaN\RedisPhpSearch\ClientInterface')
            ->getMock();

        $this->sut->setClient($mock);
        $result = $this->sut->getClient();

        $this->assertEquals($mock, $result);

        $mock = $this->getMockBuilder('IljaN\RedisPhpSearch\FilterInterface')
            ->getMock();

        $this->sut->setSearchTermFilter($mock);
        $result = $this->sut->getSearchTermFilter();

        $this->assertEquals($mock, $result);


        $this->sut->setResultFilter($mock);
        $result = $this->sut->getResultFilter();

        $this->assertEquals($mock, $result);
    }
}