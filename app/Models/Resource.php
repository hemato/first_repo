<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $table = 'resources'; // Tablo adı
    protected $primaryKey = 'id'; // Varsayılan birincil anahtar
    public $timestamps = true; // Zaman damgalarını kullan
    protected $fillable = [
        'unique_name',
        'price'
    ];

    // İlişkilendirdiğimiz Flip modeli
    public function flips()
    {
        return $this->belongsToMany(Flip::class, 'flip_resource')
            ->withPivot('count')
            ->withTimestamps();
    }
}
