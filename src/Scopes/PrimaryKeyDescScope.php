<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Scopes;

use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Scope;

/**
 * 主键倒序作用域
 * @package Jmhc\Restful\Scopes
 */
class PrimaryKeyDescScope implements Scope
{
    /**
     * Apply the scope to a given Model query builder.
     *
     * @param Builder $builder
     * @param Model $model
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->orderByDesc($model->getKeyName());
    }
}
