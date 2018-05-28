<?php

use Illuminate\Database\Seeder;

class CreateGenesisBlock extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $blockchain = new \App\BlockChain();
        $blockchain->save();

        $block = new \App\Block();
        $block->id = \App\Block::count() + 1;
        $block->block_chain_id = $blockchain->id;
        $block->nonce = 0;
        $block->hash = $block->calculateHash();
        $block->save();


        $transaction = new \App\Transaction();
        $transaction->fromAddress = "Genesis";
        $transaction->toAddress = "Genesis";
        $transaction->amount = 1000;
        $transaction->block_id = null;
        $transaction->status = 0;
        $transaction->save();


        $block->hash = $block->calculateHash();
        $block->save();


    }
}
