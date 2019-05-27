<?php

/**
 * @link https://leetcode.com/problems/sqrtx/
 * sqrt
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/27
 * Time: 下午7:49
 */
class Sqrt
{
    public function main(float $n)
    {
        return $this->newtonSearch($n);
    }

    protected function func(float $n)
    {
        return sqrt($n);
    }

    /**
     * 二分查找
     * @param float $n
     * @return float
     */
    protected function binarySearch(float $n)
    {
        if ($n < 0) {
            return 0;
        }
        if ($n == 0 || $n == 1) {
            return $n;
        }
        $left = 0;
        $right = $n;
        $res = '';
        while ($left <= $right) {
            $mid = ($left + $right) / 2;
            $pow = $mid * $mid;
            if (abs($pow - $n) <= 0.0001) {
                return round($mid, 4);
            } elseif ($pow < $n) {
                $left = $mid;
                $res = $mid;
            } elseif ($pow > $n) {
                $right = $mid;
            }
        }

        return round($res, 4);

    }

    /**
     * 牛顿迭代法
     * @param float $n
     * @return float|int
     */
    protected function newtonSearch(float $n)
    {
        $right = $n;
        while ($right * $right > $n) {
            $right = ($right + $n / $right) / 2;
        }
        return $right;
    }
}

$n = 8;
$res = (new Sqrt())->main($n);
var_dump($res);
