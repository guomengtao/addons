<?php
declare (strict_types=1);

namespace app\index\controller;

class Index
{
    public function index()
    {
        echo "<br>首页<br>";

//        埋入的钩子
        Hook::listen('login');

        echo "<br>底部<br>";

        Hook::listen('news');
    }

    public function addPlugin()
    {
        echo "导入本地插件";
        return view();
    }

    public function upload()
    {

        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');

        $fileName = $file->getOriginalName();
        $fileName = substr($fileName, 0, -4);

        $validate = validate(['image' => 'filesize:10000240|fileExt:zip']);

        $result = $validate->check(['image' => $file]);

        if (!$result) {
            return $result;
        }


        // 上传到本地服务器
        $savename = \think\facade\Filesystem::disk('public')->putFileAs('plugins', $file, $fileName.'.zip');
        dump($savename);



        // 拼接上传后的文件绝对路径
        $uploadPath = './storage/plugins/' . $fileName .".zip" ;


        // 定义解压路径
        $unzipPath = './storage/plugins/unzip/' . $fileName;


        // 实例化对象
        $zip = new \ZipArchive();
        //打开zip文档，如果打开失败返回提示信息
        if ($zip->open($uploadPath, \ZipArchive::CREATE) !== TRUE) {
            die ("Could not open archive");
        } else {
            //将压缩文件解压到指定的目录下
            $zip->extractTo($unzipPath);
            //关闭zip文档
            $zip->close();
            echo '解压成功';
        }

        $root = app()->getRootPath();
        $root = str_replace('\\', '/', $root);

        $f = new FileUtil();
        $f->moveDir($unzipPath,$root. '/app/'.$fileName);
    }
}
