<?php
/**
 * User: YL
 * Date: 2019/11/21
 */

namespace Jmhc\Restful\Listener;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Validation\Event\ValidatorFactoryResolved;
use Jmhc\Restful\Rules\ImagesRule;

class ValidatorFactoryResolvedListener implements ListenerInterface
{
    /**
     * @Inject()
     * @var ImagesRule
     */
    protected $imagesRule;

    public function listen(): array
    {
        return [
            ValidatorFactoryResolved::class,
        ];
    }

    public function process(object $event)
    {
        /**  @var ValidatorFactoryInterface $validatorFactory */
        $validatorFactory = $event->validatorFactory;

        // 注册了 images 验证器
        $validatorFactory->extend('images', function ($attribute, $value, $parameters, $validator) {
            return $this->imagesRule->handle($attribute, $value, $parameters, $validator);
        }, $this->imagesRule->message());

        // 注册其他验证器
        $this->registerOtherRules($validatorFactory);
    }

    /**
     * 注册其他验证器
     * @param ValidatorFactoryInterface $validatorFactory
     */
    protected function registerOtherRules(ValidatorFactoryInterface $validatorFactory)
    {}
}
