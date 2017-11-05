<?php

namespace Nula;

use Interop\Container\ContainerInterface;
use Nula\Controller\Error\ApplicationError;
use Nula\Controller\Error\NotFound;
use Slim\App;

class Application {

  /**
   * @var App
   */
  private $slimApplication;

  /**
   * @var ContainerInterface
   */
  private $container;

  /**
   * @param App $slimApplication
   */
  public function __construct(App $slimApplication) {
    $this->slimApplication = $slimApplication;
    $this->container = $this->slimApplication->getContainer();
  }

  public function run() {
    $this->registerRoutes();
    $this->registerViewFactory();
    $this->registerLanguagesManager();
    $this->registerProjectProvider();
    $this->registerErrorNotFoundController();
    $this->registerApplicationErrorController();
    $this->slimApplication->run();
  }

  private function registerViewFactory() {
    $this->container['viewFactory'] = new \Nula\View\Factory($this->container->get('settings')['twig']['cache']);
  }

  private function registerLanguagesManager() {
    $this->container['localeManager'] = new \Nula\I18n\LocaleManager($this->container->get('router'),
      $this->container->get('viewFactory'));
  }

  private function registerProjectProvider() {
    $this->container['projectProvider'] = new \Nula\Project\Provider($this->container->get('localeManager'));
  }

  private function registerRoutes() {
    $router = $this->container->get('router');
    /* @var $router \Slim\Router */
    $router->map(['get'], '/', \Nula\Controller\Home::class . ':actionHomepage');
    $router->map(['get'], '/{lang:[a-z]{2}-[A-Z]{2}}', \Nula\Controller\Home::class . ':actionHomepage')->setName('homepage');
    $router->map(['get'], '/{lang:[a-z]{2}-[A-Z]{2}}/projekty', \Nula\Controller\Project::class . ':actionList')->setName('projects');
    $router->map(['get'], '/{lang:[a-z]{2}-[A-Z]{2}}/projekt/{rewrite:[a-z-]+}',
      \Nula\Controller\Project::class . ':actionDetail')->setName('projectDetail');
    $router->map(['get'], '/{lang:[a-z]{2}-[A-Z]{2}}/o-nas', \Nula\Controller\About::class . ':actionAtelier')->setName('atelier');
    $router->map(['get'], '/{lang:[a-z]{2}-[A-Z]{2}}/kontakt[/{' . \Nula\Controller\About::EMAIL_SENT_STATUS_KEY . '}]', \Nula\Controller\About::class . ':actionContact')->setName('contact');
    $router->map(['post'], '/{lang:[a-z]{2}-[A-Z]{2}}/kontakt/email', \Nula\Controller\About::class . ':actionContactEmail')->setName('contactEmail');
    $router->map(['get'], '/{lang:[a-z]{2}-[A-Z]{2}}/faq', \Nula\Controller\Help::class . ':actionFaq')->setName('faq');
  }

  private function registerErrorNotFoundController() {
    $this->container['notFoundHandler'] = function (ContainerInterface $container) {
      return new NotFound($container);
    };
  }

  private function registerApplicationErrorController() {
    $this->container['errorHandler'] = function (ContainerInterface $container) {
      return new ApplicationError($container);
    };
  }

}