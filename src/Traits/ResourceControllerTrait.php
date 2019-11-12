<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Traits;

/**
 * 资源控制器方法
 * @package Jmhc\Restful\Traits
 */
trait ResourceControllerTrait
{
    public $service;

    public function index()
    {
        $this->service->updateAttribute()->index();
    }

    public function show(string $id = '')
    {
        if (empty($this->request->params['id'])) {
            $this->request->params['id'] = $id;
        }

        $this->service->updateAttribute()->show();
    }

    public function store()
    {
        $this->service->updateAttribute()->store();
    }

    public function update(string $id = '')
    {
        if (empty($this->request->params['id'])) {
            $this->request->params['id'] = $id;
        }

        $this->service->updateAttribute()->update();
    }

    public function destroy(string $id = '')
    {
        if (empty($this->request->params['id'])) {
            $this->request->params['id'] = $id;
        }

        $this->service->updateAttribute()->destroy();
    }
}
