<?php

use Illuminate\Database\Seeder;
use App\TimeEntry;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(TimeEntry::class,10)->create();
    }
}
