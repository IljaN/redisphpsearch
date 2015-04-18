<?php
/*
 * This file is part of the RedisPhpSearch Library
 *
 * (c) Ilja Neumann <https://github.com/IljaN>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace IljaN\RedisPhpSearch\ClientWrapper;

use IljaN\RedisPhpSearch\ClientInterface;

/**
 * Class PhpRedis
 * @package IljaN\RedisPhpSearch\ClientWrapper
 *
 * Wrapper around phpredis. If you use a different client
 * you need to create a wrapper around it which implements ClientInterface
 *
 * @see https://github.com/phpredis/phpredis
 *
 */
class PhpRedis implements ClientInterface
{
    /**
     * @var \Redis
     */
    private $client;

    /**
     * @param \Redis $connectedClient
     * @throws \Exception
     */
    public function __construct(\Redis $connectedClient)
    {
        if (!$connectedClient->isConnected()) {
            throw new \Exception('Client must be connected prior injection');
        }

        $this->client = $connectedClient;
    }


    /**
     * @param $key
     * @param $value
     * @return int
     */
    public function sAdd($key, $value)
    {
        return $this->client->sAdd($key, $value);
    }

    /**
     * @param array $keys
     * @return array
     */
    public function sInter($keys = array())
    {
        return call_user_func_array(
            array($this->client, 'sInter'),
            $keys
        );
    }

    public function close()
    {
        $this->client->close();
    }

    public function getClient()
    {
        return $this->client;
    }

}