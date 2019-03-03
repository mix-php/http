<?php

namespace Mix\Http\Application;

/**
 * Trait DebugTrait
 * @package Mix\Http\Application
 * @author LIUJIAN <coder.keda@gmail.com>
 */
trait DebugTrait
{

    /**
     * 打印变量的相关信息
     * @param $var
     * @param bool $send
     */
    public function dump($var, $send = false)
    {
        ob_start();
        var_dump($var);
        $dumpContent                  = ob_get_clean();
        \Mix::$app->response->content .= $dumpContent;
        if ($send) {
            throw new \Mix\Exception\DebugException(\Mix::$app->response->content);
        }
    }

    /**
     * 终止程序
     * @param string $content
     */
    public function end($content = '')
    {
        throw new \Mix\Exception\EndException($content);
    }

}
