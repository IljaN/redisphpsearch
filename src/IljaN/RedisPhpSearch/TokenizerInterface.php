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
 * Interface TokenizerInterface
 * @package IljaN\RedisPhpSearch
 *
 * Must be implemented by the tokenizer class which is used by the indexer.
 *
 */
interface TokenizerInterface
{
    /**
     * @param string $input
     * @return array
     */
    public function tokenize($input);
}