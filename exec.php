<?php
require_once 'lib/Services.php';

$services = new Services();

$exploded = explode(' ', $query);
$serviceName = trim($exploded[0]);
$command = trim($exploded[1]);

$service = $services->getServiceByName($serviceName);

if ($service) {
  switch ($command) {
    case 'restart':
      $service->restart();
      echo "{$service->getName()} restarted";
      break;
    case 'start':
      $service->start();
      echo "{$service->getName()} started";
      break;
    case 'stop':
      $service->stop();
      echo "{$service->getName()} stopped";
      break;
    default:
      echo 'Incorrect command';
  }
} else {
  echo 'Incorrect service name';
}
