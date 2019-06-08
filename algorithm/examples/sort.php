<?php
/**
 * 排序算法
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/6/8
 * Time: 下午2:01
 */
class Sort
{
    /**
     * 冒泡排序
     * 时间复杂度：O(n^2)
     * 空间复杂度：O(1)
     * @param $arr
     * @return mixed
     */
    public function bubbleSort($arr) {
        for($i = 0; $i < count($arr) - 1; $i++) {
            for($j = 0; $j < count($arr) - $i - 1; $j++) {
                if($arr[$j] > $arr[$j+1]) {
                    $temp = $arr[$j+1];
                    $arr[$j+1] = $arr[$j];
                    $arr[$j] = $temp;
                }
            }
        }

        return $arr;
    }

    /**
     * 选择排序
     * 时间复杂度：O(n^2)
     * 空间复杂度：O(1)
     * @param $arr
     * @return mixed
     */
    public function selectionSort($arr) {
        for($i = 0; $i < count($arr) - 1; $i++) {
            $minIndex = $i;
            for($j = $i + 1; $j < count($arr); $j++) {
                if($arr[$j] < $arr[$minIndex]) {
                    $minIndex = $j;
                }
            }

            $temp = $arr[$i];
            $arr[$i] = $arr[$minIndex];
            $arr[$minIndex] = $temp;
        }
        return $arr;
    }

    /**
     * 插入排序
     * 时间复杂度：O(n) ~ O(n^2)
     * 空间复杂度：O(1)
     * @param $arr
     * @return mixed
     */
    public function insertionSort($arr){
        for($i = 1; $i < count($arr); $i++) {
            $preIndex = $i - 1;
            $current = $arr[$i];
            while($preIndex >= 0 && $arr[$preIndex] > $current) {
                $arr[$preIndex + 1] = $arr[$preIndex];
                $preIndex --;
            }

            $arr[$preIndex + 1] = $current;
        }

        return $arr;
    }

    /**
     * 希尔排序
     * 时间复杂度：O(n^1.3) ~ O(n^2)
     * 空间复杂度：O(1)
     * @param $arr
     * @return mixed
     */
    public function shellSort($arr){
        $gap = 1;
        while($gap < count($arr) /3) {
            $gap = $gap * 3 +1;
        }
        for($gap; $gap > 0; $gap = floor($gap / 3)) {
            for( $i = $gap; $i < count($arr); $i++) {
                $temp = $arr[$i];
                for($j = $i - $gap; $j >= 0 && $arr[$j] > $temp; $j-= $gap) {
                    $arr[$j + $gap] = $arr[$j];
                }

                $arr[$j + $gap] = $temp;
            }
        }

        return $arr;
    }

    /**
     * 归并排序
     * 时间复杂度：O(nlogn)
     * 空间复杂度：O(n)
     * @param $arr
     * @return array
     */
    public function mergeSort($arr) {
        if(count($arr) < 2) {
            return $arr;
        }
        $left = array_slice($arr, 0, floor(count($arr) / 2));
        $right = array_slice($arr, floor(count($arr) / 2));
        return $this->merge($this->mergeSort($left), $this->mergeSort($right));
    }
    private function merge($left, $right)
    {
        $result = [];

        while(count($left) > 0 && count($right) > 0) {
            if($left[0] <= $right[0]) {
                array_push($result, array_shift($left));
            }else{
                array_push($result, array_shift($right));
            }
        }

        while(count($left)) {
            array_push($result, array_shift($left));
        }

        while(count($right)) {
            array_push($result, array_shift($right));
        }

        return $result;
    }


    /**
     * 快速排序
     * 时间复杂度: O(nlogn) ~ O(n^2)
     * 空间复杂度：O(nlogn)
     * @param $arr
     * @return array
     */
    public function quickSort($arr) {
        if(count($arr) < 2) {
            return $arr;
        }
        $left = [];
        $right = [];
        for ($i = 1; $i < count($arr); $i++) {
            if($arr[$i] > $arr[0]) {
                $right[] = $arr[$i];
            }else{
                $left[] = $arr[$i];
            }
        }

        $left = $this->quickSort($left);
        $right = $this->quickSort($right);

        return array_merge($left, [$arr[0]], $right);
    }

    /**
     * 堆排序
     * 时间复杂度：O(nlogn)
     * 空间复杂度：O(1)
     * @param $arr
     * @return mixed
     */
    public function heapSort($arr) {
        $len = count($arr);
        for ($i = floor(($len-2)/2); $i >= 0; $i--) {
            $this->maxHeapify($arr, $i, $len-1);
        }

        for($i = $len - 1; $i >= 0; $i--) {
            $temp = $arr[$i];
            $arr[$i] = $arr[0];
            $arr[0] = $temp;

            $this->maxHeapify($arr, 0, $i-1);
        }

        return $arr;
    }
    private function maxHeapify(&$arr, $start, $end) {
        $temp = $arr[$start];
        for ($s = 2*$start+1; $s <= $end; $s = 2*$s+1) {
            if ($s < $end && $arr[$s] < $arr[$s+1]) {
                $s++;
            }
            if ($temp > $arr[$s]) {
                break;
            }
            $arr[$start] = $arr[$s];
            $start = $s;
        }
        $arr[$start] = $temp;
    }

    /**
     * 计数排序
     * 时间复杂度：O(n+k) k为最小值和最大值的跨度
     * 空间复杂度：O(n+k)
     * @param $arr
     * @return mixed
     */
    public function countingSort($arr) {
        if(count($arr) < 2) {
            return $arr;
        }
        $min = min($arr);
        $max = max($arr);
        if($min >= $max) {
            return $arr;
        }
        $bucket = array_fill_keys(range($min, $max), 0);
        foreach ($arr as $value) {
            if(isset($bucket[$value])) {
                $bucket[$value] ++;
            }
        }

        $sortIndex = 0;
        foreach($bucket as $key => $value) {
            while($value) {
                $arr[$sortIndex++] = $key;
                $value--;
            }
        }

        return $arr;
    }

    /**
     * 桶排序
     * 时间复杂度：O(n) ~ O(n^2)
     * 空间复杂度 O(n+k) k为最小值和最大值的跨度
     * @param $arr
     * @param int $bucketCount
     * @return array
     */
    public function bucketSort($arr, $bucketCount = 5) {
        if(count($arr) < 2 || $bucketCount < 1) {
            return $arr;
        }
        $min = min($arr);
        $max = max($arr);
        if($min >= $max) {
            return $arr;
        }
        $space = ($max - $min + 1) / $bucketCount;
        $buckets = [];
        foreach($arr as $key => $value) {
            $index = intval(floor(($value - $min) / $space));
            if(isset($buckets[$index])) {
                $k = count($buckets[$index]) - 1;
                while( $k >= 0 && $buckets[$index][$k] > $value) {
                    $buckets[$index][$k + 1] = $buckets[$index][$k];
                    $k--;
                }
                $buckets[$index][$k + 1] = $value;
            }else {
                $buckets[$index] = [];
                $buckets[$index][] = $value;
            }
        }


        $i = 0;
        $data = [];
        while($i < $bucketCount) {
            if($buckets[$i]) {
                $data = array_merge($data, $buckets[$i]);
            }

            $i++;
        }

        return $data;
    }

    /**
     * 基数排序
     * 时间复杂度：O(n*k)
     * 空间复杂度：O(n+k)
     * @param $arr
     * @return mixed
     */
    public function radixSort($arr) {
        $len = count($arr);
        if($len < 2) {
            return $arr;
        }

        $max = max($arr);
        $loop = 1;
        while($max >= 10) {
            $loop ++;
            $max = floor($max / 10);
        }

        for($i = 1; $i <= $loop; $i++){
            $dev = intval(pow(10, $i - 1));
            $mod = 10;
            $counter = array_fill_keys(range(0, 9), []);
            for($j = 0; $j < $len; $j++) {
                $bucket = intval(floor(($arr[$j] / $dev) % $mod));
                array_push($counter[$bucket], $arr[$j]);
            }

            $pos = 0;
            for($k = 0; $k < 10; $k++) {
                while(count($counter[$k]) > 0) {
                    $arr[$pos++] = array_shift($counter[$k]);
                }
            }
        }

        return $arr;
    }
}

$arr = [3,9,6,97,14,9,2,8,34,12,23,48,18];

$sort = new Sort();
$startTime = microtime(true);
$ret = $sort->bubbleSort($arr);
echo '冒泡排序时间：'. (microtime(true)-$startTime) *1000 . 's'.PHP_EOL;

$startTime = microtime(true);
$ret = $sort->selectionSort($arr);
echo '选择排序时间：'. (microtime(true)-$startTime) *1000 . 's'.PHP_EOL;

$startTime = microtime(true);
$ret = $sort->insertionSort($arr);
echo '插入排序时间：'. (microtime(true)-$startTime) *1000 . 's'.PHP_EOL;

$startTime = microtime(true);
$ret = $sort->shellSort($arr);
echo '排序时间：'. (microtime(true)-$startTime) *1000 . 's'.PHP_EOL;

$startTime = microtime(true);
$ret = $sort->mergeSort($arr);
echo '归并排序时间：'. (microtime(true)-$startTime) *1000 . 's'.PHP_EOL;

$startTime = microtime(true);
$ret = $sort->quickSort($arr);
echo '快速排序时间：'. (microtime(true)-$startTime) *1000 . 's'.PHP_EOL;

$startTime = microtime(true);
$ret = $sort->heapSort($arr);
echo '堆排序时间：'. (microtime(true)-$startTime) *1000 . 's'.PHP_EOL;

$startTime = microtime(true);
$ret = $sort->countingSort($arr);
echo '计数排序时间：'. (microtime(true)-$startTime) *1000 . 's'.PHP_EOL;

$startTime = microtime(true);
$ret = $sort->bucketSort($arr);
echo '桶排序时间：'. (microtime(true)-$startTime) *1000 . 's'.PHP_EOL;

$startTime = microtime(true);
$ret = $sort->radixSort($arr);
echo '基数排序时间：'. (microtime(true)-$startTime) *1000 . 's'.PHP_EOL;
