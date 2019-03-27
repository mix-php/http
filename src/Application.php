<?php

namespace Mix\Http;

use Mix\Helper\FileSystemHelper;
use Mix\Core\Application\ComponentInitializeTrait;

/**
 * Class Application
 * @package Mix\Http
 * @author liu,jian <coder.keda@gmail.com>
 */
class Application extends \Mix\Core\Application
{

    use ComponentInitializeTrait;

    /**
     * 公开目录路径
     * @var string
     */
    public $publicPath = 'public';

    /**
     * 视图目录路径
     * @var string
     */
    public $viewPath = 'views';

    /**
     * 执行功能
     */
    public function run()
    {
        $method = \Mix::$app->request->server('request_method', 'GET');
        $action = \Mix::$app->request->server('path_info', '/');
        \Mix::$app->response->content = $this->runAction($method, $action);
        \Mix::$app->response->send();
    }

    /**
     * 执行功能并返回
     * @param $method
     * @param $action
     * @return mixed
     */
    public function runAction($method, $action)
    {
        $rule = "{$method} {$action}";
        return \Mix::$app->route->handle($rule);
    }

    /**
     * 获取公开目录路径
     * @return string
     */
    public function getPublicPath()
    {
        if (!FileSystemHelper::isAbsolute($this->publicPath)) {
            if ($this->publicPath == '') {
                return $this->basePath;
            }
            return $this->basePath . DIRECTORY_SEPARATOR . $this->publicPath;
        }
        return $this->publicPath;
    }

    /**
     * 获取视图目录路径
     * @return string
     */
    public function getViewPath()
    {
        if (!FileSystemHelper::isAbsolute($this->viewPath)) {
            if ($this->viewPath == '') {
                return $this->basePath;
            }
            return $this->basePath . DIRECTORY_SEPARATOR . $this->viewPath;
        }
        return $this->viewPath;
    }

}
