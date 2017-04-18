# IronMQ Laravel Queue Driver

This package provides a IronMQ (~4.0 SDK) driver for the Laravel queue system.

## History
This repository is a fork of [LaravelCollective IronMQ driver repo](https://github.com/LaravelCollective/iron-queue).

I've decided to fork and maintain IronMQ driver repository due to lack of support from original authors/maintainers.

## Installation (Laravel 5.3)
- `composer require hydreflab/iron-queue`
- Add `Collective\IronQueue\IronQueueServiceProvider::class` to your `app.php` configuration file.
- Configure your `iron` queue driver in your `config/queue.php`.
- Set `iron` as your queue driver in your `.env`.

To install driver for Laravel 5.2, run `composer require hydreflab/iron-queue:5.2.*`. Rest of the steps is identical.

Sample Configuration:

```php
'iron' => [
    'driver'  => 'iron',
    'host'    => 'mq-aws-us-east-1-1.iron.io',
    'token'   => 'your-token',
    'project' => 'your-project-id',
    'queue'   => 'your-queue-name',
    'encrypt' => true,
    'timeout' => 60
],
```
