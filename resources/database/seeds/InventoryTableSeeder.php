<?php

use Illuminate\Database\Seeder;

class InventoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \Kabooodle\Models\User::all();
        if ($users->isEmpty()) {
            $this->call(UsersTableSeeder::class);
            $users = \Kabooodle\Models\User::all();
        }

        foreach ($users as $user) {
            $user->inventory()->saveMany(factory(\Kabooodle\Models\Inventory::class, 600)->create(['user_id' => $user->id])->all());
        }
    }
}
