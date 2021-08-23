<?php

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