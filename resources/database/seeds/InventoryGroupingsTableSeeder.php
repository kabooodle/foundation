<?php

use Illuminate\Database\Seeder;

class InventoryGroupingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(InventoryTableSeeder::class);

        $users = \Kabooodle\Models\User::all();
        foreach ($users as $user) {
            $user->inventoryGroupings()->saveMany(factory(\Kabooodle\Models\InventoryGrouping::class, 10)->create(['user_id' => $user->id])->all());
            foreach($user->inventoryGroupings as $grouping) {
                $grouping->inventoryItems()->sync($user->inventory->random(random_int(2, 5))->pluck('id')->all());
            }
        }
    }
}
