<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Models;

use Hyperf\DbConnection\Model\Model;
use Hyperf\Utils\ApplicationContext;
use Jmhc\Restful\Contracts\ConstAttributeInterface;
use Jmhc\Restful\Scopes\PrimaryKeyDescScope;
use Jmhc\Restful\Traits\ModelTrait;

/**
 * 基础模型
 * @method ModelTrait initialize()
 * @package Jmhc\Restful\Models
 */
class BaseModel extends Model implements ConstAttributeInterface
{
    use ModelTrait;

    protected function initializeBefore()
    {
        // 设置当前表名
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
        return static::getSnakeSingularName() . '_' . $this->getKeyName();
    }
}
