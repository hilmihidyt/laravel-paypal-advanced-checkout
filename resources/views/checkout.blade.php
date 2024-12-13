<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Checkout</title>
    <script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_CLIENT_ID') }}&components=buttons"></script>
</head>
<body>
    <h1>Checkout</h1>
    <div id="paypal-button"></div>

<script>
    paypal.Buttons({
        createOrder: async () => {
            const response = await fetch('{{ route('create.order') }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ amount: 100 }) // Ubah jumlah sesuai kebutuhan
            });

            const order = await response.json();
            return order.id;
        },
        onApprove: async (data) => {
            const response = await fetch('{{ route('capture.order') }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ orderId: data.orderID })
            });

            const result = await response.json();
            alert('Payment successful: ' + result.id);
        },
    }).render('#paypal-button');
</script>

</body>
</html>
