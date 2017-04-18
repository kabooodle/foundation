<?php

namespace Kabooodle\Tests;

use Event;
use Mockery;
use ReflectionClass;
use ReflectionMethod;
use ReflectionObject;
use ReflectionProperty;
use Illuminate\Foundation\Testing\TestCase as L_TestCase;
use Kabooodle\Foundation\Application\KabooodleApplication;

/*
 * ARRANGE -- setup
 * ACT-- run code to test outcome
 * ASSERT -- about what happened verifying what happened
 * Class TestCase
 * @package Kabooodle\Tests
 */
abstract class BaseTestCase extends L_TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://www.kabooodle.dev';

    /**
     * @return KabooodleApplication
     */
    public static function app()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        return self::app();
    }

    public function tearDown()
    {
        // https://phpunit.de/manual/current/en/fixtures.html
        $refl = new ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }

        parent::tearDown();

        Mockery::close();
    }

    /**
     * getPrivateMethod
     *
     * @param    string $className
     * @param    string $methodName
     *
     * @return    ReflectionMethod
     */
    public function getPrivateMethod($className, $methodName)
    {
        $reflector = new ReflectionClass($className);
        $method = $reflector->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * getPrivateProperty
     *
     * @param    string $className
     * @param    string $propertyName
     *
     * @return    ReflectionProperty
     */
    public function getPrivateProperty($className, $propertyName)
    {
        $reflector = new ReflectionClass($className);
        $property = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
    }

    /**
     * Not needed because L5 has "expectsEvents()"
     *
     * @param     $event
     * @param int $times
     */
    public function expectsEvent($event, $times = 1)
    {
        Event::shouldReceive('fire')
            ->times($times)
            ->withArgs([
                Mockery::type($event),
                Mockery::any(),
                Mockery::any()
            ]);
    }
}
