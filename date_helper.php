<?php

class DateHelper
{
    /**
     * 获取某年的第几周的开始日期时间戳和结束时间戳
     * 如果是跨年周，会默认选择第一周这一年的第一天，最后一周是这一年的最后一天
     * @param int $year 年份
     * @param int $week 第几周
     * @return array
     */
    private function _weekday($year, $week = 1)
    {
        $year_start = mktime(0, 0, 0, 1, 1, $year);
        $year_end = mktime(0, 0, 0, 12, 31, $year);
        // 判断第一天是否为第一周的开始
        if (intval(date('W', $year_start)) === 1){
            $start = $year_start;//把第一天做为第一周的开始
        }else{
            $week++;
            $start = strtotime('+1 monday',$year_start);//把第一个周一作为开始
        }
        // 第几周的开始时间
        if ($week===1){
            $weekday['start'] = $start;
        }else{
            $weekday['start'] = strtotime('+'.($week-0).' monday',$start);
        }
        // 第几周的结束时间
        $weekday['end'] = strtotime('+1 sunday',$weekday['start']);
        if (date('Y',$weekday['end'])!=$year){
            $weekday['end'] = $year_end;
        }
        return $weekday;
    }

    /**
     * 获取某年有多少个周
     * @param int $year 年份
     */
    private function _getIsoWeeksInYear($year)
    {
        $date = new \DateTime;
        $date->setISODate($year, 53);
        return ($date->format("W") === "53" ? 53 : 52);
    }
}
