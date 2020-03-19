<?php
namespace Tests\Traits;

use Illuminate\Contracts\Console\Kernel;

trait CreateDatabaseTest
{
    private static $created = false;
    
    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runCreateDatabaseTest()
    {
        if(!self::$created){
            $this->artisan('create-database:test');
            $this->app[Kernel::class]->setArtisan(null);
            self::$created = true;
        }
        
    }
}
