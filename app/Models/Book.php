<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';
    
    protected $fillable = [
        'seqNum',
        'caption',
        'author',
        'pubCompany',
        'class',
        'lang',
        'subj',
        'subj_hex'
    ];

    public $timestamps = false;

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Define the relationship with the Order model
    public function orders()
    {
        return $this->hasMany(Order::class, 'productid'); // Assuming 'productid' is the foreign key
    }
} 