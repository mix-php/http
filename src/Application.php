<?php

namespace Mix\Http;

use Mix\Helper\FileSystemHelper;
use Mix\Http\Middleware\MiddlewareHandler;
use Mix\Core\Application\ComponentInitializeTrait;

/**
 * Class Application
 * @package Mix\Http
 * @author LIUJIAN <coder.keda@gmail.com>
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
     * 控制器命名空间
     * @var string
     */
    public $controllerNamespace = '';

    /**
     * 中间件命名空间
     * @var string
     */
    public $middlewareNamespace = '';

    /**
     * 全局中间件
     * @var array
     */
    public $middleware = [];

    /**
     * 执行功能
     */
    public function run()
    {
        $server                       = \Mix::$app->request->server();
        $method                       = strtoupper($server['request_method']);
        $action                       = empty($server['path_info']) ? '' : substr($server['path_info'], 1);
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
        $action = "{$method} {$action}";
        // 路由匹配
        $result = \Mix::$app->route->match($action);
        foreach ($result as $item) {
            list($route, $queryParams) = $item;
            // 路由参数导入请求类
            \Mix::$app->request->setRoute($queryParams);
            // 实例化控制器
            list($shortClass, $shortAction) = $route;
            $controllerDir    = \Mix\Helper\FileSystemHelper::dirname($shortClass);
            $controllerDir    = $controllerDir == '.' ? '' : "$controllerDir\\";
            $controllerName   = \Mix\Helper\NameHelper::snakeToCamel(\Mix\Helper\FileSystemHelper::basename($shortClass), true);
            $controllerClass  = "{$this->controllerNamespace}\\{$controllerDir}{$controllerName}Controller";
            $shortAction      = \Mix\Helper\NameHelper::snakeToCamel($shortAction, true);
            $controllerAction = "action{$shortAction}";
            // 判断类是否存在
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                // 判断方法是否存在
                if (method_exists($controllerInstance, $controllerAction)) {
                    // 通过中间件执行功能
                    $middlewares = MiddlewareHandler::newInstances($this->middlewareNamespace, array_merge($this->middleware, $route['middleware']));
                    $callback    = [$controllerInstance, $controllerAction];
                    return MiddlewareHandler::run($callback, $middlewares);
                }
            }
            // 不带路由参数的路由规则找不到时，直接抛出错误
            if (empty($queryParams)) {
                break;
            }
        }
        throw new \Mix\Exception\NotFoundException('Not Found (#404)');
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
