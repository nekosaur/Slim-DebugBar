<?php

namespace Kitchenu\Debugbar\Tests;

use Exception;
use Kitchenu\Debugbar\Middleware\Debugbar;

class MiddlewareTest extends SlimDebugBarTestCase
{
    public function testDebugbarWithDefaultErrorHandler()
    {
        foreach ($this->apps as $app) {
            $c = $app->getContainer();
            $this->invokeDebugbarMiddleware(
                $c->get('request'),
                $c->get('response'),
                $c->get('debugbar'),
                $c->get('errorHandler')
            );
        }
    }

    public function testDebugbarWithCustomErrorHandler()
    {
        foreach ($this->apps as $app) {
            $c = $app->getContainer();
            $this->invokeDebugbarMiddleware(
                $c->get('request'),
                $c->get('response'),
                $c->get('debugbar'),
                function ($request, $response, $e) {
                    return $response;
                }
            );
        }
    }

    public function invokeDebugbarMiddleware($request, $response, $debugbar, $errorHandler)
    {
        $debugbarMiddleware = new Debugbar($debugbar, $errorHandler);

        $debugbarMiddleware(
            $request,
            $response,
            function () {
                throw new Exception('test');
            }
        );

        $collector = $debugbar->getCollector('exceptions');
        $exception = $collector->getExceptions()[0];

        $this->assertEquals($exception->getMessage(), 'test');
    }
}