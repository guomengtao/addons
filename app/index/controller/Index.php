<?php
declare (strict_types=1);

namespace app\index\controller;

class Index
{
    public function index()
    {
        return view('layui');
    }

    public function layui()
    {
        return view();
    }

    public function addPlugin()
    {
        echo "导入本地插件";
        return view();
    }

    public function layuiUpload()
    {
        return view();
    }

    public function layuiAdmin()
    {
        return view();
    }

    public function up()
    {
        // 获取表单上传文件
        $file = request()->file('file_plugin');
        if (!empty($file)) {
            return json(['info' => '请选择上传文件！', 'status' => 0]);
        }
        // 移动到框架应用根目录/public/upload/ 目录下
        $info = 0;
        if ($info) {
            return json(['info' => $info->getSaveName(), 'status' => 1]); //文件名称
        } else {
            return json(['info' => '文件上传失败啦！', 'status' => 0]);
        }
    }

    /**
     * layui 文件上传接口
     */
    public function uploads()
    {


        // file('文件域的字段名')
        $file = request()->file('image');

        if (empty($file)) {
            return json(['info' => '请选择上传文件！', 'status' => 0]);
        }


        // 上传到本地服务器 返回文件存储位置
        //
        // disk('磁盘配置名称') 该配置 在 config/filesystem.php中的 disks 中查看
        // disk('public') 代表使用的是 disks 中的 public 键名对应的磁盘配置
        // putFile('目录名', $file);
        //
        // $savename 执行上传 返回文件存储位置
        //
        // 当前文件存储位置：public/storage/topic/当前时间/文件名
        $savename = \think\facade\Filesystem::disk('public')->putFile('topic',
            $file);

        // 将上传后的文件位置返回给前端

        return json(['code' => 0, 'path' => $savename]);
    }

    public function uploadLayui()
    {


        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('uploadZip');

        // 获取文件扩展名 ，非后缀名
        $file->getOriginalMime();


        $fileName = $file->getOriginalName();;
        $fileName = substr($fileName, 0, -4);
        $fileSize = $file->getSize();
        // 文件格式
        if ($file->getOriginalExtension() <> 'zip') {
            return json([
                'info' => '需要使用zip格式上传',
                'test' => 0
            ]);
        }

        // 文件大小的限制
        if ($fileSize > 91338) {
            return json([
                'info' => '文件需小于2M',
                'test' => 0
            ]);
        }


        if ($fileName == 'index') {
            return json([
                'info' => 'index模块为基础插件功能模块，请尝试使用其它名称做为插件名',
                'test' => 0
            ]);
        }

        if ($fileName == 'addon') {
            return json([
                'info' => 'addon模块为基础插件功能模块，请尝试使用其它名称做为插件名',
                'test' => 0
            ]);
        }


        // 上传到本地服务器
        $savename = \think\facade\Filesystem::disk('public')
            ->putFileAs('plugins', $file, $fileName.'.zip');

        // return json([
        //     'info' => '上传成功',
        //     'test' => 0
        // ]);

        // 拼接上传后的文件绝对路径
        $uploadPath = './storage/plugins/'.$fileName.".zip";


        // 定义解压路径
        $unzipPath = './storage/plugins/unzip/'.$fileName;


        // 实例化对象
        $zip = new \ZipArchive();
        //打开zip文档，如果打开失败返回提示信息
        if ($zip->open($uploadPath, \ZipArchive::CREATE) !== true) {

            return json([
                'info' => '解压失败',
                'test' => 0
            ]);


        } else {
            //将压缩文件解压到指定的目录下
            $zip->extractTo($unzipPath);
            //关闭zip文档
            $zip->close();

        }

        $root = app()->getRootPath();
        $root = str_replace('\\', '/', $root);

        $f = new FileUtil();
        $f->moveDir($unzipPath, $root.'/app/'.$fileName);

        return json([
            'info' => '插件安装成功',
            'test' => 0,
            'url'  => $fileName
        ]);
    }

    public function upload()
    {


        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');


        $fileName = $file->getOriginalName();
        $fileName = substr($fileName, 0, -4);

        if ($fileName == 'index') {
            return "index模块为基础插件功能模块，请尝试使用其它名称做为插件名";
        }

        $validate = validate(['image' => 'filesize:10000240|fileExt:zip']);

        $result = $validate->check(['image' => $file]);

        if (!$result) {
            return $result;
        }


        // 上传到本地服务器
        $savename = \think\facade\Filesystem::disk('public')
            ->putFileAs('plugins', $file, $fileName.'.zip');
        dump($savename);


        // 拼接上传后的文件绝对路径
        $uploadPath = './storage/plugins/'.$fileName.".zip";


        // 定义解压路径
        $unzipPath = './storage/plugins/unzip/'.$fileName;


        // 实例化对象
        $zip = new \ZipArchive();
        //打开zip文档，如果打开失败返回提示信息
        if ($zip->open($uploadPath, \ZipArchive::CREATE) !== true) {
            die("Could not open archive");
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
        $f->moveDir($unzipPath, $root.'/app/'.$fileName);
    }

    public function uploadPlug()
    {

        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file_plugin');

        if (empty($file)) {
            return json(['info' => '请选择上传文件！', 'status' => 0]);
        }

        $fileName = $file->getOriginalName();
        $fileName = substr($fileName, 0, -4);
        $fileSize = $file->getSize();

        // 文件小于2097152B 即2M
        if ($fileSize > '2097152') {
            return json(['info' => '文件限2M内，修改后重新删除', 'status' => 0]);
        }

        if ($file->getOriginalExtension() <> 'zip') {
            return json([
                'info'   => '请上传zip文件 × '
                    .$file->getOriginalExtension(),
                'status' => 0
            ]);
        }


        if ($fileName == 'index') {
            return json([
                'info'   => 'index模块为基础插件功能模块，请尝试使用其它名称做为插件名！',
                'status' => 0
            ]);
        }

        if ($fileName == 'addon') {
            return json([
                'info'   => 'addon模块为基础插件功能模块，请尝试使用其它名称做为插件名！',
                'status' => 0
            ]);
        }


        // 上传到本地服务器
        $savename = \think\facade\Filesystem::disk('public')
            ->putFileAs('plugins', $file, $fileName.'.zip');

        // return json(['info' => '插件上传成功！', 'status' => 0]);


        // 拼接上传后的文件绝对路径
        $uploadPath = './storage/plugins/'.$fileName.".zip";


        // 定义解压路径
        $unzipPath = './storage/plugins/unzip/'.$fileName;


        // 实例化对象
        $zip = new \ZipArchive();
        //打开zip文档，如果打开失败返回提示信息
        if ($zip->open($uploadPath, \ZipArchive::CREATE) !== true) {
            return json(['info' => '插件解压失败！', 'status' => 0]);
        } else {
            //将压缩文件解压到指定的目录下
            $zip->extractTo($unzipPath);
            //关闭zip文档
            $zip->close();
            // return json(['info' => '插件解压成功！', 'status' => 0]);
        }

        $root = app()->getRootPath();
        $root = str_replace('\\', '/', $root);

        $f = new FileUtil();
        $f->moveDir($unzipPath, $root.'/app/'.$fileName);

        return json([
            'info'       => '插件安装成功！',
            'status'     => 1,
            'pluginName' => "./".$fileName
        ]);
    }


    public function layuiUploadAPI()
    {


        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('pluginFile');


        $fileName = $file->getOriginalName();
        $fileName = substr($fileName, 0, -4);


        // if ($file->getOriginalExtension() <> 'application/x-zip-compressed'){
        //     return json(['info'   => $file->getOriginalMime(),
        //                  'status' => 0
        //     ]);
        // }


        if ($file->getOriginalExtension() <> 'zip'){
            return json(['info'   => '需要上传zip格式的插件',
                         'status' => 0
            ]);
        }

        if ($file->getSize() > '2097152'){
            return json(['info'   => '文件需要小于2M',
                         'status' => 0
            ]);
        }



        if ($fileName == 'index') {
            return json(['info'   => '"index模块为基础插件功能模块，请尝试使用其它名称做为插件名',
                         'status' => 0
            ]);

        }

        if ($fileName == 'addon') {
            return json(['info'   => '"addon模块为基础插件功能模块，请尝试使用其它名称做为插件名',
                         'status' => 0
            ]);

        }








        // 上传到本地服务器
        $savename = \think\facade\Filesystem::disk('public')
            ->putFileAs('plugins', $file, $fileName.'.zip');




        // 拼接上传后的文件绝对路径
        $uploadPath = './storage/plugins/'.$fileName.".zip";


        // 定义解压路径
        $unzipPath = './storage/plugins/unzip/'.$fileName;


        // 实例化对象
        $zip = new \ZipArchive();
        //打开zip文档，如果打开失败返回提示信息
        if ($zip->open($uploadPath, \ZipArchive::CREATE) !== true) {
            return json(['info' => '解压失败', 'status' => 0]);
        } else {
            //将压缩文件解压到指定的目录下
            $unzip = $zip->extractTo($unzipPath);
            if(!$unzip){
                return json(['info' => $unzip.'解压失败', 'status' => 0]);
            }

            //关闭zip文档
            $zip->close();

        }

        $root = app()->getRootPath();
        $root = str_replace('\\', '/', $root);

        return json(['info' => $root, 'status' => 1,'url'=> $fileName]);

        $f = new FileUtil();
        $move = $f->moveDir($unzipPath, $root.'/app/'.$fileName);

        if (!$move){
            return json(['info' => $move."移动失败", 'status' => 0,'url'=> $fileName]);
        }

        return json(['info' => '上传成功', 'status' => 1,'url'=> $fileName]);
    }

    public function list(){
        return view();
    }
}
