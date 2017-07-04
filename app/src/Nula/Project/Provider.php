<?php

namespace Nula\Project;

class Provider {
  
  const MAIN_PICTUTE_NAME = 'main.jpg';

  /**
   * @var \Symfony\Component\Finder\Finder
   */
  private $filesystemFinder;

  /**
   * @param \Symfony\Component\Finder\Finder $filesystemFinder
   */
  public function __construct(\Symfony\Component\Finder\Finder $filesystemFinder) {
    $this->filesystemFinder = $filesystemFinder;
  }

  /**
   * @return \Nula\Project\Project[]
   */
  public function getProjectsDesc() {
    $projectFolderNamePattern = '/(\d+)((-[a-z]+)+)/';
    $projectDirectoryIterator = $this->filesystemFinder->in(__DIR__ . '/../../../../www/projects')
            ->directories()
            ->name($projectFolderNamePattern)
            ->sortByName()
            ->getIterator();

    $projects = [];
    foreach ($projectDirectoryIterator as $projectFolder) {
      $projectFolderFilename = $projectFolder->getFilename();
      $order = \substr($projectFolderFilename, 0, \strpos($projectFolderFilename, '-'));
      $projects[$order] = $this->projectFolderToObject($projectFolder);
    }
    \krsort($projects);

    return $projects;
  }

  /**
   * @param \SplFileInfo $projectFolder
   * @return \Nula\Project\Project
   */
  private function projectFolderToObject(\SplFileInfo $projectFolder): \Nula\Project\Project {
    $project = new \Nula\Project\Project();
    $this->mapRewrite($projectFolder, $project);
    $this->mapProjectInfo($projectFolder, $project);
    $this->mapMainPicture($projectFolder, $project);
    
    return $project;
  }

  private function mapRewrite(\SplFileInfo $projectFolder, \Nula\Project\Project $project) {
    $projectFolderFilename = $projectFolder->getFilename();
    $firstDashPosition = \strpos($projectFolderFilename, '-');
    if ($firstDashPosition === false) {
      $project->setNull(true);
      return;
    }

    $rewrite = \substr($projectFolderFilename, \strpos($projectFolderFilename, '-') + 1);
    if ($rewrite === false) {
      $project->setNull(true);
      return;
    }

    $project->setRewrite($rewrite);
  }

  private function mapProjectInfo(\SplFileInfo $projectFolder, \Nula\Project\Project $project) {
    $filename = $projectFolder->getPathname() . '/info.txt';
    if (\is_file($filename) === false || \is_readable($filename) === false) {
      $project->setNull(true);
    }

    try {
      $info = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($filename));
    } catch (ParseException $parseException) {
      printf("Unable to parse the YAML string: %s", $parseException->getMessage());
    }
    
    if (isset($info['Název'])) {
      $project->setName($info['Název']);
    }
    if (isset($info['Typologie'])) {
      $project->setTypology($info['Typologie']);
    }
    if (isset($info['Místo'])) {
      $project->setPlace($info['Místo']);
    }
    if (isset($info['Autoři'])) {
      $project->setAuthors($info['Autoři']);
    }
    if (isset($info['Spolupráce'])) {
      $project->setCooperation($info['Spolupráce']);
    }
    if (isset($info['Studie'])) {
      $project->setStudy($info['Studie']);
    }
    if (isset($info['Realizace'])) {
      $project->setRealization($info['Realizace']);
    }
    if (isset($info['Soutěž'])) {
      $project->setCompetition($info['Soutěž']);
    }
    if (isset($info['Publikace'])) {
      $project->setPublication($info['Publikace']);
    }
  }

  private function mapMainPicture(\SplFileInfo $projectFolder, \Nula\Project\Project $project) {
    $mainPictureFilename = $projectFolder->getPathname() . '/' . self::MAIN_PICTUTE_NAME;
    if (\is_file($mainPictureFilename) === false || \is_readable($mainPictureFilename) === false) {
      $project->setNull(true);
    }
    
    $project->setMainImagePublicSourceName($this->createImagePublicSourceName($projectFolder, self::MAIN_PICTUTE_NAME));
  }
  
  private function createImagePublicSourceName(\SplFileInfo $projectFolder, string $pictureName): string {
    return '/projects/' . $projectFolder->getFilename() . '/' . $pictureName;
  }

}