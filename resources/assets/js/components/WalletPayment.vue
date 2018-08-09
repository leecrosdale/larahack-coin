<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card-default">
                    <div class="card-header">Make a payment</div>
                    <div class="card-body">
                        {{ message }}
                        <input class="form-control" type="text" v-model="toWallet" placeholder="Receiving Wallet" />
                        <input class="form-control" type="text" v-model="amount" placeholder="Amount" />
                        <button @click="performTransaction()">Confirm Payment</button>
                    </div>
                </div>
            </div>
        </div>

        <br/>

    </div>
</template>

<script>
    export default {
        data() {
            return {
                message: '',
                toWallet: '',
                amount: 0
            }
        },
        props: ['wallet_key'],
        computed: {
            checkStatus() {
                return mining_started;
            }
        },
        methods: {
            performTransaction() {
                var self = this;
                axios.post('wallet/pay',{
                    'toWallet': self.toWallet,
                    'amount' : self.amount
                }).then(response => {
                    console.log(response);
                    self.message = response.data.message;
                                        
                }).catch(error => {
                    console.log(error);
                    self.message = response.data.message;
                });
            }
        }
    }
</script>
