<?php

namespace Mix\Http;

use Mix\Component\AbstractComponent;
use Mix\Component\ComponentInterface;
use Mix\Http\View;

/**
 * Class Error
 * @package Mix\Http
 * @author liu,jian <coder.keda@gmail.com>
 */
class Error extends AbstractComponent
{

    /**
     * 协程模式
     * @var int
     */
    const COROUTINE_MODE = ComponentInterface::COROUTINE_MODE_REFERENCE;

    /**
     * 格式值
     */
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    /**
     * 输出格式
     * @var string
     */
    public $format = self::FORMAT_HTML;

    /**
     * 错误级别
     * @var int
     */
    public $level = E_ALL;

    /**
     * 异常处理
     * @param $e
     */
    public function handleException($e)
    {
        // 错误参数定义
        $statusCode = $e instanceof \Mix\Exception\NotFoundException ? 404 : 500;
        $errors     = [
            'status'  => $statusCode,
            'code'    => $e->getCode(),
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'type'    => get_class($e),
            'trace'   => $e->getTraceAsString(),
        ];
        // 日志处理
        if (!($e instanceof \Mix\Exception\NotFoundException)) {
            static::log($errors);
        }
        // 发送客户端
        static::send($errors);
    }

    /**
     * 写入日志
     * @param $errors
     */
    protected static function log($errors)
    {
        // 构造消息
        $message = "{message}\n[code] {code} [type] {type}\n[file] {file} [line] {line}\n[trace] {trace}";
        if (!\Mix::$app->appDebug) {
            $message = "{message} [{code}] {type} in {file} line {line}";
        }
        // 写入
        $level = \Mix\Core\Error::getLevel($errors['code']);
        switch ($level) {
            case 'error':
                \Mix::$app->log->error($message, $errors);
                break;
            case 'warning':
                \Mix::$app->log->warning($message, $errors);
                break;
            case 'notice':
                \Mix::$app->log->notice($message, $errors);
                break;
        }
    }

    /**
     * 发送客户端
     * @param $errors
     */
    protected static function send($errors)
    {
        $statusCode = $errors['status'];
        if (!\Mix::$app->appDebug) {
            if ($statusCode == 404) {
                $errors = [
                    'status'  => 404,
                    'message' => $errors['message'],
                ];
            }
            if ($statusCode == 500) {
                $errors = [
                    'status'  => 500,
                    'message' => '服务器内部错误',
                ];
            }
        }
        \Mix::$app->response->statusCode = $statusCode;
        switch (\Mix::$app->error->format) {
            case static::FORMAT_HTML:
                \Mix::$app->response->content = static::html($errors);
                \Mix::$app->response->format  = \Mix\Http\Message\Response\HttpResponse::FORMAT_HTML;
                break;
            case static::FORMAT_JSON:
                \Mix::$app->response->content = static::json($errors);
                \Mix::$app->response->format  = \Mix\Http\Message\Response\HttpResponse::FORMAT_JSON;
                break;
            case static::FORMAT_XML:
                \Mix::$app->response->content = static::xml($errors);
                \Mix::$app->response->format  = \Mix\Http\Message\Response\HttpResponse::FORMAT_XML;
                break;
        }
        \Mix::$app->response->send();
    }

    /**
     * 生成html
     * @param $errors
     * @return string
     */
    protected static function html($errors)
    {
        $tpl = [
            404 => "errors.not_found",
            500 => "errors.internal_server_error",
        ];
        return (new View())->render($tpl[$errors['status']], $errors);
    }

    /**
     * 生成json
     * @param $errors
     * @return string
     */
    protected static function json($errors)
    {
        // 转换trace格式
        if (isset($errors['trace'])) {
            $tmp = [];
            foreach (explode("\n", $errors['trace']) as $key => $item) {
                $tmp[strstr($item, ' ', true)] = trim(strstr($item, ' '));
            }
            $errors['trace'] = $tmp;
        }
        // 生成
        return \Mix\Helper\JsonHelper::encode($errors, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 生成xml
     * @param $errors
     * @return string
     */
    protected static function xml($errors)
    {
        // 转换trace格式
        if (isset($errors['trace'])) {
            $tmp = [];
            foreach (explode("\n", $errors['trace']) as $key => $item) {
                $tmp['item' . substr(strstr($item, ' ', true), 1)] = trim(strstr($item, ' '));
            }
            $errors['trace'] = $tmp;
        }
        // 生成
        return \Mix\Helper\XmlHelper::encode($errors);
    }

}
