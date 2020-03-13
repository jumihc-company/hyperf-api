<?php
/**
 * User: YL
 * Date: 2020/03/13
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\Utils\Str;
use Jmhc\Restful\Exceptions\MaintenanceModeException;
use Jmhc\Restful\ResultMsg;
use Jmhc\Restful\Utils\Helper;
use Jmhc\Restful\Utils\IpUtils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CheckForMaintenanceModeMiddleware implements MiddlewareInterface
{
    const FILE_NAME = 'runtime/down.cache';

    /**
     * 在启用维护模式时应该可以访问的uri
     * @var array
     */
    protected $except = [];

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (file_exists(base_path(self::FILE_NAME))) {
            $data = json_decode(file_get_contents(base_path(self::FILE_NAME)), true);

            if (isset($data['allowed']) && IpUtils::checkIp(Helper::ip($request), (array) $data['allowed'])) {
                return $handler->handle($request);
            }

            if ($this->inExceptArray($request)) {
                return $handler->handle($request);
            }

            throw new MaintenanceModeException($data['message'] ?? ResultMsg::MAINTENANCE);
        }

        return $handler->handle($request);
    }

    /**
     * 是否排除当前url
     * @param $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        $url = $request->fullUrl();
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if (Str::is($except, $url) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
