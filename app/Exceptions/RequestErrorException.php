<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestErrorException extends Exception
{
    public $message;
    public $status;
    public function __construct($message ='Your request cannot be processed', $status= 400) {
        $this->message = $message;
        $this->status = $status;
    }
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'success'   => false,
            'name' => 'Request error exception',
            'message'   => $this->message,
            'error_code' => $this->status,
            'error' => true,
        ])->setStatusCode($this->status);
    }
}
