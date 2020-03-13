## 目录

- [安装配置](#%E5%AE%89%E8%A3%85%E9%85%8D%E7%BD%AE)
- [使用说明](#%E4%BD%BF%E7%94%A8%E8%AF%B4%E6%98%8E)
	- [快速使用](#%E5%BF%AB%E9%80%9F%E4%BD%BF%E7%94%A8)
		- [中间件](#%E4%B8%AD%E9%97%B4%E4%BB%B6)
		- [异常处理](#%E5%BC%82%E5%B8%B8%E5%A4%84%E7%90%86)
		- [控制器](#%E6%8E%A7%E5%88%B6%E5%99%A8)
		- [模型](#%E6%A8%A1%E5%9E%8B)
		- [服务层(逻辑层)](#%E6%9C%8D%E5%8A%A1%E5%B1%82%E9%80%BB%E8%BE%91%E5%B1%82)
	- [控制器](#%E6%8E%A7%E5%88%B6%E5%99%A8-1)
	- [模型](#%E6%A8%A1%E5%9E%8B-1)
	    - [普通模型](#%E6%99%AE%E9%80%9A%E6%A8%A1%E5%9E%8B)
	    - [中间表模型](#%E4%B8%AD%E9%97%B4%E8%A1%A8%E6%A8%A1%E5%9E%8B)
	    - [mongodb模型](#mongodb%E6%A8%A1%E5%9E%8B)
	- [服务层(逻辑层)](#%E6%9C%8D%E5%8A%A1%E5%B1%82%E9%80%BB%E8%BE%91%E5%B1%82-1)
	- [命令行](#%E5%91%BD%E4%BB%A4%E8%A1%8C)
	    - [创建控制器](#%E5%88%9B%E5%BB%BA%E6%8E%A7%E5%88%B6%E5%99%A8)
	    - [创建服务层(逻辑层)](#%E5%88%9B%E5%BB%BA%E6%9C%8D%E5%8A%A1%E5%B1%82%E9%80%BB%E8%BE%91%E5%B1%82)
	    - [创建模型](#%E5%88%9B%E5%BB%BA%E6%A8%A1%E5%9E%8B)
	    - [通过文件创建所需文件](#%E9%80%9A%E8%BF%87%E6%96%87%E4%BB%B6%E5%88%9B%E5%BB%BA%E6%89%80%E9%9C%80%E6%96%87%E4%BB%B6)
	    - [生成工厂文件](%23%E7%94%9F%E6%88%90%E5%B7%A5%E5%8E%82%E6%96%87%E4%BB%B6)
	    - [维护模式](%23%E7%BB%B4%E6%8A%A4%E6%A8%A1%E5%BC%8F)
	- [中间件](#%E4%B8%AD%E9%97%B4%E4%BB%B6-1)
	- [异常处理](#%E5%BC%82%E5%B8%B8%E5%A4%84%E7%90%86-1)
	- [验证规则](#%E9%AA%8C%E8%AF%81%E8%A7%84%E5%88%99)
	    - [Images](#images)
	- [模型作用域](#%E6%A8%A1%E5%9E%8B%E4%BD%9C%E7%94%A8%E5%9F%9F)
	    - [主键字段倒序](#%E4%B8%BB%E9%94%AE%E5%AD%97%E6%AE%B5%E5%80%92%E5%BA%8F)
	- [trait介绍](#trait%E4%BB%8B%E7%BB%8D)
	    - [InstanceTrait.php](#instancetraitphp)
		- [ModelTrait.php](#modeltraitphp)
		- [RequestInfoTrait.php](#requestinfotraitphp)
		- [ResultThrowTrait.php](#resultthrowtraitphp)
		- [UserInfoTrait.php](#userinfotraitphp)
	- [工具类介绍](#%E5%B7%A5%E5%85%B7%E7%B1%BB%E4%BB%8B%E7%BB%8D)
	    - [Collection.php](#collectionphp)
	    - [DbHelper.php](#dbhelperphp)
	    - [Dispatch.php](#dispatchphp)
	    - [FileSize.php](#filesizephp)
	    - [Log.php](#logphp)
	    - [Sdl.php](#sdlphp)
	    - [Token.php](#tokenphp)

## 安装配置

使用以下命令安装：
```
composer require jmhc/hyperf-api
```
发布文件：
```php
// 发布所有文件
php bin/hyperf.php vendor:publish jmhc/hyperf-api
// 发布 Translation 组件的文件
php bin/hyperf.php vendor:publish hyperf/translation
// 发布验证器组件的文件
php bin/hyperf.php vendor:publish hyperf/validation
```

## 使用说明

> 环境变量值参考：[env](docs/ENV.md)
> 
> restful参考: [restful](https://github.com/jumihc-company/laravel-api/blob/master/docs/RESTFUL.md)

### 快速使用

1. [安装](#%E5%AE%89%E8%A3%85%E9%85%8D%E7%BD%AE)
2. [发布配置](#%E5%AE%89%E8%A3%85%E9%85%8D%E7%BD%AE)
3. [注册中间件](#%E4%B8%AD%E9%97%B4%E4%BB%B6)
4. [继承异常处理程序](#%E5%BC%82%E5%B8%B8%E5%A4%84%E7%90%86)

#### 中间件
- 必须注册全局中间件 `Jmhc\Restful\Middleware\ParamsHandlerMiddleware`
- 可选中间件查看 [中间件列表](#%E4%B8%AD%E9%97%B4%E4%BB%B6-1)

#### 异常处理

- 修改 `App\Exceptions\Handler` 继承的方法为  `Jmhc\Restful\Handlers\ExceptionHandler`
- 其他异常捕获调用父类 `response()`  方法并重写，参考 `Jmhc\Restful\Handlers\ExceptionHandler->response()`

#### 控制器

- 直接继承 `Jmhc\Restful\Controllers\BaseController`

#### 模型

- 可选继承 `Jmhc\Restful\Models\BaseModel` 、 `Jmhc\Restful\Models\BasePivot` 、 `Jmhc\Restful\Models\UserModel`  、`Jmhc\Restful\Models\VersionModel`

#### 服务层(逻辑层)

- 直接继承 `Jmhc\Restful\Services\BaseService`

### 控制器

> 需继承 `Jmhc\Restful\Controllers\BaseController`

- 可使用 `Jmhc\Restful\Traits\RequestInfoTrait` 里的参数
- 可使用 `Jmhc\Restful\Traits\UserInfoTrait` 里的参数、方法
- 可使用 `Jmhc\Restful\Traits\ResourceController` 里的方法

### 模型

#### 普通模型

> 需继承 `Jmhc\Restful\Models\BaseModel`

- 可使用 `Jmhc\Restful\Traits\ModelTrait` 里的方法

#### 中间表模型

> 需继承 `Jmhc\Restful\Models\BasePivot`

- 可使用 `Jmhc\Restful\Traits\ModelTrait` 里的方法

#### mongodb模型

> 需继承 `Jmhc\Restful\Models\BaseMongo`
> 
> 使用前需配置，参考 [配置](https://github.com/jumihc-company/mongodb#%E9%85%8D%E7%BD%AE)

- 可使用 `Jmhc\Restful\Traits\ModelTrait` 里的方法

### 服务层(逻辑层)

> 需继承 `Jmhc\Restful\Services\BaseService`

- 可使用 `Jmhc\Restful\Traits\RequestInfoTrait` 里的参数
- 可使用 `Jmhc\Restful\Traits\UserInfoTrait` 里的参数、方法
- 可使用 `Jmhc\Restful\Traits\ResourceService` 里的方法

```php
class TestController extends BaseController
{
    /**
     * @Inject()
     * @var TestService
     */
    protected $service;
    
    public function index()
    {
    	$this->request->params->a = 'a';
    	// 当初始化实例化service后，方法中有更新$this->request->params时,应当调用服务层updateAttribute方法更新$this->request->params
    	$this->service->updateAttribute()->index();
    }
    
    public function index()
    {
    	// 当初始化实例化service后，方法中无更新$this->request->params
    	$this->service->index();
    }
}
```

### 命令行

#### 创建控制器

> 创建的控制器默认继承基础控制器 BaseController
>
> `--controller-extends-custom` 参数修改继承基础控制器

```php
// 创建 Test 控制器位于 app/Http/Controllers/Test.php
php bin/hyperf.php jmhc-api:make-controller test
// 创建 Test 控制器修改继承父类
php bin/hyperf.php jmhc-api:make-controller test --controller-extends-custom App/BaseController
// 创建 Test 控制器并添加后缀，位于 app/Http/Controllers/TestController.php
php bin/hyperf.php jmhc-api:make-controller test -s
// 创建 Test 控制器位于 app/Http/Index/Controllers/Test.php
php bin/hyperf.php jmhc-api:make-controller test -m index
...
```

#### 创建服务层(逻辑层)

> 创建的服务默认继承基础服务 BaseService
>
> `--service-extends-custom` 参数修改继承基础服务

```php
// 创建 Test 服务位于 app/Http/Services/Test.php
php bin/hyperf.php jmhc-api:make-service test
// 创建 Test 服务修改继承父类
php bin/hyperf.php jmhc-api:make-service test --service-extends-custom App\BaseService
// 创建 Test 服务并添加后缀，位于 app/Http/Services/TestService.php
php bin/hyperf.php jmhc-api:make-service test -s
// 创建 Test 服务位于 app/Http/Index/Services/Test.php
php bin/hyperf.php jmhc-api:make-service test -m index
...
```

#### 创建模型

> 不传 name 将会从数据库读取所有表创建
>
> `--model-extends-custom` 参数修改继承基础模型

```php
// 创建公用模型位于 app/Common/Models 并排除 test，foos 表
php bin/hyperf.php jmhc-api:make-model --dir Common/Models -t test -t foos
// 创建 Test 模型位于 app/Http/Models/Test.php
php bin/hyperf.php jmhc-api:make-model test
// 创建 Test 模型修改继承父类
php bin/hyperf.php jmhc-api:make-model test --model-extends-custom App\BaseModel
// 创建 Test 服务并添加后缀，位于 app/Http/Models/TestModel.php
php bin/hyperf.php jmhc-api:make-model test -s
// 创建 Test 模型位于 app/Http/Index/Models/Test.php
php bin/hyperf.php jmhc-api:make-model test -m index
...
```

#### 通过文件创建所需文件

> 此命令通过 `config('jmhc-build-file')` 获取需要创建的文件名称
>
> 使用 `*-extends-custom` 修改对应继承父类

```php
// 生成控制器、模型、服务、迁移、填充
php bin/hyperf.php jmhc-api:make-with-file --controller --model --service --migration
// 覆盖生成所有文件
php bin/hyperf.php jmhc-api:make-with-file -f
// 覆盖生成控制器
php bin/hyperf.php jmhc-api:make-with-file --force-controller
...
```

#### 生成工厂文件

```php
// 通过指定目录创建factory,位于 app/Http/Common/Factory/Service.php
php bin/hyperf.php jmhc-api:make-factory service --scan-dir Http/Services --scan-dir Http/Index/Services

// 通过指定目录创建factory,并增加后缀、保存至其他路径,位于 app/Http/Commons/Factory/ServiceFactory.php
php bin/hyperf.php jmhc-api:make-factory service --scan-dir Http/Services --dir Commons/Factory -s
...
```

#### 维护模式
```php
// 开启维护模式
php bin/hyperf.php jmhc-api:down

// 开启维护模式，并设置维护消息
php bin/hyperf.php jmhc-api:down --message 服务器维护中

// 关闭维护模式
php bin/hyperf.php jmhc-api:up
```

### 中间件

> 用法加粗为必须调用

|   中间件    |   用法   |   需要实现的契约或继承模型   |
| ---- | ---- | ---- |
| `Jmhc\Restful\Middleware\CorsMiddleware` | 允许跨域 | --- |
| `Jmhc\Restful\Middleware\ParamsHandlerMiddleware` | **参数处理** | --- |
| `Jmhc\Restful\Middleware\ConvertEmptyStringsToNullMiddleware` | 转换空字符串为null | --- |
| `Jmhc\Restful\Middleware\TrimStringsMiddleware` | 清除字符串空格 | --- |
| `Jmhc\Restful\Middleware\RequestLockMiddleware` | 请求锁定 | --- |
| `Jmhc\Restful\Middleware\RequestLogMiddleware` | 记录请求日志(debug) | --- |
| `Jmhc\Restful\Middleware\RequestPlatformMiddleware` | 设置请求平台，参考`Jmhc\Restful\PlatformInfo` | --- |
| `Jmhc\Restful\Middleware\CheckVersionMiddleware` | 检测应用版本 | `Jmhc\Restful\Contracts\VersionModelInterface`<br />`Jmhc\Restful\Models\VersionModel` |
| `Jmhc\Restful\Middleware\CheckSignatureMiddleware` | 验证请求签名 | --- |
| `Jmhc\Restful\Middleware\CheckTokenMiddleware` | 检测token，设置用户数据 | `Jmhc\Restful\Contracts\UserModelInterface`<br />`Jmhc\Restful\Models\UserModel` |
| `Jmhc\Restful\Middleware\CheckSdlMiddleware` | 单设备登录，需要复写 `Jmhc\Restful\Handlers\ExceptionHandler->sdlHandler()` | --- |
| `Jmhc\Restful\Middleware\CheckForMaintenanceModeMiddleware` | 检测维护模式 | 以应用在全局中间件 |

### 异常处理

> `App\Exceptions\Handler` 继承 `Jmhc\Restful\Handlers\ExceptionHandler`
>
> 其他异常捕获调用父类 `response()`  方法并重写，参考 `Jmhc\Restful\Handlers\ExceptionHandler->response()`

### 验证规则

#### Images

> `Jmhc\Restful\Rules\ImagesRule`

验证图片字段后缀地址为 `jpeg` , `jpg` , `png` , `bmp` , `gif` , `svg` , `webp`

如：

```php
1.png // true
1.pn // false
1.png,2.png // true
```

### 模型作用域

#### 主键字段倒序

> `Jmhc\Restful\Scopes\PrimaryKeyDescScope`

`Jmhc\Restful\Models\BaseModel` 已默认注册此全局作用域

### trait介绍

#### InstanceTrait.php

> `Jmhc\Restful\Traits\InstanceTrait`
>
> 单例类 trait

```php
// 无构造参数使用
T::getInstance()->a();

// 有构造参数使用，c为构造参数名称
T::getInstance([
    'c' => ['a']
])->a();
```

#### ModelTrait.php

> `Jmhc\Restful\Traits\ModelTrait`
>
> 模型辅助 trait

使用类:
- `Jmhc\Restful\Models\BaseModel`
- `Jmhc\Restful\Models\BasePivot`

#### RequestInfoTrait.php

> `Jmhc\Restful\Traits\RequestInfoTrait`
>
> 请求信息绑定

使用类:

- `Jmhc\Restful\Controllers\BaseController`
- `Jmhc\Restful\Services\BaseService`

#### ResultThrowTrait.php

> `Jmhc\Restful\Traits\ResultThrowTrait`
>
> 异常抛出辅助

#### UserInfoTrait.php

> `Jmhc\Restful\Traits\UserInfoTrait`
>
> 用户信息绑定

使用类:

- `Jmhc\Restful\Controllers\BaseController`
- `Jmhc\Restful\Services\BaseService`

### 工具类介绍

#### Collection.php

> `Jmhc\Restful\Utils\Collection`
>
> 集合，基于 `Illuminate\Support\Collection`

- 修改`__get` 魔术方法
- 新增`__set` , `__isset` , `__unset` 魔术方法

#### DbHelper.php

> `Jmhc\Restful\Utils\DbHelper`
>
> 数据库辅助方法

```php
// 返回所有表名
make(DbHelper::class)->getAllTables();

// 返回 mysql 链接下 users 表字段数据
make(DbHelper::class, [
    'name' => 'mysql'
])->getAllColumns('users');
```

#### Dispatch.php

> `Jmhc\Restful\Utils\Dispatch`
>
> 获取当前请求类

```php
// dispatch 实例
$dispatch = make(Dispatch::class, [
    'request' => $request,
]);
// 获取调用 class
$dispatch->getClass();
// 获取调用 method
$dispatch->getMethod();
```

#### FileSize.php

> `Jmhc\Restful\Utils\FileSize`
>
> 转换文件尺寸

```php
// 返回 2097152 字节
FileSize::get('2m');

// 返回 2147483648 字节
FileSize::get('2g');
```

#### Log.php

> `Jmhc\Restful\Utils\Log`
>
> 文件日志保存

- `debug` 日志受环境变量 `LOG_DEBUG` 控制

#### Sdl.php

> `Jmhc\Restful\Utils\Sdl`
>
> 单设备登录类

#### Token.php

> `Jmhc\Restful\Utils\Token`
>
> 令牌相关类
