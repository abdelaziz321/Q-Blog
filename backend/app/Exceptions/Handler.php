<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
        || $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return response()->json([
                'message' => "what are you doing here -_-"
            ], 404);
        }
        elseif ($exception instanceof \Illuminate\Auth\AuthenticationException)
        {
            return response()->json([
                'message' => "unauthenticated user! please login first"
            ], 400);
        }
        elseif ($exception instanceof \Illuminate\Auth\Access\AuthorizationException)
        {
            return response()->json([
                'message' => "access denied, you are not allowed to make this action"
            ], 403);
        }
        elseif ($exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException)
        {
            return response()->json([
                'message' => "Token Signature could not be verified"
            ], 401);
        }

        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
        return parent::render($request, $exception);
    }
}
