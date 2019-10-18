<?php

namespace iBrand\Coterie\Core\Common;

use iBrand\Common\Wechat\Factory;
use Storage;


class MiniProgram
{
    protected $config;

    public function __construct()
    {
        $this->config = config('ibrand.wechat.mini_program.coterie');
    }

    public function msgSecCheck($str)
    {
        $app = Factory::miniProgram($this->config);

        return $app->content_security->checkText($str);

    }

    public function imgSecCheck($path){

        $app = Factory::miniProgram($this->config);

        return $app->content_security->checkImage($path);
    }

}