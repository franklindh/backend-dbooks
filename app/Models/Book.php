<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    protected $fillable = [
        'id',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'isbn',
        'created_at',
        'updated_at'
    ];
}
