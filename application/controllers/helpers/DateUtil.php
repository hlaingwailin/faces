<?php

class My_Action_Helper_DateUtil extends Zend_Controller_Action_Helper_Abstract
{

    // 2014 11 01 19:12:30
    const DEFAULT_DATE_FORMAT = "Y-m-d H:i:s";
    const FIRST_DAY_OF_MONTH = '01 M y';
    const INVOICE_RUNNING_NUMBER_FORMAT = 'ym'; // 1407 for the month of July 2014 invoices

    public static function convertDateFormat($date, $format = self::DEFAULT_DATE_FORMAT)
    {
        $dateObj = new DateTime($date);
        return $dateObj->format($format);
    }

    public static function getFirstDayOfMonth($date, $format = self::FIRST_DAY_OF_MONTH)
    {
        $dateObj = new DateTime($date);
        return $dateObj->format($format);
    }

    public static function getToday(){
         return date(self::DEFAULT_DATE_FORMAT);
    }

    public static function rollDays(DateTime $dateObj, $days){
         return $dateObj->modify("+" . $days . " day");
    }

    public static function getDaysDifference($date1, $date2){
        $dueDateObj = new DateTime($date1);
        $payDateObj = new DateTime($date2);

        $interval = $payDateObj->diff($dueDateObj);
        $d = $interval->days;

        return $d;
    }

}

?>