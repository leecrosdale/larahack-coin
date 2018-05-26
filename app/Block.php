<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{

    protected $fillable = [
        'previousHash',
        'hash',
        'transactions'
    ];

    protected $casts = [
        'hash' => 'object'
    ];


    public function calculateHash() {
        return hash(CRYPT_SHA256, $this);
    }

    public function transactions() {
        return $this->hasMany('App\Transaction');
    }

}
