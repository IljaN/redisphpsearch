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
     * @var TransformerInterface
     */
    private $searchTermTransformer;

    /**
     * @var TransformerInterface
     */
    private $resultTransformer;

    /**
     * @param \IljaN\RedisPhpSearch\ClientInterface $clientWrapper
     * @param TransformerInterface $searchTermTransformer
     * @param TransformerInterface $resultTransformer
     */
    public function __construct(
        ClientInterface $clientWrapper,
        TransformerInterface $searchTermTransformer,
        TransformerInterface $resultTransformer = null
    ) {
        $this->client = $clientWrapper;
        $this->searchTermTransformer = $searchTermTransformer;
        $this->resultTransformer = $resultTransformer;
    }


    /**
     * @param string $term
     * @return array
     */
    public function search($term)
    {

        $term = $this->searchTermTransformer->transform($term);

        if (!is_array($term)) {
            throw new \RuntimeException(sprintf('Search term transform must return array, %s given.', gettype($term)));
        }

        $result = $this->client->sInter($term);

        if ($this->resultTransformer) {
            $result = $this->resultTransformer->transform($result);

            if (!is_array($result)) {
                throw new \RuntimeException(sprintf('Result transform must return array, %s given.', gettype($result)));
            }
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getSearchTermTransformer()
    {
        return $this->searchTermTransformer;
    }

    /**
     * @param mixed $searchTermTransformer
     */
    public function setSearchTermTransformer(TransformerInterface $searchTermTransformer)
    {
        $this->searchTermTransformer = $searchTermTransformer;
    }

    /**
     * @return mixed
     */
    public function getResultTransformer()
    {
        return $this->resultTransformer;
    }

    /**
     * @param mixed $resultTransformer
     */
    public function setResultTransformer(TransformerInterface $resultTransformer)
    {
        $this->resultTransformer = $resultTransformer;
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