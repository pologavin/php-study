<?php

/**
 * @link https://leetcode.com/problems/3sum/
 * 给定一个包含 n 个整数的数组 nums，判断 nums 中是否存在三个元素 a，b，c ，使得 a + b + c = 0 ？找出所有满足条件且不重复的三元组。
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/25
 * Time: 下午9:31
 */
class ThreeSum
{
    public function main(array $nums, int $n = 0)
    {
        //return $this->sumSearch($nums, $n);
        return $this->sortSearch($nums, $n);
    }

    protected function sumSearch(array $nums, int $n = 0)
    {
        $result = [];
        $count = count($nums);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {

                $keys = array_keys($nums, $n - $nums[$i] - $nums[$j]);
                if (empty($keys)) {
                    continue;
                }
                $key = array_diff($keys, [$i, $j]);
                if (!empty($key)) {
                    $tmp = [$nums[$i], $nums[$j], $n - $nums[$i] - $nums[$j]];
                    sort($tmp);
                    if (!in_array($tmp, $result)) {
                        $result[] = $tmp;
                    }
                }
            }
        }
        return $result;
    }

    protected function sortSearch(array $nums, int $n = 0)
    {
        $result = [];
        $count = count($nums);
        sort($nums);
        for ($i = 0; $i < $count; $i++) {
            $left = $i + 1;
            $right = $count - 1;
            while ($left < $right) {
                $sum = $nums[$i] + $nums[$left] + $nums[$right];
                if ($sum > $n) {
                    $right--;
                } elseif ($sum < $n) {
                    $left++;
                } else {
                    $tmp = [$nums[$i], $nums[$left], $nums[$right]];
                    sort($tmp);
                    if(!in_array($tmp, $result)) {
                       $result[] = $tmp;
                    }

                    $left++;
                    $right--;
                }
            }
        }

        return $result;
    }
}

$nums = [-1,0,1,2,-1,-4];
$result = (new ThreeSum())->main($nums);
var_dump($result);