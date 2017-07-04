<?php

namespace Nula\Controller;

class Project extends \Nula\Controller\Base {

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @param array $args
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function actionList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args): \Psr\Http\Message\ResponseInterface {
    $projectProvider = $this->getService('projectProvider');
    $projects = $projectProvider->getProjectsDesc();

    $templateData = [
      'activeLink' => 'projects',
      'projects' => $projects,
    ];
    return $this->createTwigI18nResponse($request, $response, $args, 'project/list.twig', $templateData);
  }

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @param array $args
   * @return \Psr\Http\Message\ResponseInterface\
   */
  public function actionDetail(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args): \Psr\Http\Message\ResponseInterface {
    $projectProvider = $this->getService('projectProvider');
    $project = $projectProvider->getProjectByRewrite($args['rewrite']);
    if ($project->isNull()) {
      return new \Slim\Http\Response(404);
    }

    $templateData = [
      'activeLink' => 'projects',
      'project' => $project,
    ];
    
    return $this->createTwigI18nResponse($request, $response, $args, 'project/detail.twig', $templateData);
  }

}