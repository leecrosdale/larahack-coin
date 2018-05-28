@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        Wallet: {{ \Illuminate\Support\Facades\Auth::user()->wallet->key }}


                    </div>

                    <div id="app">
                        <mining-component wallet_key="{{ \Illuminate\Support\Facades\Auth::user()->wallet->key }} "></mining-component>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
