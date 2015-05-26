<?php

/**
 *  計算時間經過, 顯示相對應的文字
 */
class TimeBeforeHelper
{
    public static function get( $times )
    {
        $second = time() - $times;
        if ( $second < -1 ) {
            return '未來的時間';
        }

        if( $second <= 300 ) {
            return '最新檔案';
        }
        elseif( $second <= 600 ) {
            return '10分鐘內';
        }
        elseif( $second <= 3600 ) {
            return '1小時內';
        }
        elseif( $second <= 7200 ) {
            return '2小時內';
        }
        elseif( $second <= 10800 ) {
            return '3小時內';
        }
        elseif( $second <= 21600 ) {
            return '6小時內';
        }

        $today  = strtotime( date("Y-m-d") );
        $myDate = strtotime( date("Y-m-d", $times) );

        if ( $today === $myDate ) {
            return '今天';
        }
        elseif ( $today === $myDate + 86400 ) {
            return '昨天';
        }
        elseif ( $today === $myDate + 172800 ) {
            return '前天';
        }

        // ※以下是假設每週的第一天是 星期一, 最後一天是星期天
        // "last monday" 指的是 本週星期一
        // 該值 -1 可以說是 上週的最後一天
        // 所以 減7天 表示時間是在上週之間
        $thisWeekFirst = strtotime("last monday");
        $prevWeekFirst = $thisWeekFirst - 604800;
        if ( $prevWeekFirst <= $times && $times < $thisWeekFirst ) {
            return '上週';
        }

        // "Y-m-01" 表示本月第一天
        $thisMonth = strtotime(date("Y-m-01"));
        if ( $times >= $thisMonth ) {
            return '這個月';
        }

        //
        return date('Y-F m-d D(w)', $times);
        /*
            // 幾天之內
            $beforeDays = ceil($second / 86400);
            return $beforeDays . ' days before';

            return '很久很久以前';
        */
    }


}
