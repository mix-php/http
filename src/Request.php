<?php

namespace Mix\Http;

/**
 * Request组件
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Request extends BaseRequest
{

    /**
     * 请求者
     * @var \Swoole\Http\Request
     */
    protected $_requester;

    // 针对每个请求执行初始化
    public function initializeRequest($requester)
    {
        // 设置请求者
        $this->_requester = $requester;
        // 执行初始化
        $this->setRoute([]);
        $this->_get    = isset($requester->get) ? $requester->get : [];
        $this->_post   = isset($requester->post) ? $requester->post : [];
        $this->_files  = isset($requester->files) ? $requester->files : [];
        $this->_cookie = isset($requester->cookie) ? $requester->cookie : [];
        $this->_server = isset($requester->server) ? $requester->server : [];
        $this->_header = isset($requester->header) ? $requester->header : [];
    }

    // 返回原始的HTTP包体
    public function getRawBody()
    {
        return $this->_requester->rawContent();
    }

}
