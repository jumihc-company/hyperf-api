<?php
/**
 * User: YL
 * Date: 2019/11/20
 */

namespace Jmhc\Restful\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 忽略验证令牌
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 * @package Jmhc\Restful\Annotation
 */
class IgnoreCheckToken extends AbstractAnnotation
{}
