<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
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
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Handle API requests with JSON responses
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Handle API exceptions and return consistent JSON responses
     */
    protected function handleApiException($request, Throwable $exception)
    {
        // Validation Exception (422)
        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'message' => 'The given data was invalid',
                'errors' => $exception->errors()
            ], 422);
        }

        // Authentication Exception (401)
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthenticated',
                'message' => $exception->getMessage() ?: 'Authentication required'
            ], 401);
        }

        // JWT Token Expired Exception (401)
        if ($exception instanceof TokenExpiredException) {
            return response()->json([
                'success' => false,
                'error' => 'Token expired',
                'message' => 'Your session has expired. Please login again.'
            ], 401);
        }

        // JWT Token Invalid Exception (401)
        if ($exception instanceof TokenInvalidException) {
            return response()->json([
                'success' => false,
                'error' => 'Token invalid',
                'message' => 'The token is invalid. Please login again.'
            ], 401);
        }

        // JWT General Exception (401)
        if ($exception instanceof JWTException) {
            return response()->json([
                'success' => false,
                'error' => 'Token error',
                'message' => 'Could not process token. Please login again.'
            ], 401);
        }

        // Authorization Exception (403)
        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'success' => false,
                'error' => 'Forbidden',
                'message' => $exception->getMessage() ?: 'You do not have permission to perform this action'
            ], 403);
        }

        // Model Not Found Exception (404)
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'error' => 'Not found',
                'message' => 'The requested resource was not found'
            ], 404);
        }

        // Not Found HTTP Exception (404)
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'success' => false,
                'error' => 'Not found',
                'message' => 'The requested endpoint was not found'
            ], 404);
        }

        // Method Not Allowed Exception (405)
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'success' => false,
                'error' => 'Method not allowed',
                'message' => 'The HTTP method is not supported for this endpoint'
            ], 405);
        }

        // Default to 500 server error
        $statusCode = method_exists($exception, 'getStatusCode') 
            ? $exception->getStatusCode() 
            : 500;

        // Show detailed error in debug mode, generic message in production
        $message = config('app.debug') 
            ? $exception->getMessage() 
            : 'An error occurred while processing your request';

        return response()->json([
            'success' => false,
            'error' => 'Server error',
            'message' => $message,
            'trace' => config('app.debug') ? $exception->getTraceAsString() : null
        ], $statusCode);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthenticated',
                'message' => 'Authentication required to access this resource'
            ], 401);
        }

        return redirect()->guest(route('admin.login'));
    }
}
