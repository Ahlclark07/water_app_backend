<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ConsommationJournaliere extends Model
{
    use HasFactory;

    protected $table = 'consommation_journaliere';

    protected $fillable = [
        'user_id',
        'date',
        'consommation',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->attributes['date'])->locale('fr')->isoFormat('ddd');
    }
}
