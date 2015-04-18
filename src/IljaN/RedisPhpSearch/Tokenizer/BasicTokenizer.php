<?php
/*
 * This file is part of the PhpRedisSearch Library
 *
 * (c) Ilja Neumann <https://github.com/IljaN>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace IljaN\RedisPhpSearch\Tokenizer;


use IljaN\RedisPhpSearch\TokenizerInterface;

class BasicTokenizer implements TokenizerInterface
{
    const REGEX_WHITESPACE_AND_PUNCTUATION = '/[^a-zA-Z0-9]/';

    /**
     * @param $input
     * @return array
     */
    public function tokenize($input)
    {
        return array_unique(
            preg_split(BasicTokenizer::REGEX_WHITESPACE_AND_PUNCTUATION, $input, 0, PREG_SPLIT_NO_EMPTY)
        );
    }

}