<?php
declare (strict_types = 1);

namespace app\index\controller;

class Index
{
    public function index()
    {
        echo  "<br>首页<br>";

//        埋入的钩子
        Hook::listen('login');

        echo "<br>底部<br>";

        Hook::listen('news');
    }
}
