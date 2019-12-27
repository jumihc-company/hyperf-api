<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Handlers;

use Error;
use ErrorException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Database\Exception\QueryException;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\ValidationException;
use Jmhc\Restful\Exceptions\ResultException;
use Jmhc\Restful\ResultCode;
use Jmhc\Restful\ResultMsg;
use Jmhc\Restful\Utils\Cipher;
use Jmhc\Restful\Utils\LogHelper;
use Jmhc\Restful\Utils\RedisLock;
use Jmhc\Restful\Utils\Token;
use Jmhc\Sms\Exceptions\SmsException;
use LogicException;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;
use RuntimeException;
use Throwable;

/**
 * 异常处理
 * @package Jmhc\Restful\Handlers
 */
class ExceptionHandler extends \Hyperf\ExceptionHandler\ExceptionHandler
{
    protected $code = ResultCode::ERROR;
    protected $msg = ResultMsg::ERROR;
    protected $data;
    protected $debug;

    protected $httpCode = ResultCode::HTTP_ERROR_CODE;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Hyperf\HttpServer\Contract\ResponseInterface
     */
    protected $response;

    /**
     * @var ConfigInterface
     */
    protected $configInterface;

    /**
     * @var Cipher
     */
    protected $cipher;

    /**
     * @var Token
     */
    protected $token;

    public function __construct(
        RequestInterface $request,
        \Hyperf\HttpServer\Contract\ResponseInterface $response,
        ConfigInterface $configInterface,
        Cipher $cipher,
        Token $token
    )
    {
        $this->request = $request;
        $this->response = $response;
        $this->configInterface = $configInterface;
        $this->cipher = $cipher;
        $this->token = $token;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        // 重置属性
        $this->resetProperty();

        // 设置响应数据
        $this->response($throwable);

        // 响应数据
        $res = [
            'code' => $this->code,
            'msg' => $this->msg,
            'data' => $this->data,
        ];
        // 如果是调试模式
        if ($this->configInterface->get('jmhc-api.exception_debug', true)) {
            $res['debug'] = $this->debug;
        }
        // 响应处理
        $res = $this->responseHandler($res);

        // 响应header
        $headers = [];

        // 判断刷新的token是否存在
        if(! empty($this->request->refreshToken)) {
            // 刷新token
            $this->refreshToken($this->request, $this->request->refreshToken, $headers);
            // 单设备登录操作
            $this->sdlHandler($this->request, $this->request->refreshToken);
        }

        // 解除请求锁定
        $this->unRequestLocke($this->request);

        // 设置响应头
        foreach ($headers as $k => $v)
        {
            $this->response = $this->response->withHeader($k, $v);
        }

        return $this->response
            ->withStatus($this->httpCode)
            ->json($res);
    }

    /**
     * Determine if the current exception handler should handle the exception,.
     *
     * @param Throwable $throwable
     * @return bool
     *              If return true, then this exception handler will handle the exception,
     *              If return false, then delegate to next handler
     */
    public function isValid(Throwable $throwable): bool
    {
        return true;
    }

    /**
     * 重置属性
     */
    protected function resetProperty()
    {
        $this->code = ResultCode::ERROR;
        $this->msg = ResultMsg::ERROR;
        $this->data = null;
        $this->debug = null;
        $this->httpCode = ResultCode::HTTP_ERROR_CODE;
    }

    protected function response(Throwable $e)
    {
        // 调试信息
        $this->debug = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'message' => $e->getMessage(),
        ];

        if ($e instanceof ResultException) {
            // 返回异常
            $this->code = $e->getCode();
            $this->msg = $e->getMessage();
            $this->data = $e->getData();
            $this->httpCode = $e->getHttpCode();
        } elseif ($e instanceof QueryException) {
            // 数据库异常
            $this->code = ResultCode::SYS_EXCEPTION;
            $this->msg = ResultMsg::SYS_EXCEPTION;
            LogHelper::throwableSave(
                $this->configInterface->get('jmhc-api.db_exception_file_name', 'handle_db.exception'),
                $e
            );
        } elseif ($e instanceof ValidationException) {
            // 验证器异常
            $this->msg = $e->validator->errors()->first();
        } elseif ($e instanceof InvalidArgumentException || $e instanceof NoGatewayAvailableException) {
            // easySms 短信异常
            LogHelper::get()->save(
                $this->configInterface->get('jmhc-api.sms_exception_file_name', 'sms.exception'),
                $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL . json_encode($e->getExceptions(), JSON_UNESCAPED_UNICODE)
            );
        } elseif ($e instanceof SmsException) {
            // 短信异常
            $this->msg = $e->getMessage();
            $this->data = $e->getData();
        } elseif ($e instanceof ReflectionException || $e instanceof LogicException || $e instanceof RuntimeException) {
            // 反射、逻辑、运行异常
            $this->code = ResultCode::SYS_EXCEPTION;
            $this->msg = ResultMsg::SYS_EXCEPTION;
            LogHelper::throwableSave(
                $this->configInterface->get('jmhc-api.exception_file_name', 'handle.exception'),
                $e
            );
        } elseif ($e instanceof Error || $e instanceof ErrorException) {
            // 发生错误
            $this->code = ResultCode::SYS_ERROR;
            $this->msg = ResultMsg::SYS_ERROR;
            LogHelper::throwableSave(
                $this->configInterface->get('jmhc-api.error_file_name', 'handle.error'),
                $e
            );
        }
    }

    /**
     * 响应处理
     * @param array $res
     * @return array|string
     */
    protected function responseHandler(array $res)
    {
        try {
            $res = $this->cipher->response($res);
        } catch (Throwable $e) {}

        return $res;
    }

    /**
     * 刷新token
     * @param $request
     * @param string $token
     * @param array $headers
     */
    protected function refreshToken($request, string $token, array &$headers)
    {
        $headers['Refresh-Token'] = $token;
    }

    /**
     * 单设备登录操作
     * @param $request
     * @param string $token
     */
    protected function sdlHandler($request, string $token)
    {}

    /**
     * 解除请求锁定
     * @param $request
     */
    protected function unRequestLocke($request)
    {
        if ($this->code != ResultCode::REQUEST_LOCKED &&
            $request->requestLock instanceof RedisLock) {
            $request->requestLock->release();
        }
    }
}
