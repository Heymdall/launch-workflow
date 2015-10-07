<?php
require_once 'lib/Workflows.php';
require_once 'lib/Services.php';


$w = new Workflows();
$services = new Services();

function createActionResult(Service $service, Workflows $w, $command, $commandQuery = '') {
  if (strlen($commandQuery) === 0 || strpos($command, $commandQuery) !== false) {
    $w->result('', "{$service->getName()} {$command}", $command, ucfirst($command) . " {$service->getName()} service", "assets/{$command}.png", 'yes', "{$service->getName()} > {$command}");
  }
}

if (!$query || ($query && strpos($query, '>') === false)) {
  $services = $services->getServices($query);

  foreach ($services as $service) {
    $w->result('',
      $service->getName(),
      $service->getName(),
      ($service->isRunning() ? 'Running' : 'Stopped') . ($service->isStartOnLoad() ? ', start on load' : ''),
      $service->isRunning() ? 'assets/on.png' : 'assets/off.png',
      'no',
      $service->getName() . ' > '
    );
  }
} else {
  $command = explode('>', $query);
  $serviceName = trim($command[0]);
  $command = trim($command[1]);
  $service = $services->getServiceByName($serviceName);
  if ($service && $service->isRunning()) {
    createActionResult($service, $w, 'stop', $command);
    createActionResult($service, $w, 'restart', $command);
  } else {
    createActionResult($service, $w, 'start', $command);
  }
}


echo $w->toxml();
