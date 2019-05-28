<?php

/**
 * 字典树
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/28
 * Time: 下午1:49
 */
class TrieNode
{
    // 节点值
    private $val;
    // 是否是最后节点
    private $isEnd = false;

    /**
     * 孩子节点
     * @var TrieNode
     */
    protected $children = [];


    public function __construct()
    {
    }

    public function getChildren($letter)
    {
        return $this->children[$letter];
    }

    public function setChildren($letter, TrieNode $node)
    {
        $this->children[$letter] = $node;
    }

    public function setVal($val)
    {
        $this->val = $val;
    }

    public function setEnd()
    {
        $this->isEnd = true;
    }

    public function isEnd()
    {
        return $this->isEnd;
    }
}

class DicTrie
{
    /**
     * 根节点
     * @var TrieNode
     */
    private $root;

    public function __construct()
    {
        $this->root = new TrieNode();
    }

    /**
     * 插入
     * @param $word
     * @return bool
     */
    public function insert($word)
    {
        if (empty($word)) {
            return false;
        }
        $length = mb_strlen($word);
        $node = $this->root;
        for ($i = 0; $i < $length; $i++) {
            $letter = mb_substr($word, $i, 1);
            if (empty($node->getChildren($letter))) {
                $children = new TrieNode();
                $children->setVal($letter);
                $node->setChildren($letter, $children);
            }
            $node = $node->getChildren($letter);
        }
        $node->setEnd();

        return true;
    }

    /**
     * 搜索
     * @param $word
     * @return bool
     */
    public function search($word)
    {
        if (empty($word)) {
            return false;
        }
        $endNode = $this->searchEndNode($word);
        return $endNode !== null && $endNode->isEnd();
    }

    /**
     * 前缀搜索
     * @param $prefix
     * @return bool
     */
    public function startsWith($prefix)
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
            $children = $node->getChildren($letter);
            if (empty($children)) {
                return null;
            }
            $node = $children;
        }

        return $node;
    }
}

$trie = new DicTrie();
$res = $trie->insert('字典树，又称 前缀树 或 trie树，是一种有序树，用于保存关联数组，其中的键通常是字符串。');
var_dump($res);
$res = $trie->startsWith('字典树，又称 前缀树 或 trie树，是一种有序树，用于保存关联数组，其中的键通常是字符串。');
var_dump($res);