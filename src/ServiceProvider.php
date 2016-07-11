<?php

namespace Kitchenu\Debugbar;

use Kitchenu\Debugbar\Middleware\Debugbar;
use Slim\App;

class ServiceProvider
{
    /**
     * Default settings
     *
     * @var array
     */
    protected $settings = [
        'enabled' => true,
        'storage' => [
            'enabled' => true,
            'path'    => __DIR__ . '/../../../../debugbar',
        ],
        'capture_ajax' => true,
        'collectors' => [
            'phpinfo'    => true,  // Php version
            'messages'   => true,  // Messages
            'time'       => true,  // Time Datalogger
            'memory'     => true,  // Memory usage
            'exceptions' => true,  // Exception displayer
            'request'    => true,  // Request logger
        ]
    ];

    /**
     * @param  array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = array_merge($this->settings, $settings);
    }

    /**
     * Register DebugBar service.
     *
     * @param  App $app
     *
     * @return void
     */
    public function register(App $app)
    {
        $container = $app->getContainer();

        $container['debugbar'] = function ($container) {
            return new SlimDebugBar($container, $this->settings);
        };

        $app->group('/_debugbar', function() {
            $this->get('/open', 'Kitchenu\Debugbar\Controllers\OpenHandlerController:handle')
                ->setName('debugbar-openhandler');

            $this->get('/assets/stylesheets', 'Kitchenu\Debugbar\Controllers\AssetController:css')
                ->setName('debugbar-assets-css');

            $this->get('/assets/javascript', 'Kitchenu\Debugbar\Controllers\AssetController:js')
                ->setName('debugbar-assets-js');
        });

        if (!$this->settings['enabled']) {
            return;
        }

        $app->add(new Debugbar($container['debugbar'], $container['router']));
    }
}
