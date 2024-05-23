<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'message',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->locale('fr')->diffForHumans();
    }
}
