<?php

namespace Mix\Http;

/**
 * Interface MiddlewareInterface
 * @author LIUJIAN <coder.keda@gmail.com>
 * @package Mix\Http
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
