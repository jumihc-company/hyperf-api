<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Traits;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Jmhc\Restful\PlatformInfo;
use Jmhc\Restful\Utils\Collection;
use Jmhc\Restful\Utils\Helper;
use Psr\Container\ContainerInterface;

/**
 * 请求信息
 * @package Jmhc\Restful\Traits
 */
trait RequestInfoTrait
{
    /**
     * @Inject()
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject()
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject()
     * @var ResponseInterface
     */
    protected $response;

    /**
     * 请求参数
     * @var Collection
     */
    protected $params;

    /**
     * 请求ip
     * @var string
     */
    protected $ip;

    /**
     * 请求平台 PlatformInfo::other
     * @var string
     */
    protected $platform;

    /**
     * 设置请求信息
     * @return $this
     */
    public function setRequestInfo()
    {
        $this->params = new Collection($this->request->all());
        $this->ip = Helper::ip($this->request);
        $this->platform = $this->request->platform ?? PlatformInfo::OTHER;

        return $this;
    }
}
