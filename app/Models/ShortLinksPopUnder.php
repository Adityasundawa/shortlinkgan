<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortLinksPopUnder extends Model
{
    use HasFactory;
    protected $fillable = ['url','short_links_id','count','precentage'];
    public function shortLink()
    {
        return $this->belongsTo(ShortLink::class, 'short_links_id');
    }
}
