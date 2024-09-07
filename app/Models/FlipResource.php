<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlipResource extends Model
{
    use HasFactory;

    protected $table = 'flip_resource'; // Tablo adı
    public $timestamps = true; // Zaman damgalarını kullan
    protected $fillable = [
        'flip_id',
        'resource_id',
        'count'
    ];

    public function flip()
    {
        return $this->belongsTo(Flip::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}
