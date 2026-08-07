<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "slug",
        "id_folder_parent",
        "id_author"
    ];

    public function folderChildren(){
        return $this->hasMany(Folder::class, 'id_folder_parent', 'id');
    }

    public function parentFolder(){
        return $this->hasOne(Folder::class, 'id', 'id_folder_parent');
    }

    public function author(){
        return $this->hasOne(User::class, 'id', 'id_author');
    }
}
