<?php

namespace Nula\Project;

use Symfony\Component\Yaml\Exception\ParseException;

class Provider {

  const MAIN_PICTUTE_NAME = 'main.jpg';
  const IMAGE_TYPE_THUMBNAIL = 'thumbnail';
  const IMAGE_TYPE_FULL = 'full';

  /**
   * @return \Nula\Project\Project[]
   */
  public function getProjectsDesc() {
    $filesystemIterator = new \Symfony\Component\Finder\Finder();    
    $projectDirectoryIterator = $filesystemIterator->in(__DIR__ . '/../../../../www/projects')
            ->directories()
            ->name('/^(\d+)((-[a-z]+)+)$/')
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

  public function getProjectByRewrite(string $rewrite): \Nula\Project\Project {
    $filesystemIterator = new \Symfony\Component\Finder\Finder();
    $projectDirectoryIterator = $filesystemIterator->in(__DIR__ . '/../../../../www/projects')
            ->directories()
            ->name('/^(\d+)-' . $rewrite . '$/')
            ->getIterator();
    $projectDirectoryIterator->rewind();
    if ($projectDirectoryIterator->valid()) {
      $project = $this->projectFolderToObject($projectDirectoryIterator->current());
    } else {
      $project = new \Nula\Project\Project();
      $project->setNull(true);
    }

    return $project;
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
    $this->mapFullImages($projectFolder, $project);
    $this->mapThumbnailImages($projectFolder, $project);

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

    $info = [];
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
    if (isset($info['Popis'])) {
      $project->setPopis($info['Popis']);
    }
  }

  private function mapMainPicture(\SplFileInfo $projectFolder, \Nula\Project\Project $project) {
    $mainPictureFilename = $projectFolder->getPathname() . '/' . self::MAIN_PICTUTE_NAME;
    if (\is_file($mainPictureFilename) === false || \is_readable($mainPictureFilename) === false) {
      $project->setNull(true);
    }

    $project->setMainImagePublicSourceName($this->createImagePublicSourceName($projectFolder, self::MAIN_PICTUTE_NAME));
  }
  
  
  private function mapFullImages(\SplFileInfo $projectFolder, \Nula\Project\Project $project) {
    $project->setFullImages($this->mapImages($projectFolder, self::IMAGE_TYPE_FULL));
  }
  
  private function mapThumbnailImages(\SplFileInfo $projectFolder, \Nula\Project\Project $project) {
    $project->setThumbnailImages($this->mapImages($projectFolder, self::IMAGE_TYPE_THUMBNAIL));
  }

  private function mapImages(\SplFileInfo $projectFolder, string $imageType): array {        
    $filesystemIterator = new \Symfony\Component\Finder\Finder();
    $imageDirectoryIterator = $filesystemIterator->in($projectFolder->getPathname())
            ->files()
            ->name('/^(\d+)-' . $imageType . '.jpg$/')
            ->getIterator();
    
    $images = [];
    foreach ($imageDirectoryIterator as $imageFile) {
      $imageFilename = $imageFile->getFilename();
      $order = \intval(\substr($imageFilename, 0, \strpos($imageFilename, '-')));
      $images[$order] =  $this->createImagePublicSourceName($projectFolder, $imageFilename);
    }
    \ksort($images);
    
    return $images;
  }

  private function createImagePublicSourceName(\SplFileInfo $projectFolder, string $imageFilename): string {
    return '/projects/' . $projectFolder->getFilename() . '/' . $imageFilename;
  }

}