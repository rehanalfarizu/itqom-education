<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log semua exception untuk debugging di production
            if (app()->environment('production')) {
                Log::error('Exception occurred: ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle API errors dengan response JSON yang lebih baik
        if ($request->is('api/*')) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions dengan response yang konsisten
     */
    protected function handleApiException($request, Throwable $e)
    {
        $statusCode = 500;
        $message = 'Internal Server Error';

        // Tentukan status code berdasarkan jenis exception
        if ($e instanceof HttpExceptionInterface) {
            $statusCode = $e->getStatusCode();
        } elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $statusCode = 404;
            $message = 'Resource not found';
        } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
            $statusCode = 422;
            $message = 'Validation failed';
        } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
            $statusCode = 401;
            $message = 'Unauthenticated';
        }

        $response = [
            'success' => false,
            'message' => $message,
            'status_code' => $statusCode
        ];

        // Tambahkan detail error hanya jika debugging aktif
        if (config('app.debug')) {
            $response['error'] = $e->getMessage();
            $response['trace'] = $e->getTraceAsString();
        }

        return response()->json($response, $statusCode);
    }
}
