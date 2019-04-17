<?php

require __DIR__.'/../vendor/autoload.php';

use App\RobotArm;

if (!isset($argv[1]) || !is_file($argv[1])) {
    exit('Commands file invalid!' . PHP_EOL);
}

try {
    $robotArm = new RobotArm($argv[1]);
}
catch (Exception $e) {
    exit($e->getMessage() . PHP_EOL);
}

$stack = $robotArm->exec();

foreach($stack as $index => $blocks) {
    echo $index . trim(": " . str_replace(["[", "]"], "", $blocks)), PHP_EOL;
}