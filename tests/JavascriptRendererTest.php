<?php

namespace Kitchenu\Debugbar\Tests;

use Kitchenu\Debugbar\JavascriptRenderer;

class JavascriptRendererTest extends SlimDebugBarTestCase
{
    /**
     * @var JavascriptRenderer
     */
    // protected $renderer;

    // public function setUp()
    // {
    //     parent::setUp();
    //     $this->renderer = new JavascriptRenderer($this->debugbar);
    // }

    public function testRenderHeadSlim()
    {
        foreach ($this->apps as $app) {
            $renderer = new JavascriptRenderer($app->getContainer()->get('debugbar'));

            $router = $app->getContainer()->get('router');
            $router->removeNamedRoute('debugbar-assets-css');
            $router->removeNamedRoute('debugbar-assets-js');

            $app->get('css_test', function() {})->setName('debugbar-assets-css');
            $app->get('js_test', function() {})->setName('debugbar-assets-js');

            $html = $renderer->renderHeadSlim($router);
     
            $this->assertContains('<link rel="stylesheet" type="text/css" href="css_test', $html);
            $this->assertContains('<script type="text/javascript" src="js_test', $html);
            $this->assertContains('<script type="text/javascript" src="js_test', $html);
        }
    }

    public function testDumpAssetsToString()
    {
        foreach ($this->apps as $app) {
            $renderer = new JavascriptRenderer($app->getContainer()->get('debugbar'));
            $string = $renderer->dumpAssetsToString('css');
            $this->assertContains('@font-face', $string);
        }
    }
}
