<?php

class Service
{
  public function __construct($name, $running, $plist) {
    $this->setName($name);
    $this->setRunning($running);
    $this->setPlist($plist);

    $launchFile = exec('printf $HOME') . '/Library/LaunchAgents/' . basename($plist);
    $this->setStartOnLoad(file_exists($launchFile));
  }

  /**
   * @var string
   */
  protected $name;
  /**
   * @var bool
   */
  protected $running;

  /**
   * @var string
   */
  protected $plist;

  /**
   * @var bool
   */
  protected $startOnLoad;


  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * @return boolean
   */
  public function isRunning() {
    return $this->running;
  }

  /**
   * @param boolean $running
   */
  public function setRunning($running) {
    $this->running = $running;
  }

  /**
   * Start service
   * @return bool
   */
  public function start() {
    if ($this->running) {
      return false;
    }
    echo exec("launchctl load $this->plist");
    $this->running = true;
    return true;
  }

  public function stop() {
    if (!$this->running) {
      return false;
    }
    exec("launchctl unload $this->plist");
    $this->running = false;
    return true;
  }

  public function restart() {
    $this->stop();
    $this->start();
  }

  /**
   * @return string
   */
  public function getPlist() {
    return $this->plist;
  }

  /**
   * @param string $plist
   */
  public function setPlist($plist) {
    $this->plist = $plist;
  }

  /**
   * @return boolean
   */
  public function isStartOnLoad() {
    return $this->startOnLoad;
  }

  /**
   * @param boolean $startOnLoad
   */
  public function setStartOnLoad($startOnLoad) {
    $this->startOnLoad = $startOnLoad;
  }
}
