<?php

namespace App\Exceptions;

use Exception;

class HttpException extends Exception
{
    protected $code = 400;
}