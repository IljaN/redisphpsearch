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
     * @param $term
     * @param string $prefix
     * @return array
     * @internal param string $termParts
     */
    public function search($term, $prefix = 'keywords::')
    {

        $termParts = $this->searchTermTransformer->transform($term);

        if (!is_array($termParts)) {
            throw new \RuntimeException(sprintf('Search termParts transform must return array, %s given.',
                gettype($termParts)));
        }

        $prefixedTermParts = array();
        if ($prefix) {
            foreach ($termParts as $termPart) {
                $prefixedTermParts[] = $termPart;
            }
            $termParts = $prefixedTermParts;
        }

        $result = $this->client->sInter($termParts);

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
