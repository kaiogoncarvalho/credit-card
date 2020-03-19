<?php
namespace Tests\Traits;

use Illuminate\Contracts\Console\Kernel;

trait DatabaseMigrationsOnce
{
    private static $migrated = false;
    
    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrationsOnce()
    {
        if(!self::$migrated){
            $this->artisan('migrate:fresh');
            $this->app[Kernel::class]->setArtisan(null);
            self::$migrated = true;
        }
        
    }
}
