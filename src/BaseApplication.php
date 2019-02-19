<?php

namespace Mix\Http;

/**
 * Class BaseApplication
 * @package Mix\Http
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class BaseApplication extends \Mix\Core\Application
{

    /**
     * 获取组件
     * @param $name
     * @return \Mix\Core\Component\ComponentInterface
     */
    public function __get($name)
    {
        // 从容器返回组件
        $component = $this->container->get($name);
        // 触发前置处理事件
        self::triggerBeforeInitialize($component);
        // 返回组件
        return $component;
    }

    /**
     * 清扫组件容器
     */
    public function cleanComponents()
    {
        // 触发后置处理事件
        foreach (array_keys($this->components) as $name) {
            if (!$this->container->has($name)) {
                continue;
            }
            $component = $this->container->get($name);
            self::triggerAfterInitialize($component);
        }
    }

    /**
     * 触发前置处理事件
     * @param $component
     */
    protected static function triggerBeforeInitialize($component)
    {
        if ($component->getStatus() == Component::STATUS_READY) {
            $component->onBeforeInitialize();
        }
    }

    /**
     * 触发后置处理事件
     * @param $component
     */
    protected static function triggerAfterInitialize($component)
    {
        if ($component->getStatus() == Component::STATUS_RUNNING) {
            $component->onAfterInitialize();
        }
    }

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
            throw new \Mix\Exceptions\DebugException(\Mix::$app->response->content);
        }
    }

    /**
     * 终止程序
     * @param string $content
     */
    public function end($content = '')
    {
        throw new \Mix\Exceptions\EndException($content);
    }

}
