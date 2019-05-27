<?php
/**
 * @link https://leetcode.com/problems/two-sum/
 * 一个整型数组，计算那两个数组下标值和等于某个值
 * Given nums = [2, 7, 11, 15], target = 9
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/25
 * Time: 下午4:52
 */

class TwoSum {

    /**
     * 时间复杂度O(n)
     * @param Integer[] $nums
     * @param Integer $target
     * @return Integer[]
     */
    function main($nums, $target) {
        if(count($nums) < 2) {
            return [];
        }
        foreach($nums as $key => $num) {
            $num1 = $target - $num;
            unset($nums[$key]);
            if(($key1 = array_search($num1, $nums)) !== false) {
                return [$key, $key1];
            }
        }
        return [];
    }
}

$nums = [2, 7, 11, 15];
$target = 9;
$result = (new TwoSum())->main($nums, $target);
var_dump($result);