<?php

namespace Mix\Http;

/**
 * Class MiddlewareHandler
 * @package Mix\Http
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
                throw new \RuntimeException("{$class} type is not 'Mix\Http\MiddlewareInterface'");
            }
            $instances[$key] = $object;
        }
        return $instances;
    }

}
