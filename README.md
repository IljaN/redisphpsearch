# RedisPhpSearch Library

[![Build Status](https://travis-ci.org/IljaN/redisphpsearch.svg?branch=master)](https://travis-ci.org/IljaN/redisphpsearch)
[![Coverage Status](https://coveralls.io/repos/IljaN/redisphpsearch/badge.svg?branch=master)](https://coveralls.io/r/IljaN/redisphpsearch?branch=master)

**IMPORTANT: This project is under heavy development, everything is subject to change!**

## About
A lightweight PHP library which provides fulltext search capability
using [redis](http://redis.io) as index server.

## Overview

### Indexing
A index is created by taking a text and an id. First the text is processed by a tokenizer function which
splits the text in to multiple tokens (words).

```php
// For string "Hello World!"
array('Hello','World'); 
// For string "Goodbye World!"
array('Goodbye','World'); 
```
After tokenizing a redis set is created out of each token ([SADD](http://redis.io/commands/sadd)) whereby the token is the redis key and the set members are the ids. 

An id can be anything, for example a database primary key in a relational database or some other entity id
which identifies the full text containing the token.

```php
use IljaN\RedisPhpSearch\ClientWrapper\PhpRedis;
use IljaN\RedisPhpSearch\Tokenizer\SimpleTokenizer;
use IljaN\RedisPhpSearch\Indexer;

$redis = new \Redis();
$redis->connect('127.0.0.1');
$redisClientWrapper = new PhpRedis($redis); // Wraps original client for portability

$indexer = new Indexer($redisClientWrapper, new SimpleTokenizer());
$indexer->index('Hello World!', 1);
$indexer->index('Goodbye World!', 2);
```
Translates to following redis commands:
```bash
127.0.0.1:6379> SADD Hello 1
127.0.0.1:6379> SADD World! 1
127.0.0.1:6379> SADD Goodbye 2
127.0.0.1:6379> SADD World! 2
```

### Searching

Searching is done by intersecting multiple sets ([SINTER](http://redis.io/commands/sinter)) thus only getting the id`s which contain every token (word).
```php
use IljaN\RedisPhpSearch\ClientWrapper\PhpRedis;
use IljaN\RedisPhpSearch\Tokenizer\SimpleTokenizer;
use IljaN\RedisPhpSearch\Search;

// Setup ommited... (see above)

$search = new Search($redisClientWrapper);
$search->search("Hello");   // array('1')
$search->search("World");   // array('1','2')
$search->search("Goodbye"); // array('2')
```
Translates to following redis commands:

```bash
127.0.0.1:6379> SINTER Hello
1) "1"
127.0.0.1:6379> SINTER World
1) "1"
2) "2"
127.0.0.1:6379> SINTER Goodbye
1) "2"
127.0.0.1:6379> 
```
## Usage

wip

### Setup

wip

### Tokenizers

wip

### Transformers

wip

### Tests
```bash
$cd redisphpsearch
$phpunit
```

### Todo

wip

















