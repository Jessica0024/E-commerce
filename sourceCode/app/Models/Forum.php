<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Forum extends Model
{
    protected $table = 'forum'; // If your table is named 'forum'

    protected $fillable = [
        'live_stream_id', // Add 'live_stream_id' to the fillable attributes
        'user_id',
        'content'
    ];

    // Define the relationship with the user who posted the comment
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Define the relationship with the live stream the comment belongs to
    public function liveStream()
    {
        return $this->belongsTo(LiveStream::class);
    }
}