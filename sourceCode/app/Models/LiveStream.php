<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveStream extends Model
{     public function forum()
    {
        return $this->hasMany(Forum::class, 'live_stream_id');
    }
}
