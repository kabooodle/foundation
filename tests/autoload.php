<?php

require __DIR__ .'/../bootstrap/autoload.php';
$__app = require __DIR__.'/../bootstrap/app.php';
$__app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

if (env('APP_ENV') === 'testing') {
    $connection = new PDO("mysql:host=".env('DB_HOST')."", env('DB_USERNAME'), env('DB_PASSWORD'));

    $output = new Symfony\Component\Console\Output\ConsoleOutput();
    $output->writeln("<info>Truncating database...</info>");

    $connection->query("CREATE DATABASE IF NOT EXISTS ".env('DB_DATABASE'))->execute();
    $output->writeln("<info>Migrating database...</info>");
    Artisan::call('migrate', ['--env' => 'testing']);
    $output->writeln("<info>Beginning tests</info>");
}
