<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

   protected $fillable = [
        'user_id', // BARU
        'name',
        'description', // BARU
        'is_active', // BARU
    ];

    // Pastikan relasi user ada
    public function user()
    {
       return $this->belongsTo(User::class); 
    }

    /**
     * Mendapatkan data user yang memiliki campaign ini.
     */
    // public function user()
    // {
    //    return $this->belongsTo(User::class); // Mungkin Anda butuh ini nanti
    // }

    /**
     * Mendapatkan semua short link yang terkait dengan campaign ini.
     */
    public function shortLinks()
    {
        // Ini sudah benar
        return $this->hasMany(ShortLink::class);
    }
}
