<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @param  Request  $request
     * @param Throwable $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception): Response
    {
        // Check if the request expects a JSON response or if the app is running in API mode
        if ($request->wantsJson() || $request->is('api/*')) {
            $status = method_exists($exception, 'getStatusCode')
                ? $exception->getStatusCode()
                : 500;



            $response = [
                'success' => false,
                'message' => $exception->getMessage(),
            ];



            // Customize the response for NotFoundHttpException
            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException || $exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                $response['message'] = 'Resource not found';
                $status = 404;
            }



            // You can add more conditions here for other types of exceptions if needed

            return response()->json($response, $status);
        }

        return parent::render($request, $exception);
    }





}
