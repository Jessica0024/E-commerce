<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class qnas extends Model

{
    protected $table = 'qnas';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id', 'question', 'answer',
    ];

}
