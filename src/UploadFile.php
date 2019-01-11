<?php

namespace Mix\Http;

use Mix\Helpers\RandomStringHelper;

/**
 * UploadFile类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class UploadFile
{

    // 文件名
    public $name;

    // MIME类型
    public $type;

    // 临时文件
    public $tmpName;

    // 错误码
    public $error;

    // 文件尺寸
    public $size;

    /**
     * 创建实例，通过表单名称
     * @param $name
     * @return $this
     */
    public static function newInstance($name)
    {
        $file = \Mix::$app->request->files($name);
        return is_null($file) ? $file : new self($file);
    }

    // 构造
    public function __construct($file)
    {
        $this->name    = $file['name'];
        $this->type    = $file['type'];
        $this->tmpName = $file['tmp_name'];
        $this->error   = $file['error'];
        $this->size    = $file['size'];
    }

    // 文件另存为
    public function saveAs($filename)
    {
        $dir = dirname($filename);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $bytes = file_put_contents($filename, file_get_contents($this->tmpName));
        return $bytes ? true : false;
    }

    // 获取基础名称
    public function getBaseName()
    {
        return pathinfo($this->name)['filename'];
    }

    // 获取扩展名
    public function getExtension()
    {
        return pathinfo($this->name)['extension'];
    }

    // 获取随机文件名
    public function getRandomFileName()
    {
        return md5(RandomStringHelper::randomAlphanumeric(32)) . '.' . $this->getExtension();
    }

}
