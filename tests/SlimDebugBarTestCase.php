<?php

namespace Kitchenu\Debugbar\Tests;

use Slim\App;
use Kitchenu\Debugbar\SlimDebugBar;
use PHPUnit_Framework_TestCase;
use Kitchenu\Debugbar\PimpleServiceProvider;
use Kitchenu\Debugbar\PhpDiServiceProvider;

abstract class SlimDebugBarTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var App[]
     */
    protected $apps;

    public function setUp()
    {
        $settings = [
            'storage' => [
                'enabled' => true,
                'driver'  => 'file',
                'path'    => __DIR__ . '/../debugbar',
            ],
            'capture_ajax' => true,
            'collectors' => [
                'phpinfo'    => true,  // Php version
                'messages'   => true,  // Messages
                'time'       => true,  // Time Datalogger
                'memory'     => true,  // Memory usage
                'exceptions' => true,  // Exception displayer
                'route'      => true,
                'request'    => true,  // Request logger
            ]
        ];
        

        $pimpleServiceProvider = new PimpleServiceProvider($settings);
        $phpDiServiceProvider = new PhpDiServiceProvider($settings);

        $container = [
            'settings' => [
                'displayErrorDetails' => true,
            ]
        ];

        $pimpleApp = new App($container);
        $pimpleServiceProvider->register($pimpleApp);

        $phpDiApp = new \DI\Bridge\Slim\App($container);
        $phpDiServiceProvider->register($phpDiApp);

        $this->apps = [$pimpleApp, $phpDiApp];
    }
}