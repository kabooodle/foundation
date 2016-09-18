<?php

namespace Kabooodle\Tests;

use Evals\App\Library\Commandr\CommandTranslator;
use Evals\Tests\App\AbstractUnitTestCase;
use Mockery;
use Evals\App\Library\Commandr\Command;

abstract class AbstractCommandTest extends TestCase
{
    /**
     * @group wattt
     */
    public function testCommandTranslatorAndThatHandlerExists()
    {
        $className = $this->classString;
        $reflectionClass = new \ReflectionClass($className);
        $command = $reflectionClass->newInstanceWithoutConstructor($className);
        $commandTranslator = new CommandTranslator();
        $handler = $commandTranslator->getCommandHandler($command);
        $this->assertTrue(
            class_exists($handler),
            'Command Handler does not exist: ' . $handler
        );

    }

    public function testCommandNamespace()
    {
        $expected = $this->classNamespaceString;

        $this->assertSame($expected, $this->classString);
    }

    public function testCommandClassInstantiation()
    {
        $command = Mockery::mock($this->classString);

        $this->assertInstanceOf($this->classString, $command);
    }

    public function testCommandClassExtendsCommand()
    {
        $command = Mockery::mock($this->classString);

        $this->assertInstanceOf(Command::class, $command);
    }

    public function testCommandHasProperMethodsAndGettersReturnWhatGiven()
    {
        // Data order in this array must match data order in instantiation of command
        $data = $this->getData();
        $instantiationDataArray = [];
        foreach($data as $v) {
            $instantiationDataArray[] = $v['value'];
        }
        $command = new $this->classString(...$instantiationDataArray);

        foreach($data as $assertData) {
            $this->assertSame(
                $assertData['value'],
                $command->{$assertData['methodName']}(),
                'Failed asserting that method ' . $assertData['methodName'] . '() returns what given'
            );
        }
    }
}
