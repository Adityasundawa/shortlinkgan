<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicrositeLinkButton extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'clicks',
        'microsite_links_id',

    ];

    public function link()
    {
        return $this->belongsTo(MicrositeLink::class, 'microsite_links_id');
    }

    public function micrositeLink()
    {
        return $this->belongsTo(MicrositeLink::class, 'microsite_links_id');
    }

    public function clickTrafficts()
    {
        return $this->hasMany(MicrositeLinkButtonTraffict::class, 'microsite_link_buttons_id');
    }
}
