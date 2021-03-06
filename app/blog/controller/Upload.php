<?php


namespace app\blog\controller;


class Upload
{


    public function index()
    {
        return view();
    }

    public function upload()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');


        // 获取上传的原始文件名
        $fileName = $file->getOriginalName();
        $fileName = substr($fileName, 0, -4);

        $validate = validate(['image' => 'filesize:10000240|fileExt:zip']);

        $result = $validate->check(['image' => $file]);

        if (!$result) {
            return $result;
        }

        // 上传到本地服务器
        $savename = \think\facade\Filesystem::disk('public')->putFileAs('plugins/', $file, $fileName.".zip");
        dump($savename);

        $root = app()->getRootPath();
        $root = str_replace('\\', '/', $root);


        // 拼接上传后的文件绝对路径
        $uploadPath = './storage/plugins/' . $fileName.".zip";


        dump($uploadPath);


        // 定义解压路径
        $unzipPath = './storage/plugins/unzip/'. $fileName;


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

        $fu = new FileUtil();
        $copyDir = $fu->copyDir($unzipPath, $root.'/app/'.$fileName);
        dump($copyDir);


    }



    public function mov()
    {
        $fu = new FileUtil();
//        $fu->copyFile('a/1/2/3', 'a/1/2/4');
//        $fu->copyDir('a/1', 'D:\360Downloads\addons\app');

    }
}