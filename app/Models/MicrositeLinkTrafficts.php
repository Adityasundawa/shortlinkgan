<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicrositeLinkTrafficts extends Model
{
    use HasFactory;
    protected $fillable = ['date', 'unique_visitor_day', 'visitor_day', 'microsite_links_id', 'domain_decentralizes_id','fingerprint_id','city','country','device_type'];
    public function micrositeLink()
    {
        return $this->belongsTo(MicrositeLink::class);
    }
}
