<?php

calss ArrayHelper {
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
    public function multi2point($arr, &$new_arr = [], $k = ''){
        foreach($arr as $key => $value){
            if(is_array($value)){
                $this->multi2point($value,$new_arr, $k.$key.'.');
            } else {
                $new_arr[$k.$key] = $value;
            }
        }
        return $new_arr;
    }

    /**
     * 对二维数组进行分组，并去除分组的键名
     * @param arr $arr 二维数组
     * @param string $key 需要分组的key
     * @return array
     */
    public function _arrayGroupBy($arr, $key)
    {
        $grouped = [];
        foreach ($arr as $value) {
            $val = $value;
            unset($val[$key]); // 删除以选择的key的值
            $grouped[$value[$key]][] = $val;
        }
        // 如果提供了更多参数，则递归地构建嵌套分组
        // 每个分组数组值根据下一个顺序键分组
        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $params = array_merge([$value], array_slice($args, 2, func_num_args()));
                $grouped[$key] = call_user_func_array([$this, '_arrayGroupBy'], $params);
            }
        }
        return $grouped;
    }
    
    /**
     * 多维数组合并，array_merge只支持一维数组的合并
     * @author tom <tom@influx.io>
     * @return void
     */
    private function _arrayMergeMulti()
    {
        $args = func_get_args();
        $array = array();
        foreach ( $args as $arg ) {
            if ( is_array($arg) ) {
                foreach ( $arg as $k => $v ) {
                    if ( is_array($v) ) {
                        $array[$k] = isset($array[$k]) ? $array[$k] : array();
                        $array[$k] = $this->_arrayMergeMulti($array[$k], $v);
                    } else {
                        $array[$k] = $v;
                    }
                }
            }
        }
        return $array;
    }
    
}
