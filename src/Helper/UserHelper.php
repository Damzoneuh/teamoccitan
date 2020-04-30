<?php


namespace App\Helper;


use Exception;

trait UserHelper
{
    /**
     * @return string
     * @throws Exception
     */
    public function getResetToken(){
        return bin2hex(random_bytes(16));
    }
}