<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $fillable = [
        'block_id',
        'fromAddress',
        'toAddress',
        'amount'
    ];

    public function fromWallet() {
        return $this->belongsTo('App\Wallet','fromAddress','key');
    }

    public function toWallet() {
        return $this->belongsTo('App\Wallet','toAddress','key');
    }

    public function block() {
        return $this->belongsTo('App\Block');
    }
}
