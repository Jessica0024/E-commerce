<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'product_image',
        'product_name',
        'product_quantity',
        'product_price',
        'total_price',

        'name',
        'email',
        'phoneNo',
        'address_1',
        'address_2',
        'state',
        'zipcode',
        'payment_method',
    ]; 

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
