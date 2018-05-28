<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card-default">
                    <div class="card-header">Mining Control</div>
                    <div class="card-body">
                        <button @click="toggleMining()">{{ miningText() }}</button>
                    </div>
                </div>
            </div>
        </div>

        <hr/>

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card-default">
                    <div class="card-header">Current Local Block - Block Status: {{ mining_status }}</div>
                    <div class="card-body">
                        <label>Current Hash:</label> {{ hash }} <br/>
                        <label>Latest Block Hash:</label> {{ latest_block.hash }} <br/>
                        <label>Pending Transactions:</label> {{ pending_transactions.length }} <br/>
                        <label>Nonce:</label> {{ nonce}}
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
                'mining_started': false,
                'latest_block': 0,
                'pending_transactions': [],
                'hash': '',
                'nonce': 0,
                'difficulty': 5,
                'miningInterval': null,
                'timestamp': Date.now(),
                'mining_status': 'Waiting',
                'found': false,
                'checkingLatestBlockInterval': null,
            }
        },
        props: ['wallet_key'],
        computed: {
            checkStatus() {
                return mining_started;
            }
        },
        methods: {
            miningText() {
                return this.mining_started ? 'Stop Mining' : 'Start Mining'
            },
            toggleMining() {

                this.mining_started = !this.mining_started;

                if (this.mining_started) {

                    this.mining_status = "Retrieving Pending Transactions";
                    this.found = false;
                    this.pending_transactions = 0;
                    this.hash = '';


                    var self = this;
                    axios.get('api/transactions/pending').then(response => {
                        self.pending_transactions = response.data;


                        this.mining_status = "Mining";

                        this.getLatestBlock();
                        this.startMining()

                    }).catch(error => {
                        console.log(error);
                    });




                } else {

                    this.mining_status = "Stopping";

                    clearInterval(this.miningInterval);

                    if (this.found) {

                        this.mining_status = "Block Found! Pushing to chain";

                        var self = this;
                        axios.post('api/block/create',{
                            'previousHash': self.latest_block.hash,
                            'timeStamp': self.timestamp,
                            'transactions': JSON.stringify(this.pending_transactions),
                            'nonce': this.nonce,
                            'hash': this.hash,
                            'difficulty': this.difficulty,
                            'wallet_key': this.wallet_key
                        }).then(response => {
                            self.mining_status = response.data.status + ": " + response.data.reason;
                        }).catch(error => {
                            console.log(error);
                            this.mining_status = "Error, contact lee lol";
                        });

                    }
                }
            },
            startMining() {

               this.miningInterval = setInterval(() => {
                    this.mineBlock();
                },20);

                this.checkingLatestBlockInterval = setInterval(() => {
                    this.getLatestBlock();
                },60000);


            },

            mineBlock() {

                var val = Array(this.difficulty + 1).join("0");

                if (this.hash.substring(0, this.difficulty) !== val) {

                    this.nonce++;
                    this.hash = this.calculateHash();
                    val = Array(this.difficulty + 1).join("0");

                } else {
                    console.log("BLOCK MINED: " + this.hash);
                    this.found = true;
                    this.toggleMining();
                    this.toggleMining();
                }



            },
            getLatestBlock() {
                var self = this;
                return axios.get('api/block/latest').then(response => {

                    if (self.latest_block.hash != response.data.hash) {
                        self.latest_block = response.data;
                        self.toggleMining();
                        self.toggleMining();
                    }


                }).catch(error => {
                    console.log(error);
                });
            },
            calculateHash() {
                return SHA256(this.difficulty + this.latest_block.hash + this.timestamp + JSON.stringify(this.pending_transactions) + this.nonce).toString();
            }
        }
    }
</script>
