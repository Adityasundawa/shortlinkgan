<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabelShortlink extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'short_links_id',
        'labels_id',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function shortLink()
    {
        return $this->belongsTo(ShortLink::class, 'short_links_id');
    }

    public function label()
    {
        return $this->belongsTo(Label::class, 'labels_id');
    }
}
