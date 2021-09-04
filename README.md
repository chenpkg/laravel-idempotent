<h1 align="left"> Idempotent </h1>

<p align="left"> laravel 幂等中间件，防止客户端同一时间请求多次。</p>
<p align="left">建议将 laravel 默认缓存设置为 redis，将得到更好的性能。</p>

## Requirement

1. PHP >= 7.0 | PHP >= 8.0
2. laravel >= 6

## Installation

```shell
$ composer require chenpkg/laravel-idempotent
```

## Usage

发布配置文件

```
$ php artisan vendor:publish --tag="laravel-idempotent"
```

中间件为 `Chenpkg\Idempotent\IdempotentMiddleware`，别名 `idempotent`

```php
Route::group(['middleware' => 'idempotent'], function () {
    //...
});
```

> 注：请不要直接加在 `App\Http\Kernel` 的 `middleware` 里面，由于中间件执行顺序问题，可能导致该组件获取不到当前用户身份标识符 ID

或者你可以将它加入到指定路由中间件组中

```php
protected $middlewareGroups = [
    // ...
    'api' => [
        'idempotent',
        'throttle:api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];
```

重复的请求将会抛出 `Chenpkg\Idempotent\Exceptions\RepeatRequestException` `Http` 异常

## Configure


```php
// config/idempotent.php

return [
    // true 自动获取唯一key, false 前端提供
    'forcible' => true,

    // 需要过滤重复请求的请求类型
    'methods' => ['POST', 'PUT', 'PATCH'],

    // 缓存有效时间/秒，防止死锁
    'seconds' => 10,

    // 获取当前用户
    'resolve_user' => function (\Illuminate\Http\Request $request) {
        return auth()->user();
    },

    // 前端提供 key 请求头名称.
    'header_name' => 'Idempotent-Key',
];
```


## License

MIT