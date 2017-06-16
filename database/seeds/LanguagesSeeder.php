<?php

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('languages')->delete();

        Language::create([
            'name' => 'Русский',
            'slug' => 'ru'
        ]);
        Language::create([
            'name' => 'English',
            'slug' => 'en'
        ]);
    }
}
