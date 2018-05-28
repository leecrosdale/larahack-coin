<?php

namespace App;

use App\Helpers\Coin;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{

    protected $fillable = [
        'id',
        'previousHash',
        'hash'
    ];

    public function calculateHash() {
        return Coin::calcHash($this->previousHash . $this->timestamp . $this->transactions()->get() . $this->nonce);
    }

    public function transactions() {
        return $this->hasMany('App\Transaction');
    }

    public function toArray()
    {

        return [
            'id' => $this->id,
            'transactions' => $this->transactions()->get()->toArray(),
            'previousHash' => $this->previousHash,
            'timestamp' => $this->timestamp,
            'hash' => $this->hash
        ];
    }

}
