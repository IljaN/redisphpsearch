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


use IljaN\RedisPhpSearch\ClientInterface;


class Indexer
{

    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var TokenizerInterface
     */
    private $tokenizer;


    /**
     * @param ClientInterface $clientWrapper
     * @param TokenizerInterface $tokenizer
     */
    public function __construct(ClientInterface $clientWrapper, TokenizerInterface $tokenizer)
    {
        $this->client = $clientWrapper;
        $this->tokenizer = $tokenizer;
    }

    /**
     * @param $text
     * @param $id
     * @param $prefix
     */
    public function index($text, $id, $prefix = 'keywords::')
    {
        $tokens = $this->tokenizer->tokenize($text);

        if (!is_array($tokens)) {
            throw new \RuntimeException(sprintf('Tokenizer must return array, %s given', gettype($tokens)));
        }

        foreach ($tokens as $token) {
            $this->client->sAdd($prefix . $token, $id);
        }
    }

    /**
     * @return TokenizerInterface
     */
    public function getTokenizer()
    {
        return $this->tokenizer;
    }

    /**
     * @param TokenizerInterface $tokenizer
     */
    public function setTokenizer(TokenizerInterface $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * @return \IljaN\RedisPhpSearch\ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param \IljaN\RedisPhpSearch\ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }


}