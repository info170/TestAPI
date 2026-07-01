<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WrongRequestException extends MyException
{
    protected $code = 400;

}