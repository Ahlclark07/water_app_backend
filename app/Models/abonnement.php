<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonnement extends Model
{
    use HasFactory;
    protected $table = 'abonnements';

    protected $fillable = [
        'user_id',
        'titre',
        'total',
        'consommation',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
