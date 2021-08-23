<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021-08-20 15:45
 */

namespace Chenpkg\Idempotent;

use Chenpkg\Idempotent\Exceptions\RepeatRequestException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IdempotentMiddleware
{
    protected $config;

    const PLACE_HOLDER = 'idempotent_place_holder';

    public function handle(Request $request, Closure $next)
    {
        $this->config = config('idempotent');

        if (! in_array($request->method(), $this->config['methods'])) {
            return $next($request);
        }

        $idempotentKey = $this->getIdempotentKey();

        if (! $idempotentKey) {
            return $next($request);
        }

        $this->repeated($idempotentKey);

        return $next($request);
    }

    /**
     * @param $request
     * @param $response
     */
    public function terminate($request, $response)
    {
        Cache::forget($this->getCacheKey($request->header($this->config['header_name'])));
    }

    /**
     * @param $key
     * @return string
     */
    protected function getCacheKey($key)
    {
        return 'idempotent_key:'.$key;
    }

    /**
     * @param $idempotentKey
     * @return true
     * @throws RepeatRequestException
     */
    protected function repeated($idempotentKey)
    {
        $value = Cache::get($this->getCacheKey($idempotentKey));

        if ($value == static::PLACE_HOLDER) {
            throw new RepeatRequestException('Your request is still being processed.');
        }

        $seconds = (int)$this->config['seconds'];
        $seconds = $seconds > 0 ? $seconds : null;

        Cache::put($this->getCacheKey($idempotentKey), static::PLACE_HOLDER, $seconds);

        return true;
    }

    /**
     * @return array|string|null
     */
    protected function getIdempotentKey()
    {
        return $this->config['forcible'] ? $this->generateIdempotentKey() : request()->header($this->config['header_name']);
    }

    /**
     * @return string
     */
    protected function generateIdempotentKey()
    {
        $user = $this->resolveUser();

        $idempotentKey = $user ? $user->getAuthIdentifier().request() : request()->ip().request();
        // $idempotentKey = sha1($idempotentKey);
        $idempotentKey = md5($idempotentKey);

        request()->headers->set(config('idempotent.header_name'), $idempotentKey);

        return $idempotentKey;
    }

    /**
     * @return mixed|null
     */
    protected function resolveUser()
    {
        $user = null;

        $resolveUser = $this->config['resolve_user'];

        if ($resolveUser instanceof Closure && $result = app()->call($resolveUser)) {
            $user = $result;
        }

        return $user;
    }
}