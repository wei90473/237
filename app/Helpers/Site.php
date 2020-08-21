<?php
namespace App\Helpers;

class Site
{
    static $foohooa_sites = ['101', '103', '201', '202', '203', '204', '205', 'C01', 'C02'];
    /*
        檢查是否為福華場地
    */
    public static function checkFooHooa($site)
    {
        return in_array($site, self::$foohooa_sites);
    }

}