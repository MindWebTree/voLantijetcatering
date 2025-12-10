<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
     * @param  \Throwable  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {

        // sandeep add code after session expire form submit return to homepage
        if ($exception instanceof TokenMismatchException) {

            if(!$request->is('admin/*') && !$request->is('api/*')){
                // dd($request->expectsJson());
                // if ($request->expectsJson()) {
                //     return response()->json([
                //         'error' => 'Session expired. Please refresh the page.',
                //     ], 419); // 419 is the HTTP status code for CSRF token mismatch
                // }
               return redirect()->route('shop.home.index');
            }

            if($request->is('admin/*')){
              return redirect()->route('admin.session.create');
            }
        }

  // ✅ Ensure validation errors properly redirect back with errors
    if ($exception instanceof ValidationException) {
        return parent::render($request, $exception);
    }

    // ✅ Handle HTTP exceptions like 404, 403, and 500
    if ($exception instanceof HttpException) {
        if ($exception->getStatusCode() === 500) {
            return response()->view('shop::errors.500', [], 500);
        }
        return parent::render($request, $exception);
    }

    // ✅ Handle all other unexpected fatal errors (syntax errors, undefined errors, etc.)
    if (!config('app.debug')) {
       return response()->view('shop::errors.500', [], 500);
    }


        return parent::render($request, $exception);
    }

}
