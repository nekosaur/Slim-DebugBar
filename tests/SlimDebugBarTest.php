<?php

namespace Kitchenu\Debugbar\Tests;

use Kitchenu\Debugbar\JavascriptRenderer;
use Kitchenu\Debugbar\PimpleServiceProvider;
use Kitchenu\Debugbar\PhpDiServiceProvider;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class SlimDebugBarTest extends SlimDebugBarTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->debugbars = array_map(function ($app) {
            return $app->getContainer()->get('debugbar');
        }, $this->apps);
    }

    public function testStartStopMeasure()
    {
        foreach ($this->debugbars as $debugbar) {
            $debugbar->startMeasure('test');
            $debugbar->stopMeasure('test');
            $collector = $debugbar->getCollector('time');
            $measure = $collector->getMeasures()[0];
    
            $this->assertEquals($measure['label'], 'test');
            $this->assertGreaterThan($measure['start'], $measure['end']);
        }
    }

    public function testAddMeasure()
    {
        foreach ($this->debugbars as $debugbar) {
            $debugbar->addMeasure('test', 10, 20);
            $collector = $debugbar->getCollector('time');
            $measure = $collector->getMeasures()[0];

            $this->assertEquals('test', $measure['label']);
            $this->assertGreaterThan($measure['start'], $measure['end']);
        }
    }

    public function testMeasure()
    {
        foreach ($this->debugbars as $debugbar) {
            $debugbar->measure('test', function () {});
            $collector = $debugbar->getCollector('time');
            $measure = $collector->getMeasures()[0];

            $this->assertEquals('test', $measure['label']);
        }
    }

    public function testAddException()
    {
        foreach ($this->debugbars as $debugbar) {
            $debugbar->addException(new \Exception('test'));
            $collector = $debugbar->getCollector('exceptions');
            $exception = $collector->getExceptions()[0];

            $this->assertEquals($exception->getMessage(), 'test');
        }
    }

    public function testGetJavascriptRenderer()
    {
        foreach ($this->debugbars as $debugbar) {
            $javascriptRenderer = $debugbar->getJavascriptRenderer();

            $this->assertInstanceOf(JavascriptRenderer::class, $javascriptRenderer);
        }
    }

    public function testModifyResponse()
    {
        foreach ($this->apps as $app) {
            $environment = $app->getContainer()->get('environment');

            $request = Request::createFromEnvironment($environment);

            $response = new Response();
    
            $app->get('/test', function ($request, $response) {
                $body = $response->getBody();
                $body->write('Test');
                return $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
            });

            $response = $app->process($request, $response);

            $response = $app->getContainer()->get('debugbar')->modifyResponse($response);
            
            $this->assertContains('var phpdebugbar = new PhpDebugBar.DebugBar();', (string) $response->getBody());
        }
    }

    public function testCollect()
    {
        foreach ($this->debugbars as $debugbar) {
            $this->assertArrayHasKey('__meta', $debugbar->collect());
        }
    }

    public function testAddMessage()
    {
        foreach ($this->debugbars as $debugbar) {
            $debugbar->addMessage('test');
            $collector = $debugbar->getCollector('messages');
            $message = $collector->getMessages()[0];

            $this->assertEquals($message['message'], 'test');
        }
    }

    public function test__call()
    {
        foreach ($this->debugbars as $debugbar) {
            $debugbar->info('test');

            $collector = $debugbar->getCollector('messages');
            $message = $collector->getMessages()[0];

            $this->assertEquals($message['label'], 'info');
            $this->assertEquals($message['message'], 'test');
        }
    }
}