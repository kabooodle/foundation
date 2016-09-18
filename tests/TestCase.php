<?php

namespace Kabooodle\Tests;

use DB;

use ReflectionClass;
use ReflectionMethod;
use ReflectionObject;
use ReflectionProperty;
use Kabooodle\Foundation\Application\KabooodleApplication;
use Illuminate\Foundation\Testing\TestCase as L_TestCase;

/**
 * Class TestCase
 * @package Kabooodle\Tests
 */
class TestCase extends L_TestCase
{

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://kabooodle.dev';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require_once __DIR__.'/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public static function setUpBeforeClass()
    {
        global $__fm;
        static::$fm = $__fm;

        Faker::setLocale('en_EN');
        DB::table('user_actions')->truncate();
        DB::beginTransaction();
    }

    public static function tearDownAfterClass()
    {
        static::$fm->deleteSaved();
        DB::rollBack();
    }

    /**
     * @return KabooodleApplication
     */
    public static function app()
    {
        global $__app;
        return $__app;
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
//        Mockery::close();
    }

    /**
     * getPrivateMethod
     *
     * @param 	string $className
     * @param 	string $methodName
     * @return	ReflectionMethod
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
     * @param 	string $className
     * @param 	string $propertyName
     * @return	ReflectionProperty
     */
    public function getPrivateProperty($className, $propertyName)
    {
        $reflector = new ReflectionClass($className);
        $property = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
    }
}
