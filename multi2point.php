<?php

// 主要处理mongodb中的子文档点对点更新数据

/**
 * 将多维数组转化成一维数组，键名由多维数组的键名由“.”拼接，如下例子：：
 * 调用方式：var_export($this->loopHH($dd))；
 * $dd = [                      [
 *      'a'=>[                      "a.b.0.0" => 1,
 *          'b'=>[                  "a.b.0.1" => 2,
 *              [1,2,3,4]  ==>      "a.b.0.2" => 3,
 *          ],             ==>      "a.b.0.3" => 4,
 *          'c'=>[         ==>      "a.c.0.0" => 2,
 *              [2,3,4]             "a.c.0.1" => 3,
 *          ]                       "a.c.0.2" => 4
 *      ],                      ]
 *  ];
 * @param array     $arr          需要处理的多维数组
 * @param array     $new_arr      初始化一个空数组
 * @param string    $k            初始化key
 * @return array
 */
public function loopHH($arr, &$new_arr = [], $k = ''){
    foreach($arr as $key => $value){
        if(is_array($value)){
            $this->loopHH($value,$new_arr, $k.$key.'.');
        } else {
            $new_arr[$k.$key] = $value;
        }
    }
    return $new_arr;
}
