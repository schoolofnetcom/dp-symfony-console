<?php

use ErikFig\Console\Commands\PokeApi;
use Symfony\Component\Console\Application;

require __DIR__.'/vendor/autoload.php';

$application = new Application;
$application->add(new PokeApi);
$application->run();
