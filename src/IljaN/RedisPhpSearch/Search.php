<?php

/*
 * This file is part of the RedisPhpSearch Library
 *
 * (c) Ilja Neumann <https://github.com/IljaN>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IljaN\RedisPhpSearch;

/**
 * Class Search
 * @package IljaN\RedisPhpSearch
 */
class Search
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var FilterInterface
     */
    private $searchTermFilter;

    /**
     * @var FilterInterface
     */
    private $resultFilter;

    /**
     * @param \IljaN\RedisPhpSearch\ClientInterface $clientWrapper
     * @param FilterInterface $searchTermFilter
     * @param FilterInterface $resultFilter
     */
    public function __construct(
        ClientInterface $clientWrapper,
        FilterInterface $searchTermFilter = null,
        FilterInterface $resultFilter = null
    ) {
        $this->client = $clientWrapper;
        $this->searchTermFilter = $searchTermFilter;
        $this->resultFilter = $resultFilter;
    }


    /**
     * @param string $term
     * @return array
     */
    public function search($term)
    {
        if ($this->searchTermFilter) {
            $term = $this->searchTermFilter->filter(array($term));

            if (!is_array($term)) {
                throw new \RuntimeException(sprintf('Search term filter must return array, %s given.', gettype($term)));
            }

        } else {
            $term = explode(' ', $term);
        }

        $result = $this->client->sInter($term);

        if ($this->resultFilter) {
            $result = $this->resultFilter->filter($result);

            if (!is_array($result)) {
                throw new \RuntimeException(sprintf('Result filter must return array, %s given.', gettype($result)));
            }
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getSearchTermFilter()
    {
        return $this->searchTermFilter;
    }

    /**
     * @param mixed $searchTermFilter
     */
    public function setSearchTermFilter(FilterInterface $searchTermFilter)
    {
        $this->searchTermFilter = $searchTermFilter;
    }

    /**
     * @return mixed
     */
    public function getResultFilter()
    {
        return $this->resultFilter;
    }

    /**
     * @param mixed $resultFilter
     */
    public function setResultFilter(FilterInterface $resultFilter)
    {
        $this->resultFilter = $resultFilter;
    }

    /**
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }


}