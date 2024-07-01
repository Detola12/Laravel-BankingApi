<?php

namespace App\Traits;

trait HttpResponses
{
    protected function success($data, $message = null, $code = 200){
        return response()->json([
            'status' => 'Success',
            'message' => $message,
            'data' => $data
        ], $code);
    }
    protected function error($code, $message = null, ){
        return response()->json([
            'status' => 'Failed',
            'message' => $message,
        ], $code);
    }
}
