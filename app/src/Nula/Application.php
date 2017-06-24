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
    $this->registerLanguagesManager();
    $this->registerViewFactory();
    $this->slimApplication->run();
  }

  private function registerLanguagesManager() {
    $this->container['languagesManager'] = new \Nula\I18n\LanguagesManager();
  }

  private function registerViewFactory() {
    $this->container['viewFactory'] = new \Nula\View\Factory($this->container->get('languagesManager'),
            $this->container->get('settings')['twig']['cache']);
  }

  private function registerRoutes() {
    $router = $this->container->get('router'); /* @var $router \Slim\Router */
    $router->map(['get'], '/', \Nula\Controller\Home::class . ':actionHomepage');
    $router->map(['get'], '/{lang:[a-z]{2}}', \Nula\Controller\Home::class . ':actionHomepage')->setName('homepage');
    $router->map(['get'], '/{lang:[a-z]{2}}/projekty', \Nula\Controller\Project::class . ':actionList')->setName('projects');
    $router->map(['get'], '/{lang:[a-z]{2}}/projekt/{rewrite:[a-z-]+}',
            \Nula\Controller\Project::class . ':actionDetail')->setName('projectDetail');    
    $router->map(['get'], '/{lang:[a-z]{2}}/o-nas', \Nula\Controller\About::class . ':actionAtelier')->setName('atelier');
    $router->map(['get'], '/{lang:[a-z]{2}}/kontakt[/{'. \Nula\Controller\About::EMAIL_SENT_STATUS_KEY .'}]', \Nula\Controller\About::class . ':actionContact')->setName('contact');    
    $router->map(['post'], '/{lang:[a-z]{2}}/kontakt/email', \Nula\Controller\About::class . ':actionContactEmail')->setName('contactEmail');
    $router->map(['get'], '/{lang:[a-z]{2}}/faq', \Nula\Controller\Help::class . ':actionFaq')->setName('faq');
  }

}