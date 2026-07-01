<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SlotIsFullException extends MyException
{
    protected $code = 409;
}