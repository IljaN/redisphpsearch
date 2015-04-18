<?php
/*
 * This file is part of the RedisPhpSearch Library
 *
 * (c) Ilja Neumann <https://github.com/IljaN>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace IljaN\RedisPhpSearch\Transformer\Term;


use IljaN\RedisPhpSearch\TransformerInterface;

/**
 * Class BasicTermTransformer
 * @package IljaN\RedisPhpSearch\Transformer\Term
 *
 * Explodes search string by space and lowercases everything
 * thus making search case insensitive (use in conjunction with BasicTokenizer)
 */
class BasicTermTransformer implements TransformerInterface
{

    /**
     * @param $input
     * @return array
     */
    public function transform($input)
    {
        $termPieces = explode(' ', $input);
        return array_map('strtolower', $termPieces);
    }
}