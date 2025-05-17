<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];
    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];

    public function register(): void
    {
        $this->reportable(fn (Throwable $e) => null);

        $this->renderable(function (QueryException $e) {
            if ($e->getCode() !== '23000') {
                return null;
            }

            $map = [
                'users_email_unique' => ['email' => 'Ten e-mail już istnieje w systemie.'],
                'users_phone_unique' => ['phone' => 'Ten numer telefonu już istnieje w systemie.'],
            ];

            foreach ($map as $constraint => $message) {
                if (str_contains($e->getMessage(), $constraint)) {
                    throw ValidationException::withMessages($message);
                }
            }

            return null;
        });
    }
}