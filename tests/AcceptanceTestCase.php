<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Traits\DatabaseMigrationsOnce;
use Tests\Traits\CreateDatabaseTest;

abstract class AcceptanceTestCase extends TestCase
{
    use DatabaseMigrationsOnce, DatabaseTransactions, CreateDatabaseTest;
    
    protected function setUpTraits(){
        $this->runCreateDatabaseTest();
        $this->runDatabaseMigrationsOnce();
        parent::setUpTraits();
    }
}
