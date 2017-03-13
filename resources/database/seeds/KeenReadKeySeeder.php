<?php

use Kabooodle\Models\User;
use Illuminate\Database\Seeder;
use Kabooodle\Services\Keen\KeenService;

class KeenReadKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function(){

            $keenService = app()->make(KeenService::class);

            $users = User::all();
            foreach($users as $user){
                if ($user->subscriptions->count() > 0) {
                    $user->keen_access_key = $keenService->createScopedKeyForUser($user);
                    $user->save();
                }
            }
        });
    }
}
