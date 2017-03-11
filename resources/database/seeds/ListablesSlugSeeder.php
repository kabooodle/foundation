<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

use Illuminate\Database\Seeder;
use Kabooodle\Models\Listable;

class ListablesSlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $listables = Listable::all();
        if ($listables->count() > 0) {
            foreach ($listables as $listable){
                $listable->slug = $listable->getUUID();
                $listable->save();
            }
        }
    }
}
