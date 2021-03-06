<?php
/*
 * This file is part of the RedisPhpSearch Library
 *
 * (c) Ilja Neumann <https://github.com/IljaN>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace IljaN\RedisPhpSearch\Tokenizer;


use IljaN\RedisPhpSearch\TokenizerInterface;

class SimpleTokenizer implements TokenizerInterface
{
    const REGEX_WHITESPACE_AND_PUNCTUATION = '/[^a-zA-Z0-9]/';

    /**
     * @param $input
     * @return array
     */
    public function tokenize($input)
    {
        $result = array_unique(
            preg_split(SimpleTokenizer::REGEX_WHITESPACE_AND_PUNCTUATION, $input, 0, PREG_SPLIT_NO_EMPTY)
        );

       return array_map('strtolower', $result);
    }
}
