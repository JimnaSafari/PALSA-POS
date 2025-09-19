<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            // Log critical errors to external service (e.g., Sentry)
            if ($this->shouldReport($e)) {
                \Log::error('Application Error', [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'url' => request()->fullUrl(),
                    'user_id' => auth()->id(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle API requests
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        // Handle specific exceptions for web requests
        if ($e instanceof ModelNotFoundException) {
            return response()->view('errors.404', [], 404);
        }

        if ($e instanceof AccessDeniedHttpException) {
            return response()->view('errors.403', [], 403);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->view('errors.404', [], 404);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions
     */
    protected function handleApiException(Request $request, Throwable $e)
    {
        $status = 500;
        $message = 'Internal Server Error';

        if ($e instanceof ValidationException) {
            $status = 422;
            $message = 'Validation Error';
            return response()->json([
                'error' => true,
                'message' => $message,
                'errors' => $e->errors()
            ], $status);
        }

        if ($e instanceof AuthenticationException) {
            $status = 401;
            $message = 'Unauthenticated';
        }

        if ($e instanceof AccessDeniedHttpException) {
            $status = 403;
            $message = 'Access Denied';
        }

        if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
            $status = 404;
            $message = 'Resource Not Found';
        }

        // Don't expose internal errors in production
        if (app()->environment('production') && $status === 500) {
            $message = 'Something went wrong. Please try again later.';
        } else {
            $message = $e->getMessage() ?: $message;
        }

        return response()->json([
            'error' => true,
            'message' => $message,
            'status' => $status
        ], $status);
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => true,
                'message' => 'Unauthenticated'
            ], 401);
        }

        return redirect()->guest(route('userLogin'));
    }
}