<?php

namespace App\Http\Controllers\Api;

use App\Block;
use App\BlockChain;
use App\Helpers\Coin;
use App\Transaction;
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




}
