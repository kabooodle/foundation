<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \Kabooodle\Models\User::all();
        if ($users->count() < 10) {
            factory(\Kabooodle\Models\User::class, 10 - $users->count())->create();
        }
    }
}
