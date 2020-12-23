<?php

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/bootstrap.php';

$kernel = new Kernel('test', true);
$kernel->boot();

$application = new Application($kernel);
$application->setAutoExit(false);

$database = $application->run(new StringInput('dbal:run-sql "SELECT username FROM user;"'), new NullOutput());
if ($database) {
    $application->run(new StringInput('doctrine:database:drop --if-exists --force -q'));
    $application->run(new StringInput('doctrine:database:create -q'));
    $application->run(new StringInput('doctrine:schema:update --force -q'));
}
$kernel->shutdown();
