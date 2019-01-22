<?php

namespace Kitchenu\Debugbar;

use Slim\App;

abstract class BaseServiceProvider
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
            'driver'  => 'file',  // file, pdo, redis
            'path'    => '',
            'connection' => null
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

    /**
     * @param  array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = array_replace_recursive($this->settings, $settings);

        if (empty($this->settings['storage']['path'])) {
            $this->settings['storage']['path'] = __DIR__ . '/../../../../debugbar';
        }
    }

    /**
     * Register DebugBar service.
     *
     * @param  App $app
     *
     * @return void
     */
    abstract public function register(App $app);
}
