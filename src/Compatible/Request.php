<?php

namespace Mix\Http\Compatible;

/**
 * Request组件
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Request extends \Mix\Http\BaseRequest
{

    // 初始化事件
    public function onInitialize()
    {
        parent::onInitialize();
        // 初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        $this->_get    = $_GET;
        $this->_post   = $_POST;
        $this->_files  = $_FILES;
        $this->_cookie = $_COOKIE;
        $this->setHeader();
        $this->setServer();
    }

    // 设置 HEADER 值
    protected function setHeader()
    {
        $header = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $header[str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))))] = $value;
                unset($_SERVER[$name]);
            }
        }
        $this->_header = $header;
    }

    // 设置 SERVER 值
    protected function setServer()
    {
        $this->_server = array_change_key_case($_SERVER, CASE_LOWER);
    }

    // 返回原始的HTTP包体
    public function getRawBody()
    {
        return file_get_contents('php://input');
    }

}
