<?php

namespace Mix\Http;

/**
 * Class View
 * @package Mix\Http
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class View
{

    /**
     * 标题
     * @var string
     */
    public $title;

    /**
     * 渲染视图
     * @param $__template__
     * @param $__data__
     * @return string
     */
    public function render($__template__, $__data__)
    {
        // 传入变量
        extract($__data__);
        // 生成视图
        $__filepath__ = \Mix::$app->getViewPath() . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $__template__) . '.php';
        if (!is_file($__filepath__)) {
            throw new \Mix\Exception\ViewException("视图文件不存在：{$__filepath__}");
        }
        ob_start();
        include $__filepath__;
        return ob_get_clean();
    }

    /**
     * 获取视图前缀
     * @param AbstractController $controller
     * @return string
     */
    public static function prefix(\Mix\Http\AbstractController $controller)
    {
        $prefix = str_replace([\Mix::$app->route->controllerNamespace . '\\', '\\', 'Controller'], ['', '.', ''], get_class($controller));
        $items  = [];
        foreach (explode('.', $prefix) as $item) {
            $items[] = \Mix\Helper\NameHelper::camelToSnake($item);
        }
        return implode('.', $items);
    }

}
