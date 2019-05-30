<?php

/**
 * @link https://leetcode.com/problems/powx-n/
 * pow(x,n)
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/26
 * Time: 下午2:44
 */
class Pow
{
    public function main(int $x, int $n)
    {
        return $this->recurrence($x, $n);
    }

    protected function func(int $x, int $n)
    {
        return pow($x, $n);
    }

    /**
     * 循环
     * @param int $x
     * @param int $n
     * @return float|int
     */
    protected function loop(int $x, int $n)
    {
        $result = 1;
        for ($i = 1; $i <= $n; $i++) {
            $result = $result * $x;
        }
        return $result;
    }

    /**
     * 递归
     * @param int $x
     * @param int $n
     * @return float|int
     */
    protected function recurrence(int $x, int $n)
    {
        if ($n < 0) {
            return 1 / $this->recurrence($x, -$n);
        } elseif ($n == 0) {
            return 1;
        } elseif ($n == 1) {
            return $x;
        }

        if ($n % 2) {
            return $x * $this->recurrence($x * $x, floor($n / 2));
        } else {
            return $this->recurrence($x * $x, $n / 2);
        }
    }
}

$x = 2;
$n = -2;
$result = (new Pow())->main($x, $n);

echo $result;
