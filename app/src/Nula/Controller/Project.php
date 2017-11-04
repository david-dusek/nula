<?php

namespace Nula\Controller;

use Nula\Project\Provider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Project extends Base {

  /**
   * @param Request $request
   * @param Response $response
   * @param array $args
   * @return ResponseInterface
   * @throws \Exception
   * @throws ContainerExceptionInterface
   * @throws NotFoundExceptionInterface
   */
  public function actionList(Request $request, Response $response, array $args): ResponseInterface {
    $projectProvider = $this->getProjectProvider();
    $projects = $projectProvider->getProjectsDesc($this->getLocale($args));

    $templateData = [
      'activeLink' => 'projects',
      'projects' => $projects,
    ];
    return $this->createTwigLocalizedResponse($request, $response, $args, 'project/list.twig', $templateData);
  }

  /**
   * @param Request $request
   * @param Response $response
   * @param array $args
   * @return ResponseInterface
   * @throws \Exception
   * @throws ContainerExceptionInterface
   * @throws NotFoundExceptionInterface
   */
  public function actionDetail(Request $request, Response $response, array $args): ResponseInterface {
    $projectProvider = $this->getProjectProvider();
    $project = $projectProvider->getProjectByRewrite($this->getRewrite($args), $this->getLocale($args));
    if ($project->isNull()) {
      return new Response(404);
    }

    $templateData = [
      'activeLink' => 'projects',
      'project' => $project,
    ];

    return $this->createTwigLocalizedResponse($request, $response, $args, 'project/detail.twig', $templateData);
  }

  /**
   * @return Provider
   * @throws ContainerExceptionInterface
   * @throws NotFoundExceptionInterface
   */
  private function getProjectProvider(): Provider {
    $projectProvider = $this->getService('projectProvider');
    return $projectProvider;
  }

  /**
   * @param array $args
   * @return string
   */
  public function getRewrite(array $args): string {
    return $args['rewrite'];
  }

}