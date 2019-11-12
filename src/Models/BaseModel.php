<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Models;

use Hyperf\Database\Model\Model;
use Jmhc\Restful\Contracts\ConstAttribute;
use Jmhc\Restful\Scopes\PrimaryKeyDescScope;
use Jmhc\Restful\Traits\ModelTrait;

/**
 * 基础模型
 * @method ModelTrait initialize()
 * @package Jmhc\Restful\Models
 */
class BaseModel extends Model implements ConstAttribute
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

        static::addGlobalScope(PrimaryKeyDescScope::class);
    }

    public function getForeignKey()
    {
        return static::getSnakeSingularName() . '_' . $this->getKeyName();
    }
}
