<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{

    protected $fillable = ['user_id','key'];

    public function transactions() {
        return $this->hasMany('App\Transaction')->where('fromAddress', $this->key)->orWhere('toAddress', $this->key);
    }
}
