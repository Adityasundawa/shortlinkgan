<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicrositePopUnder extends Model
{
    use HasFactory;


    protected $fillable = ['url','microsite_link_id','percentage'];
    public function micrositeLink()
    {
        return $this->belongsTo(MicrositeLink::class);
    }


}
