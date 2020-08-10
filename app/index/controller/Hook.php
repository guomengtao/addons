<?php


namespace app\index\controller;


class Hook
{
    static public function listen($plugin)
    {


        $className = '\app\\' . $plugin . '\controller\index';

        if (class_exists($className)) {
            $p = new $className;
            echo $p->index();
        }


    }
}