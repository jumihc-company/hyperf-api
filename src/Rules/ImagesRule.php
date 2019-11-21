<?php
/**
 * User: YL
 * Date: 2019/11/21
 */

namespace Jmhc\Restful\Rules;

use Jmhc\Restful\Contracts\RuleInterface;

class ImagesRule implements RuleInterface
{
    protected $images = ['jpeg', 'jpg', 'png', 'bmp', 'gif', 'svg', 'webp'];

    public function handle($attribute, $value, $parameters, $validator): bool
    {
        $images = array_filter(explode(',', $value));
        if (empty($images)) {
            return false;
        }

        foreach ($images as $v) {
            if (! in_array(pathinfo($v, PATHINFO_EXTENSION), $this->images)) {
                return false;
            }
        }

        return true;
    }

    public function message(): string
    {
        return __('validation.images');
    }
}
