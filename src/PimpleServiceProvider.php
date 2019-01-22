<?php

namespace Kitchenu\Debugbar;

use Kitchenu\Debugbar\Middleware\Debugbar;
use Slim\App;

class PimpleServiceProvider extends BaseServiceProvider
{
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
            return new SlimDebugBar($container['router'], $container['request'], $this->settings);
        };

        if (!$this->settings['enabled']) {
            return;
        }

        $app->group('/_debugbar', function() {
            $this->get('/open', 'Kitchenu\Debugbar\Controllers\OpenHandlerController:handle')
                ->setName('debugbar-openhandler');

            $this->get('/assets/stylesheets', 'Kitchenu\Debugbar\Controllers\AssetController:css')
                ->setName('debugbar-assets-css');

            $this->get('/assets/javascript', 'Kitchenu\Debugbar\Controllers\AssetController:js')
                ->setName('debugbar-assets-js');
        });

        $app->add(new Debugbar($container['debugbar'], $container['errorHandler']));
    }
}