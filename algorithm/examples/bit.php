<?php

/**
 * @link https://leetcode.com/problems/number-of-1-bits/
 * 二进制
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/28
 * Time: 下午9:08
 */
class Bit
{
    public function main(int $n)
    {
        return $this->bitWise($n);
    }

    /**
     * 取模
     * @param int $n
     * @return int
     */
    public function mod(int $n)
    {
        if ($n == 0 && $n == 1) return $n;
        $count = 0;
        while ($n > 0) {
            if ($n % 2) {
                $count++;
            }
            $n = $n >> 1;
        }
        return $count;
    }

    /**
     * 位运算
     * @param int $n
     * @return int
     */
    public function bitWise(int $n)
    {
        $count = 0;
        while ($n >0) {
            $n = $n & ($n-1); // 剔除最后一个1
            $count++;
        }
        return $count;
    }
}

$n = 12;
$res = (new Bit())->main($n);
echo $res;