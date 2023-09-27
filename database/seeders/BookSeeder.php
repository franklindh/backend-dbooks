<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 100; $i++) {
            Book::insert([
                [
                    'id' => Str::uuid(),
                    'judul' => 'udin.customer' . $i,
                    'penulis' => 'Udin',
                    'penerbit' => 'A',
                    'tahun_terbit' => 2023,
                    'isbn' => Str::random(13),
                    'created_at' => now()
                ]
            ]);
        }
    }
}
