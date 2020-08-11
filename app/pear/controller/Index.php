<?php
declare (strict_types = 1);

namespace app\pear\controller;

class Index
{
    public function index()
    {
        return '您好！这是一个[pear]示例应用。我是一个水果，而且我很甜，可以润喉。止咳。';
    }

    public  function  eat(){
        return view();
    }
}
