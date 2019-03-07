<?php

namespace Mix\Http\Middleware;

/**
 * Class MiddlewareHandler
 * @package Mix\Http\Middleware
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class MiddlewareHandler
{

    /**
     * 执行中间件
     * @param callable $callable
     * @param array $middlewares
     * @return mixed
     */
    public static function run(callable $callable, array $middlewares)
    {
        $item = array_shift($middlewares);
        if (empty($item)) {
            return call_user_func($callable, \Mix::$app->request, \Mix::$app->response);
        }
        return $item->handle($callable, function () use ($callable, $middlewares) {
            return self::run($callable, $middlewares);
        });
    }

    /**
     * 实例化中间件
     * @param string $namespace
     * @param array $middlewares
     * @return array
     */
    public static function newInstances(string $namespace, array $middlewares)
    {
        $instances = [];
        foreach ($middlewares as $key => $name) {
            $class = "{$namespace}\\{$name}Middleware";
            $object = new $class();
            if (!($object instanceof MiddlewareInterface)) {
                throw new \RuntimeException("{$class} type is not 'Mix\Http\Middleware\MiddlewareInterface'");
            }
            $instances[$key] = $object;
        }
        return $instances;
    }

}
