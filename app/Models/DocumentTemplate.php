<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }
}
