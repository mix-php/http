<?php

namespace Mix\Http\Middleware;

/**
 * Interface MiddlewareInterface
 * @author LIUJIAN <coder.keda@gmail.com>
 * @package Mix\Http\Middleware
 */
interface MiddlewareInterface
{

    /**
     * 处理
     * @param callable $callback
     * @param \Closure $next
     * @return mixed
     */
    public function handle(callable $callback, \Closure $next);

}
