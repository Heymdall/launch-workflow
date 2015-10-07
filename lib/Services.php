<?php
require_once 'Service.php';

class Services
{
  /**
   * @var Service[]
   */
  protected $services;

  public function __construct() {
    $this->loadServices();
  }

  private function findPlists() {
    $dirHandle = opendir('/usr/local/opt/');
    $plists = [];

    while ($file = readdir($dirHandle)) {
      $files = glob("/usr/local/opt/$file/homebrew.mxcl.*.plist");
      $plists = array_merge($plists, $files);
    }
    return $plists;
  }

  private function loadServices() {
    $plists = $this->findPlists();

    $services = [];
    $launchCtlRes = shell_exec('launchctl list');
    foreach ($plists as $plist) {
      $exploded = explode('.', $plist);
      $name = $exploded[count($exploded) - 2];
      $isRun = strpos($launchCtlRes, $name) !== false;
      $services[] = new Service($name, $isRun, $plist);
    }

    $this->services = $services;
  }

  /**
   * @param $filter
   * @return Service[]
   */
  public function getServices($filter) {
    return array_filter($this->services, function (Service $service) use ($filter) {
      return $filter && strpos($service->getName(), $filter) !== false || !$filter;
    });
  }

  /**
   * @param $name
   * @return bool|Service
   */
  public function getServiceByName($name) {
    foreach ($this->services as $service) {
      if ($service->getName() == $name) {
        return $service;
      }
    }
    return false;
  }
}
