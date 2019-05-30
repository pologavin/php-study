<?php
/**
 * @link https://leetcode.com/problems/word-search-ii/
 * 查找单词
 * Created by PhpStorm.
 * User: gavin
 * Date: 2019/5/28
 * Time: 9:57 PM
 */
class Words
{
    public function main(array $board, array $words)
    {

    }
}

$board = [
    ['o','a','a','n'],
    ['e','t','a','e'],
    ['i','h','k','r'],
    ['i','f','l','v']
];
$words = ["oath","pea","eat","rain"];

$res = (new Words())->main($board, $words);

var_dump($res);