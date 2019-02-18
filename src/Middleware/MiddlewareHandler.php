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
     * @param array $instances
     * @return mixed
     */
    public static function run(callable $callable, array $instances)
    {
        $item = array_shift($instances);
        if (empty($item)) {
            return call_user_func($callable);
        }
        return $item->handle($callable, function () use ($callable, $instances) {
            return self::run($callable, $instances);
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
            $class  = "{$namespace}\\{$name}Middleware";
            $object = new $class();
            if (!($object instanceof MiddlewareInterface)) {
                throw new \RuntimeException("{$class} type is not 'Mix\Http\Middleware\MiddlewareInterface'");
            }
            $instances[$key] = $object;
        }
        return $instances;
    }

}
