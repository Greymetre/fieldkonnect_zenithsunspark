<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <style>
        /* Define your CSS styles for the PDF here */
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Order Details</h1>
    <table>
        <thead>
            <tr>
                <th colspan="2">Buyer Details</th>
                <th colspan="2">Seller Details</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Name</td>
                <td>{{ $order->buyers->name }}</td>
                <td>Name</td>
                <td>{{ $order->sellers->name }}</td>
            </tr>
            <tr>
                <td>BP Code</td>
                <td>{{ $order->buyers->sap_code ?? '-' }}</td>
                <td>BP Code</td>
                <td>{{ $order->sellers->sap_code ?? '-' }}</td>
            </tr>
            <tr>
                <td>Mobile Number</td>
                <td>{{ $order->buyers->mobile }}</td>
                <td>Mobile Number</td>
                <td>{{ $order->sellers->mobile }}</td>
            </tr>
            <tr>
                <td>Address</td>
                <td>{{ $order->buyers->customeraddress?$order->buyers->customeraddress->address1:'' }}</td>
                <td>Address</td>
                <td>{{ $order->sellers->customeraddress?$order->sellers->customeraddress->address1:'' }}</td>
            </tr>
        </tbody>
    </table>
    

    <h2>Product Details</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Rate(LP)</th>
                <th>Tax%</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $ttq = 0; @endphp
            @foreach($order->orderdetails as $detail)
            <tr>
                <td>{{ $detail->products->product_name }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ $detail->products->productpriceinfo->mrp }}</td>
                <td>{{ $detail->products->productpriceinfo->gst }}</td>
                <td>{{ $detail->line_total }}</td>
            </tr>
            @php $ttq += $detail->quantity; @endphp
            @endforeach
            <tr><th colspan="5"> </th></tr>
            <tr>
                <th colspan="2">Sub Total</th>
                <th colspan="3">{{$order->sub_total}}</th>
            </tr>
            @if($order->gst5_amt && $order->gst5_amt != '' && $order->gst5_amt != NULL)
            <tr>
                <th colspan="2">5% Tax</th>
                <th colspan="3">{{$order->gst5_amt}}</th>
            </tr>
            @endif
            @if($order->gst12_amt && $order->gst12_amt != '' && $order->gst12_amt != NULL)
            <tr>
                <th colspan="2">12% Tax</th>
                <th colspan="3">{{$order->gst12_amt}}</th>
            </tr>
            @endif
            @if($order->gst18_amt && $order->gst18_amt != '' && $order->gst18_amt != NULL)
            <tr>
                <th colspan="2">18% Tax</th>
                <th colspan="3">{{$order->gst18_amt}}</th>
            </tr>
            @endif
          
          
            <tr>
                <th colspan="2">Total Order Value</th>
                <th colspan="3">{{$order->grand_total}}</th>
            </tr>
            <tr><th colspan="5"> </th></tr>
            <tr>
                <th colspan="5">Remark - {{$order->order_remark}}</th>
            </tr>
        </tbody>
    </table>

</body>
</html>
