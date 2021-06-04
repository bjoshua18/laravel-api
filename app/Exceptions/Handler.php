<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;

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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // Error en la validacion de formularios
        if ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }
        // Error al no encontrar una instancia
        if ($e instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($e->getModel()));
            $id = implode('', $e->getIds());
            return $this->errorResponse("No existe ninguna instancia de $model con el id $id", 404);
        }
        // Error de autenticacion
        if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }
        // Error de autorizacion
        if ($e instanceof AuthorizationException) {
            return $this->errorResponse('No posee permisos para ejecutar esta accion', 403);
        }
        // Error 404
        if ($e instanceof NotFoundHttpException) {
            return $this->errorResponse('No se encontró la URL especificada', 404);
        }
        // Error de metodos no permitidos
        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('El método especificado no es válido', 405);
        }
        // Error http
        if ($e instanceof HttpException) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }
        // Error de integridad cuando se intenta eliminar un recurso relacionado con otro
        if ($e instanceof QueryException) {
            if ($e->errorInfo[1] === 1451) {
                return $this->errorResponse('No se puede eliminar el recurso porque está relacionado con algún otro', 409);
            }
        }
        // Resto de errores
        if (config('app.debug')) {
            return parent::render($request, $e);
        }
        return $this->errorResponse('Falla inesperada. Intentelo mas tarde', 500);
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
        return $this->errorResponse('No autenticado', 401);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        return $this->errorResponse($e->errors(), $e->status);
    }
}
