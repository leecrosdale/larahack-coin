<?php

namespace App\Http\Controllers\Api;

use App\Block;
use App\BlockChain;
use App\Helpers\Coin;
use App\Transaction;
use App\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BlockController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {



        $transactions = json_decode($request->input('transactions'));
        $hash = $request->input('hash');
        $previousHash  = $request->input('previousHash');
        $timeStamp = $request->input('timeStamp');
        $nonce = $request->input('nonce');
        $winnerKey = $request->input('wallet_key');

        $failed = false;

        if ($transactions) {
            foreach ($transactions as $transaction) {
                $check = Transaction::where('status', 0)->where('id', $transaction->id)->first();

                if (!$failed && !$check) {
                    $failed = true;
                }
            }
        }

        if ($failed) {
            return response()->json(['status' => 'fail', 'reason' => 'Pending transactions were already mined']);
        }

        $recalc = Coin::calcHash(config("coin.difficulty") . Block::all()->last()->hash . $timeStamp . $request->input('transactions') . $nonce);

        if ($recalc != $hash) {
            return response()->json(['status' => 'fail', 'reason' => "Hashes don't match on recheck"]);
        }

        $block = new Block();
        $block->hash = $recalc;
        $block->previousHash = $previousHash;
        $block->block_chain_id = BlockChain::first()->id;
        $block->timestamp = $timeStamp;
        $block->nonce = $nonce;
        $block->save();

        foreach ($transactions as $transaction) {
            $check = Transaction::where('status', 0)->where('id', $transaction->id)->first();
            $check->status = 1;
            $check->save();

            if (($check->amount / 100 * 2) > 0.01) {

                Transaction::create([
                    'fromAddress' => 'reward',
                    'toAddress' => $winnerKey,
                    'amount' => ($check->amount / 100 * 2)
                ]);

            }

        }

        // Create transaction reward
        Transaction::create([
            'fromAddress' => 'reward',
            'toAddress' => $winnerKey,
            'amount' => config('coin.mining_reward')
        ]);


        return response()->json(['status' => 'success', 'reason' => 'You got the block!']);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }


    public function pay(Request $request) {

        $userWallet = Auth::user()->wallet;

        if (!$userWallet) {
            return response()->json(['status' => 'error', 'message' => "Your wallet isn't set up, contact admin"]);
        }

        $amount = $request->input('amount');

        if ($amount <= 0) {
            return response()->json(['status' => 'error', 'message' => "Must be a positive amount"]);
        }

        $toWallet = Wallet::where('key',$request->input('toWallet'))->first();

        if (!$toWallet) {
            return response()->json(['status' => 'error', 'message' => "Wallet doesn't exist"]);
        }

        $balance = Auth::user()->balance();

        if ($balance < $amount) {
            return response()->json(['status' => 'error', 'message' => 'You do not have enough coin']);
        }


        Transaction::create([
            'fromAddress' => $userWallet->key,
            'toAddress' => $toWallet->key,
            'amount' => $amount
        ]);

        return response()->json(['status' => 'ok', 'message' => 'Transaction Complete']);


    }




}
