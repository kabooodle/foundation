<?php
//// Using this file as the bootstrapper in phpunit so that we can have
//// more control over what we are doing when you call "phpunit"
//
////require_once __DIR__ .'/../bootstrap/autoload.php';
//
////$unitTesting = true;
////$testEnvironment = 'testing';
////$__app = require __DIR__.'/../bootstrap/app.php';
//
//if (in_array($__app->environment(), ['testing'])) {
////    $connection = new PDO("mysql:host=".getenv('EVALS_DB_HOST')."", getenv('EVALS_DB_USER'), getenv('EVALS_DB_PW'));
////
////    $output = new Symfony\Component\Console\Output\ConsoleOutput();
////    $output->writeln("<info>Truncating database...</info>");
////
////    $connection->query("DROP DATABASE IF EXISTS ".getenv('EVALS_DB_NAME'))->execute();
////    $connection->query("CREATE DATABASE IF NOT EXISTS ".getenv('EVALS_DB_NAME'))->execute();
////
////    Artisan::call('migrate', ['--env' => 'testing']);
////    Artisan::call('db:seed', ['--env' => 'testing']);
//}
////
////$__fm = new \League\FactoryMuffin\FactoryMuffin;
////$__fm->loadFactories(__DIR__.'/_factories');
