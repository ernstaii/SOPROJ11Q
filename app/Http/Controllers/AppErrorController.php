<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppErrorRequest;
use App\Models\AppError;

class AppErrorController extends Controller
{
    public function store(StoreAppErrorRequest $request) {
        AppError::create([
            'error_id' => $request->error_id,
            'message' => $request->message,
            'stacktrace' => $request->stacktrace
        ]);
    }
}
