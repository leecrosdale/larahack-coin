<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{

    protected $fillable = ['user_id','key'];

    public function transactions() {
        return Transaction::where('fromAddress', $this->key)->orWhere('toAddress', $this->key);
    }

    public function completedTransactions() {
        return Transaction::where('fromAddress', $this->key)->where('status',1)->orWhere('toAddress', $this->key)->where('status',1);
    }

}
