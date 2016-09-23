<?php

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\DatabaseManager;
use Mockery as m;
use Recca0120\Terminal\Console\Commands\Mysql;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class MysqlTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_handle()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $databaseManager = m::mock(DatabaseManager::class);
        $connection = m::mock(ConnectionInterface::class);
        $command = new Mysql($databaseManager);
        $laravel = m::mock(Application::class);
        $command->setLaravel($laravel);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $databaseManager->shouldReceive('connection')->andReturn($connection);

        $connection
            ->shouldReceive('setFetchMode')->once()
            ->shouldReceive('select')->with('select * from users;')->andReturn([])->once()
            ->mock();

        $laravel
            ->shouldReceive('call')->once()->andReturnUsing(function ($command) {
                call_user_func($command);
            });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $command->run(new StringInput('--command="select * from users;"'), new NullOutput);
    }
}
