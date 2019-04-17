<?php

namespace App\Test;

use App\RobotArm;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RobotArmTest extends TestCase
{
    /** @var string */
    protected $commandsFileName;

    public function setUp()
    {
        parent::setUp();

        $this->commandsFileName = __DIR__ . '/files/commands.txt';
    }

    /** @test */
    public function initTest()
    {
        $robotArm = new RobotArm($this->commandsFileName);

        $propStack      = $this->getProperty('stack');
        $propPositions  = $this->getProperty('positions');
        $propCommands   = $this->getProperty('commands');
        $stack          = $propStack->getValue($robotArm);
        $positions      = $propPositions->getValue($robotArm);
        $commands       = $propCommands->getValue($robotArm);

        $this->assertEquals(10, count($stack));
        $this->assertEquals(10, count($positions));
        $this->assertEquals(8,  count($commands));

        for($i = 0; $i < 10; $i++)
        {
            $this->assertEquals("[$i]", $stack[$i]);
            $this->assertEquals($i, $positions[$i]);
        }

        $expectedCommands = [
            'moveAOntoB(0, 9)',
            'pileAOntoB(9, 1)',
            'moveAOverB(8, 1)',
            'pileAOverB(5, 7)',
            'moveAOverB(6, 7)',
            'moveAOntoB(5, 9)',
            'moveAOntoB(4, 7)',
            'pileAOntoB(7, 9)',
        ];

        foreach($expectedCommands as $i => $cmd) {
            $this->assertEquals($cmd, sprintf('%s(%s, %s)', $commands[$i][0], $commands[$i][1], $commands[$i][2]));
        }
    }

    /** @test */
    public function commandTest()
    {
        $robotArm   = new RobotArm($this->commandsFileName);

        $propStack  = $this->getProperty('stack');
        $moveAOntoB = $this->getMethod('moveAOntoB');
        $moveAOverB = $this->getMethod('moveAOverB');
        $pileAOntoB = $this->getMethod('pileAOntoB');
        $pileAOverB = $this->getMethod('pileAOverB');

        //Command 1
        $moveAOntoB->invokeArgs($robotArm, [0, 9]);

        $stack = $propStack->getValue($robotArm);

        $this->assertEquals('',         $stack[0]);
        $this->assertEquals('[1]',      $stack[1]);
        $this->assertEquals('[2]',      $stack[2]);
        $this->assertEquals('[3]',      $stack[3]);
        $this->assertEquals('[4]',      $stack[4]);
        $this->assertEquals('[5]',      $stack[5]);
        $this->assertEquals('[6]',      $stack[6]);
        $this->assertEquals('[7]',      $stack[7]);
        $this->assertEquals('[8]',      $stack[8]);
        $this->assertEquals('[9] [0]',  $stack[9]);

        //Command 2
        $pileAOntoB->invokeArgs($robotArm, [9, 1]);

        $stack = $propStack->getValue($robotArm);

        $this->assertEquals('',             $stack[0]);
        $this->assertEquals('[1] [9] [0]',  $stack[1]);
        $this->assertEquals('[2]',          $stack[2]);
        $this->assertEquals('[3]',          $stack[3]);
        $this->assertEquals('[4]',          $stack[4]);
        $this->assertEquals('[5]',          $stack[5]);
        $this->assertEquals('[6]',          $stack[6]);
        $this->assertEquals('[7]',          $stack[7]);
        $this->assertEquals('[8]',          $stack[8]);
        $this->assertEquals('',             $stack[9]);

        //Command 3
        $moveAOverB->invokeArgs($robotArm, [8, 1]);

        $stack = $propStack->getValue($robotArm);

        $this->assertEquals('',                 $stack[0]);
        $this->assertEquals('[1] [9] [0] [8]',  $stack[1]);
        $this->assertEquals('[2]',              $stack[2]);
        $this->assertEquals('[3]',              $stack[3]);
        $this->assertEquals('[4]',              $stack[4]);
        $this->assertEquals('[5]',              $stack[5]);
        $this->assertEquals('[6]',              $stack[6]);
        $this->assertEquals('[7]',              $stack[7]);
        $this->assertEquals('',                 $stack[8]);
        $this->assertEquals('',                 $stack[9]);

        //Command 4
        $pileAOverB->invokeArgs($robotArm, [5, 7]);

        $stack = $propStack->getValue($robotArm);

        $this->assertEquals('',                 $stack[0]);
        $this->assertEquals('[1] [9] [0] [8]',  $stack[1]);
        $this->assertEquals('[2]',              $stack[2]);
        $this->assertEquals('[3]',              $stack[3]);
        $this->assertEquals('[4]',              $stack[4]);
        $this->assertEquals('',                 $stack[5]);
        $this->assertEquals('[6]',              $stack[6]);
        $this->assertEquals('[7] [5]',          $stack[7]);
        $this->assertEquals('',                 $stack[8]);
        $this->assertEquals('',                 $stack[9]);

        //Command 5
        $moveAOverB->invokeArgs($robotArm, [6, 7]);

        $stack = $propStack->getValue($robotArm);

        $this->assertEquals('',                 $stack[0]);
        $this->assertEquals('[1] [9] [0] [8]',  $stack[1]);
        $this->assertEquals('[2]',              $stack[2]);
        $this->assertEquals('[3]',              $stack[3]);
        $this->assertEquals('[4]',              $stack[4]);
        $this->assertEquals('',                 $stack[5]);
        $this->assertEquals('',                 $stack[6]);
        $this->assertEquals('[7] [5] [6]',      $stack[7]);
        $this->assertEquals('',                 $stack[8]);
        $this->assertEquals('',                 $stack[9]);

        //Command 6
        $moveAOntoB->invokeArgs($robotArm, [5, 9]);

        $stack = $propStack->getValue($robotArm);

        $this->assertEquals('[0]',          $stack[0]);
        $this->assertEquals('[1] [9] [5]',  $stack[1]);
        $this->assertEquals('[2]',          $stack[2]);
        $this->assertEquals('[3]',          $stack[3]);
        $this->assertEquals('[4]',          $stack[4]);
        $this->assertEquals('',             $stack[5]);
        $this->assertEquals('[6]',          $stack[6]);
        $this->assertEquals('[7]',          $stack[7]);
        $this->assertEquals('[8]',          $stack[8]);
        $this->assertEquals('',             $stack[9]);

        //Command 7
        $moveAOntoB->invokeArgs($robotArm, [4, 7]);

        $stack = $propStack->getValue($robotArm);

        $this->assertEquals('[0]',          $stack[0]);
        $this->assertEquals('[1] [9] [5]',  $stack[1]);
        $this->assertEquals('[2]',          $stack[2]);
        $this->assertEquals('[3]',          $stack[3]);
        $this->assertEquals('',             $stack[4]);
        $this->assertEquals('',             $stack[5]);
        $this->assertEquals('[6]',          $stack[6]);
        $this->assertEquals('[7] [4]',      $stack[7]);
        $this->assertEquals('[8]',          $stack[8]);
        $this->assertEquals('',             $stack[9]);

        //Command 7
        $pileAOntoB->invokeArgs($robotArm, [7, 9]);

        $stack = $propStack->getValue($robotArm);

        $this->assertEquals('[0]',              $stack[0]);
        $this->assertEquals('[1] [9] [7] [4]',  $stack[1]);
        $this->assertEquals('[2]',              $stack[2]);
        $this->assertEquals('[3]',              $stack[3]);
        $this->assertEquals('',                 $stack[4]);
        $this->assertEquals('[5]',              $stack[5]);
        $this->assertEquals('[6]',              $stack[6]);
        $this->assertEquals('',                 $stack[7]);
        $this->assertEquals('[8]',              $stack[8]);
        $this->assertEquals('',                 $stack[9]);
    }

    /** @test */
    public function testExec()
    {
        $robotArm = new RobotArm($this->commandsFileName);

        $stack = $robotArm->exec();

        $this->assertEquals('[0]',              $stack[0]);
        $this->assertEquals('[1] [9] [7] [4]',  $stack[1]);
        $this->assertEquals('[2]',              $stack[2]);
        $this->assertEquals('[3]',              $stack[3]);
        $this->assertEquals('',                 $stack[4]);
        $this->assertEquals('[5]',              $stack[5]);
        $this->assertEquals('[6]',              $stack[6]);
        $this->assertEquals('',                 $stack[7]);
        $this->assertEquals('[8]',              $stack[8]);
        $this->assertEquals('',                 $stack[9]);
    }

    /**
     * @param $propertyName
     * @return \ReflectionProperty
     */
    private function getProperty($propertyName)
    {
        $reflector = new ReflectionClass(RobotArm::class);
        $property  = $reflector->getProperty( $propertyName );

        $property->setAccessible(true);

        return $property;
    }

    /**
     * @param $methodName
     * @return \ReflectionMethod
     */
    public function getMethod($methodName)
    {
        $reflector = new ReflectionClass(RobotArm::class);
        $method    = $reflector->getMethod($methodName);

        $method->setAccessible(true);

        return $method;
    }
}