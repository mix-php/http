<?php

namespace Mix\Http;

use Mix\Core\Component\AbstractComponent;
use Mix\Core\Component\ComponentInterface;
use Mix\Http\View;

/**
 * Error类
 * @author liu,jian <coder.keda@gmail.com>
 */
class Error extends AbstractComponent
{

    /**
     * 协程模式
     * @var int
     */
    public static $coroutineMode = ComponentInterface::COROUTINE_MODE_REFERENCE;

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
            self::log($errors);
        }
        // 发送客户端
        self::send($errors);
    }

    /**
     * 写入日志
     * @param $errors
     */
    protected static function log($errors)
    {
        // 构造消息
        $message = <<<EOL
{message}
[type] {type} [code] {code}
[file] {file} [line] {line}
[trace] {trace}
EOL;
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
        $format                          = \Mix::$app->error->format;
        $tpl                             = [
            404 => "errors.{$format}.not_found",
            500 => "errors.{$format}.internal_server_error",
        ];
        $content                         = (new View())->render($tpl[$statusCode], $errors);
        \Mix::$app->response->statusCode = $statusCode;
        \Mix::$app->response->content    = $content;
        switch ($format) {
            case self::FORMAT_HTML:
                \Mix::$app->response->format = \Mix\Http\Message\Response\HttpResponse::FORMAT_HTML;
                break;
            case self::FORMAT_JSON:
                \Mix::$app->response->format = \Mix\Http\Message\Response\HttpResponse::FORMAT_JSON;
                break;
            case self::FORMAT_XML:
                \Mix::$app->response->format = \Mix\Http\Message\Response\HttpResponse::FORMAT_XML;
                break;
        }
        \Mix::$app->response->send();
    }

}
