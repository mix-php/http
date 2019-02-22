<?php

namespace Mix\Http;

use Mix\Core\Bean\AbstractObject;
use Mix\Http\View;

/**
 * Class AbstractController
 * @package Mix\Http
 * @author LIUJIAN <coder.keda@gmail.com>
 */
abstract class AbstractController extends AbstractObject
{

    /**
     * 默认布局
     * @var string
     */
    public $layout = 'main';

    /**
     * 渲染视图 (包含布局)
     * @param $name
     * @param array $data
     * @return string
     */
    public function render($name, $data = [])
    {
        if (strpos($name, '.') === false) {
            $name = View::prefix($this) . '.' . $name;
        }
        $view            = new View();
        $data['content'] = $view->render($name, $data);
        return $view->render("layouts.{$this->layout}", $data);
    }

    /**
     * 渲染视图 (不包含布局)
     * @param $name
     * @param array $data
     * @return string
     */
    public function renderPartial($name, $data = [])
    {
        if (strpos($name, '.') === false) {
            $name = View::prefix($this) . '.' . $name;
        }
        $view = new View();
        return $view->render($name, $data);
    }

}
