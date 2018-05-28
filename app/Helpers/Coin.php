<?php
/**
 * Created by PhpStorm.
 * User: Coding
 * Date: 28/05/2018
 * Time: 11:49
 */

namespace App\Helpers;

class Coin
{

    public static function calcHash($data) {
       return hash("sha256", $data);
    }

}