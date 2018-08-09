@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Pay another wallet</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        Wallet: {{ \Illuminate\Support\Facades\Auth::user()->wallet->key }}


                    </div>

                    <div id="app">
                        <wallet-payment wallet_key="{{ \Illuminate\Support\Facades\Auth::user()->wallet->key }} "></wallet-payment>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
