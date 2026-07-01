<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotFoundException extends MyException
{
    protected $code = 404;

}