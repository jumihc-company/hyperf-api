<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\HttpServer\Contract\RequestInterface;
use Jmhc\Restful\Contracts\VersionInterface;
use Jmhc\Restful\ResultCode;
use Jmhc\Restful\ResultMsg;
use Jmhc\Restful\Traits\ResultThrowTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 检测版本中间件
 * @package Jmhc\Restful\Middleware
 */
class CheckVersionMiddleware implements MiddlewareInterface
{
    use ResultThrowTrait;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var VersionInterface
     */
    protected $versionModel;

    public function __construct(
        RequestInterface $request,
        VersionInterface $versionModel
    )
    {
        $this->request = $request;
        $this->versionModel = $versionModel;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 当前版本
        $version = $this->getVersion('version');

        // 判断版本号是否存在
        if (empty($version)) {
            $this->error('版本号不存在');
        }

        // 验证版本
        $info = $this->versionModel->getLastInfo();
        if (! empty($info)) {
            if ($version < $info->code && $info->is_force) {
                $this->error(ResultMsg::OLD_VERSION, ResultCode::OLD_VERSION, [
                    'content' => $info->content,
                    'url' => $info->url,
                ]);
            }
        }

        return $handler->handle($this->request);
    }

    /**
     * 获取version
     * @param string $name
     * @return array|string|null
     */
    protected function getVersion(string $name)
    {
        $version = $this->request->header($name, 0);
        if (empty($version)) {
            $version = $this->request->input($name, 0);
        }

        return $version;
    }
}
