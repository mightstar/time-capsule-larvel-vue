<?php

namespace App\Utilities;

use Illuminate\Support\Collection;

class Data
{

    /**
     * Take value form array
     * @param $arr
     * @param $key
     * @return mixed|null
     */
    public static function take(&$arr, $key)
    {
        if (isset($arr[$key])) {
            $value = $arr[$key];
            unset($arr[$key]);
            return $value;
        }
        return null;
    }
}
