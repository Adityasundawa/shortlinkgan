<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_name', // <-- HAPUS INI
        'campaign_id',   // <-- TAMBAHKAN INI
        'description',
        'short_url',
        'original_url',
        'user_id',
        'meta',
        'utm_campaign',
        'utm_medium',
        'utm_source',
        'utm_content',
        'utm_term',
        'images_background',
        'use_play_button',
        'custom_title',
        'custom_description',
        'type_short_links',
        'is_popunder',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function labelShortlinks()
    {
        return $this->hasMany(LabelShortlink::class, 'short_links_id');
    }

    public function popUnders()
    {
        return $this->hasMany(ShortLinksPopUnder::class, 'short_links_id');
    }

    /**
     * Mendapatkan campaign yang memiliki short link ini.
     */
    public function campaign()
    {
        // Relasi ini sekarang akan berfungsi dengan benar
        return $this->belongsTo(Campaign::class);
    }

    // Relasi baru ke ShortLinkTraffict
    public function traffics()
    {
        // Asumsi foreign key di ShortLinkTraffict adalah short_links_id
        return $this->hasMany(ShortLinkTraffict::class, 'short_links_id');
    }

    // 🚨 ACCESSOR: Hitung Total Visitor per Short Link 🚨
    protected function totalVisitor(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->traffics->sum('visitor_day'),
        );
        // Hapus ->shouldCache() jika Anda menggunakan versi Laravel lama yang tidak mendukungnya
    }
}
