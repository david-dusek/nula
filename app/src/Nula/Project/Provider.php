<?php

namespace Nula\Project;

class Provider {

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
    $projectFolderFilename = $projectFolder->getFilename();
    
    $project = new \Nula\Project\Project();
    $project->setRewrite(\substr($projectFolderFilename, \strpos($projectFolderFilename, '-') + 1));
    
    return $project;
  }

}