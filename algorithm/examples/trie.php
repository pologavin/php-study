<?php

/**
 * @link https://leetcode.com/problems/implement-trie-prefix-tree/
 * 字典树
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/28
 * Time: 下午4:07
 */
class Trie
{

    /**
     * root node
     * @var TrieNode
     */
    private $root = [];

    /**
     * Initialize your data structure here.
     */
    function __construct()
    {
    }

    /**
     * Inserts a word into the trie.
     * @param String $word
     * @return NULL
     */
    function insert($word)
    {
        if (empty($word)) {
            return false;
        }
        $length = mb_strlen($word);
        $node = &$this->root;
        for ($i = 0; $i < $length; $i++) {
            $letter = mb_substr($word, $i, 1);
            if (!isset($node[$letter])) {
                $node[$letter] = [
                    'is_end' => false,
                ];
            }
            $node = &$node[$letter];
        }
        $node['is_end'] = true;
        return true;
    }

    /**
     * Returns if the word is in the trie.
     * @param String $word
     * @return Boolean
     */
    function search($word)
    {
        if (empty($word)) {
            return false;
        }
        $endNode = $this->searchEndNode($word);
        return $endNode !== null && $endNode['is_end'];
    }

    /**
     * Returns if there is any word in the trie that starts with the given prefix.
     * @param String $prefix
     * @return Boolean
     */
    function startsWith($prefix)
    {
        if (empty($prefix)) {
            return false;
        }
        $endNode = $this->searchEndNode($prefix);
        return $endNode != null;
    }

    private function searchEndNode($prefix)
    {
        $length = mb_strlen($prefix);
        $node = $this->root;
        for ($i = 0; $i < $length; $i++) {
            $letter = mb_substr($prefix, $i, 1);
            if (!isset($node[$letter])) {
                return null;
            }
            $node = $node[$letter];
        }
        return $node;
    }
}

$trie = new Trie();
$res = $trie->insert('apple');
$res = $trie->search('apple');
var_dump($res);
$res = $trie->search('app');
var_dump($res);
$res = $trie->startsWith('app');
var_dump($res);
$res = $trie->insert('app');
$res = $trie->search('app');
var_dump($res);