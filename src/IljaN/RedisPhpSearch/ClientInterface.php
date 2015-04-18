<?php
/*
 * This file is part of the PhpRedisSearch Library
 *
 * (c) Ilja Neumann <https://github.com/IljaN>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace IljaN\RedisPhpSearch;

/**
 * Interface for a thin wrapper around a redis client which
 * exposes a small subset of redis commands required for indexing and searching.
 * This allows to use different clients with RedisPhpSearch.
 *
 * Interface ClientInterface
 * @package IljaN\RedisPhpSearch\ClientWrapper
 */
interface ClientInterface
{
    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function sAdd($key, $value);

    /**
     * @param array $keys
     * @return array
     */
    public function sInter($keys = array());

    public function close();

    /**
     *
     * Returns the underlying redis client
     *
     * @return mixed
     */
    public function getClient();
}