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
 * Interface FilterInterface
 * @package IljaN\RedisPhpSearch
 */
interface FilterInterface
{
    /**
     * @param $input
     * @return array
     */
    public function filter(array $input);

}