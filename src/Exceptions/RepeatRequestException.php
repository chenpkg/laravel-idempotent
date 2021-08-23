<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021-08-23 11:43
 */

namespace Chenpkg\Idempotent\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class RepeatRequestException extends HttpException
{
    public function __construct($message = null)
    {
        parent::__construct(423, $message);
    }
}