<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortLinkTraffict extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'unique_visitor_day', 'visitor_day', 'short_links_id', 'domain_decentralizes_id', 'country', 'city', 'device_type', 'fingerprint_id'];

    public function shortLink()
    {
        return $this->belongsTo(ShortLink::class);
    }

    public function domainDecentralize()
    {
        return $this->belongsTo(DomainDecentralize::class, 'domain_decentralizes_id');
    }
}
