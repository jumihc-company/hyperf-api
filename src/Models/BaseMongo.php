<?php
/**
 * User: YL
 * Date: 2019/11/27
 */

namespace Jmhc\Restful\Models;

use Hyperf\Utils\ApplicationContext;
use Jmhc\Mongodb\Eloquent\Model;
use Jmhc\Restful\Contracts\ConstAttributeInterface;
use Jmhc\Restful\Scopes\PrimaryKeyDescScope;
use Jmhc\Restful\Traits\ModelTrait;

/**
 * 基础 mongo 模型
 * @method ModelTrait initialize()
 * @package Jmhc\Restful\Models
 */
class BaseMongo extends Model implements ConstAttributeInterface
{
    use ModelTrait;

    /**
     * 关闭属性保护
     * @var bool
     */
    protected static $unguarded = true;

    protected function initializeBefore()
    {
        // 设置表名称
        if (empty($this->table)) {
            $this->setTable(static::getSnakePluralName());
        }
    }

    protected function boot(): void
    {
        parent::boot();

        static::addGlobalScope(
            ApplicationContext::getContainer()->get(PrimaryKeyDescScope::class)
        );
    }

    public function getForeignKey()
    {
        return static::getSnakeSingularName() . '_' . ltrim($this->getKeyName(), '_');
    }
}
