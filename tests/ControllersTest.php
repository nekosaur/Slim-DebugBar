<?php

namespace Kitchenu\Debugbar\Tests;

use Kitchenu\Debugbar\Controllers\AssetController;
use Kitchenu\Debugbar\Controllers\OpenHandlerController;

class ControllersTest extends SlimDebugBarTestCase
{
    /**
     * @var \Psr\Container\ContainerInterface[]
     */
    protected $containers;

    public function setUp() {
        parent::setUp();

        $this->containers = array_map(function ($app) {
            return $app->getContainer();
        }, $this->apps);
    }

    public function testAssetController()
    {
        foreach ($this->containers as $container) {
            $controller = new AssetController($container);
    
            $cssResponse = $controller->css(
                $container->get('request'), $container->get('response'), []
            );
            
            $this->assertEquals($cssResponse->getHeaderLine('Content-type'), 'text/css');
    
            $jsResponse = $controller->js(
                $container->get('request'), $container->get('response'), []
            );
            
            $this->assertEquals($jsResponse->getHeaderLine('Content-type'), 'text/javascript');
        }
    }

    public function testOpenHandlerController()
    {
        foreach ($this->containers as $container) {
            $controller = new OpenHandlerController($container);

            $response = $controller->handle(
                $container->get('request'), $container->get('response'), []
            );
            
            $this->assertEquals($response->getHeaderLine('Content-type'), 'application/json;charset=utf-8');
        }
    }
}