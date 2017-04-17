@extends('layouts.app')

@section('view.stylesheet')
<style>

</style>
@endsection

@section('content')
@if (isset($sidebar_content))
    <div class="container-fluid">
@else
    <div class="container">
@endif
    <div class="row">
        <div>
        <!-- <div class="col-md-10 col-md-offset-1"> -->
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h3 class="panel-title pull-left" style="padding-top: 7.5px;">{!! $panel_header !!}</h3>
                    @if (isset($panel_dropdown))
                        <div class="pull-right">
                            {!! $panel_dropdown !!}
                        </div>
                    @endif
                </div>
                <div class="panel-body">
                    {!! $content !!}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('view.scripts')
<script src="{{ asset('assets/js/web3.js') }}"></script>
<script src="{{ asset('assets/js/uport-connect.js') }}"></script>
<script type="text/javascript">
    const Connect = window.uportconnect.Connect;
	const appName = 'nosh';
	const connect = new Connect(appName);
	const web3 = connect.getWeb3();
	const globalState = {
		uportId: "0x962517e3d2cbc1d0410876d975316edc3385dfe6",
		txHash: "",
		sendToAddr: "0x7d86a87178d28f805716828837D1677Fb7aF6Ff7", //back to B9 testnet faucet
		sendToVal: "1"
	};
    const value = parseFloat(globalState.sendToVal) * 1.0e18;
    const gasPrice = 100000000000;
    const gas = 500000;
    const hash = '{!! $hash !!}';
    var ether_go = false;
	const uportConnect = function () {
        web3.eth.getCoinbase((error, address) => {
			if (error) { throw error; }
			console.log(address);
			globalState.uportId = address;
		});
	};
	const sendEther = () => {
        web3.eth.sendTransaction(
			{
				from: globalState.uportId,
				to: globalState.sendToAddr,
				value: value,
				gasPrice: gasPrice,
				gas: gas,
                data: hash
			},
			(error, txHash) => {
				if (error) { throw error; }
				globalState.txHash = txHash;
                $.ajax({
    				type: "POST",
    				url: '{!! $ajax !!}',
    				data: 'txHash=' + txHash,
    				dataType: 'json',
    				success: function(data){
    					if (data.message !== 'OK') {
    						toastr.error(data.message);
    						// console.log(data);
    					} else {
    						window.location = data.url;
    					}
    				}
    			});
				console.log(txHash);
			}
		);
	};
    $(document).ready(function() {
        // Core
        if (noshdata.message_action !== '') {
            if (noshdata.message_action.search('Error - ') == -1) {
                toastr.success(noshdata.message_action);
            } else {
                toastr.error(noshdata.message_action);
            }
        }
        sendEther();
        // setInterval(send_ether, 1000);
    });
</script>
@endsection
