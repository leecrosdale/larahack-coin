<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function wallet() {
        return $this->hasOne('App\Wallet');
    }

    public function balance() {
        $transactions = $this->wallet->completedTransactions()->get();

        $balance = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->fromAddress = $this->key) {
                $balance -= $transaction->amount;
            } else {
                $balance += $transaction->amount;
            }
        }

        return $balance;

    }

}
