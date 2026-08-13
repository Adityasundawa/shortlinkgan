<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicrositeLinkButtonTraffict extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'microsite_link_buttons_id',
    ];

    public function button()
    {
        return $this->belongsTo(MicrositeLinkButton::class, 'microsite_link_buttons_id');
    }
}
