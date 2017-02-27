<?php

namespace Nula;

class Application {

  /**
   * @var \Slim\App
   */
  private $slimApplication;
  
  /**
   * @var \Interop\Container\ContainerInterface
   */
  private $container;

  /**
   * @param \Slim\App $slimApplication
   */
  public function __construct(\Slim\App $slimApplication) {
    $this->slimApplication = $slimApplication;
    $this->container = $this->slimApplication->getContainer();
  }

  public function run() {
    $this->registerRoutes();
    $this->registerViewFactory();        
    $this->slimApplication->run();
  }
  
  
  private function registerViewFactory() {
    $this->slimApplication->getContainer()['viewFactory'] = new \Nula\View\Factory($this->container->get('settings')['twig']['cache']);
  }
  
  private function registerRoutes() {
    $router = $this->container->get('router'); /* @var $router \Slim\Router */        
    $router->map(['get'], '/{lang:[a-y]{2}}', \Nula\Controller\Home::class . ':homepage');
    $router->map(['get'], '/', \Nula\Controller\Home::class . ':homepage');
  }

}