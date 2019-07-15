<?php

namespace Nula\Project;

use Nula\I18n\LocaleManager;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Exception\ParseException;

class Provider {

  const MAIN_PICTURE_NAME = 'main.jpg';
  const IMAGE_TYPE_THUMBNAIL = 'thumbnail';
  const IMAGE_TYPE_FULL = 'full';
  /**
   * @var LocaleManager
   */
  private $localeManager;

  /**
   * Provider constructor.
   * @param LocaleManager $localeManager
   */
  public function __construct(LocaleManager $localeManager) {
    $this->localeManager = $localeManager;
  }

  /**
   * @param string $locale
   * @return array
   * @throws \Exception
   */
  public function getProjectsDesc(string $locale) {
    $filesystemIterator = new Finder();
    $projectDirectoryIterator = $filesystemIterator->in(__DIR__ . '/../../../../www/projects')
      ->directories()
      ->name('/^(\d+)((-[a-z]+)+)$/')
      ->sortByName()
      ->getIterator();

    $projects = [];
    foreach ($projectDirectoryIterator as $projectFolder) {
      $projectFolderFilename = $projectFolder->getFilename();
      $order = \substr($projectFolderFilename, 0, \strpos($projectFolderFilename, '-'));
      $projects[$order] = $this->projectFolderToObject($projectFolder, $locale);
    }
    \krsort($projects);

    return $projects;
  }

  /**
   * @param string $rewrite
   * @param string $locale
   * @return Project
   * @throws \Exception
   */
  public function getProjectByRewrite(string $rewrite, string $locale): Project {
    $filesystemIterator = new Finder();
    $projectDirectoryIterator = $filesystemIterator->in(__DIR__ . '/../../../../www/projects')
      ->directories()
      ->name('/^(\d+)-' . $rewrite . '$/')
      ->getIterator();
    $projectDirectoryIterator->rewind();
    if ($projectDirectoryIterator->valid()) {
      $project = $this->projectFolderToObject($projectDirectoryIterator->current(), $locale);
    } else {
      $project = new Project();
      $project->setNull(true);
    }

    return $project;
  }

  /**
   * @param \SplFileInfo $projectFolder
   * @param string $locale
   * @return Project
   * @throws \Exception
   */
  private function projectFolderToObject(\SplFileInfo $projectFolder, string $locale): Project {
    $project = new Project();
    $this->mapRewrite($projectFolder, $project);
    $this->mapProjectInfo($projectFolder, $project, $locale);
    $this->mapMainPicture($projectFolder, $project);
    $this->mapFullImages($projectFolder, $project);
    $this->mapThumbnailImages($projectFolder, $project);

    return $project;
  }

  /**
   * @param \SplFileInfo $projectFolder
   * @param Project $project
   */
  private function mapRewrite(\SplFileInfo $projectFolder, Project $project) {
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

  /**
   * @param \SplFileInfo $projectFolder
   * @param Project $project
   * @param string $locale
   * @throws \Exception
   */
  private function mapProjectInfo(\SplFileInfo $projectFolder, Project $project, string $locale) {
    $filename = $projectFolder->getPathname() . '/info.txt';
    if (\is_file($filename) === false || \is_readable($filename) === false) {
      $project->setNull(true);
    }

    $info = [];
    try {
      $info = \Symfony\Component\Yaml\Yaml::parse($this->getInfoFromFile($filename));
    } catch (ParseException $parseException) {
      printf("Unable to parse the YAML string: %s", $parseException->getMessage());
    }

    $this->extractProperty($project, 'setName', $info, 'Název', $locale);
    $this->extractProperty($project, 'setTypology', $info, 'Typologie', $locale);
    $this->extractProperty($project, 'setPlace', $info, 'Místo', $locale);
    $this->extractProperty($project, 'setAuthors', $info, 'Autoři', $locale);
    $this->extractProperty($project, 'setCooperation', $info, 'Spolupráce', $locale);
    $this->extractProperty($project, 'setInvestor', $info, 'Investor', $locale);
    $this->extractProperty($project, 'setStudy', $info, 'Studie', $locale);
    $this->extractProperty($project, 'setVolumetricStudy', $info, 'Objemová studie', $locale);
    $this->extractProperty($project, 'setRealization', $info, 'Realizace', $locale);
    $this->extractProperty($project, 'setProject', $info, 'Projekt', $locale);
    $this->extractProperty($project, 'setAward', $info, 'Ocenění', $locale);
    $this->extractProperty($project, 'setCompetition', $info, 'Soutěž', $locale);
    $this->extractProperty($project, 'setPublication', $info, 'Publikace', $locale);
    $this->extractProperty($project, 'setDescription', $info, 'Popis', $locale);
  }

  /**
   * @param Project $project
   * @param string $projectSetterName
   * @param array $info
   * @param string $propertyNameInInfo
   * @param string $lang
   */
  private function extractProperty(Project $project, string $projectSetterName, array $info, string $propertyNameInInfo, string $lang) {
    if (!isset($info[$propertyNameInInfo])) {
      return;
    }

    $lang = $this->localeManager->extractLangCodeFromLocale($lang);
    $defaultLang = $this->localeManager->extractLangCodeFromLocale($this->localeManager::DEFAULT_LOCALE);

    if (is_array($info[$propertyNameInInfo]) && array_key_exists($lang, $info[$propertyNameInInfo])) {
      $value = $info[$propertyNameInInfo][$lang];
    } else if (is_array($info[$propertyNameInInfo]) && array_key_exists($defaultLang, $info[$propertyNameInInfo])) {
      $value = $info[$propertyNameInInfo][$defaultLang];
    } else if (isset($info[$propertyNameInInfo])) {
      $value = $info[$propertyNameInInfo];
    } else {
      $value = null;
    }

    if (!is_null($value)) {
      $project->{$projectSetterName}($value);
    }
  }

  /**
   * @param string $filename
   * @return string
   * @throws \Exception
   */
  private function getInfoFromFile(string $filename): string {
    $originalFileContent = file_get_contents($filename);
    if ($originalFileContent === false) {
      throw new \Exception("Unable to read content of file $filename");
    }

    $fileContentWithoutBOM = preg_replace('/\x{FEFF}/u', '', $originalFileContent);
    if (is_null($fileContentWithoutBOM)) {
      throw new \Exception("Problem occured when replacing BOMs");
    }

    return $fileContentWithoutBOM;
  }

  /**
   * @param \SplFileInfo $projectFolder
   * @param Project $project
   */
  private function mapMainPicture(\SplFileInfo $projectFolder, Project $project) {
    $mainPictureFilename = $projectFolder->getPathname() . '/' . self::MAIN_PICTURE_NAME;
    if (\is_file($mainPictureFilename) === false || \is_readable($mainPictureFilename) === false) {
      $project->setNull(true);
    }

    $project->setMainImagePublicSourceName($this->createImagePublicSourceName($projectFolder, self::MAIN_PICTURE_NAME));
  }

  /**
   * @param \SplFileInfo $projectFolder
   * @param Project $project
   */
  private function mapFullImages(\SplFileInfo $projectFolder, Project $project) {
    $project->setFullImages($this->mapImages($projectFolder, self::IMAGE_TYPE_FULL));
  }

  /**
   * @param \SplFileInfo $projectFolder
   * @param Project $project
   */
  private function mapThumbnailImages(\SplFileInfo $projectFolder, Project $project) {
    $project->setThumbnailImages($this->mapImages($projectFolder, self::IMAGE_TYPE_THUMBNAIL));
  }

  /**
   * @param \SplFileInfo $projectFolder
   * @param string $imageType
   * @return array
   */
  private function mapImages(\SplFileInfo $projectFolder, string $imageType): array {
    $filesystemIterator = new Finder();
    $imageDirectoryIterator = $filesystemIterator->in($projectFolder->getPathname())
      ->files()
      ->name('/^(\d+)-' . $imageType . '.jpg$/')
      ->getIterator();

    $images = [];
    foreach ($imageDirectoryIterator as $imageFile) {
      $imageFilename = $imageFile->getFilename();
      $order = \intval(\substr($imageFilename, 0, \strpos($imageFilename, '-')));
      $images[$order] = $this->createImagePublicSourceName($projectFolder, $imageFilename);
    }
    \ksort($images);

    return $images;
  }

  /**
   * @param \SplFileInfo $projectFolder
   * @param string $imageFilename
   * @return string
   */
  private function createImagePublicSourceName(\SplFileInfo $projectFolder, string $imageFilename): string {
    return '/projects/' . $projectFolder->getFilename() . '/' . $imageFilename;
  }

}