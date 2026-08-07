<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicrositeLink extends Model
{
    use HasFactory;
    protected $fillable = [
        'short_url',
        'images',
        'title',
        'description',
        'meta',
        'users_id',
        'template_microsites_id',
        'images_background',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function buttons()
    {
        return $this->hasMany(MicrositeLinkButton::class, 'microsite_links_id');
    }

    protected function images(): Attribute
    {
        return Attribute::make(get: fn ($images) => asset('microsite/' . $images));
    }


    protected function imagesBackground(): Attribute
    {
        return Attribute::make(get: fn ($images) => asset('microsite/' . $images));
    }


    public function micrositePopUnder()
    {
        return $this->hasMany(MicrositePopUnder::class);
    }

    public function trafficts()
    {
        return $this->hasMany(MicrositeLinkTrafficts::class, 'microsite_links_id', 'id')
            ->selectRaw('microsite_links_id, SUM(visitor_day) as total_visitors')
            ->groupBy('microsite_links_id');
    }
}
