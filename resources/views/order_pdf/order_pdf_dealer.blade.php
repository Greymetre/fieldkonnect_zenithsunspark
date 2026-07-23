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

        th,
        td {
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
                <td>Order Number</td>
                <td>{{ $order->orderno?$order->orderno:'' }}</td>
                <td>Order Date</td>
                <td>{{ $order->order_date?$order->order_date:'' }}</td>
            </tr>
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
                <td>{{ $order->buyers->sap_code }}</td>
                <td>BP Code</td>
                <td>{{ $order->sellers->sap_code }}</td>
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
                <th>Trade Dis %</th>
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
                <td>{{ $detail->products->productpriceinfo->discount }}</td>
                <td>{{ $detail->line_total }}</td>
            </tr>
            @php $ttq += $detail->quantity; @endphp
            @endforeach
        </tbody>
    </table>
    <table>
        <tbody>
            @if($order->product_cat_id == '2')
            <tr>
                <th colspan="3">DOD Discount %</th>
                <th colspan="2">{{$order->dod_discount ?? ''}}</th>
                <th>{{$order->dod_discount_amount ?? ''}}</th>
            </tr>
            <tr>
                <th colspan="3">Special Distribution Discount %</th>
                <th colspan="2">{{$order->special_distribution_discount ?? ''}}</th>
                <th>{{$order->special_distribution_discount_amount	 ?? ''}}</th>
            </tr>
            <tr>
                <th colspan="3">Distribution Margin Discount%</th>
                <th colspan="2">{{$order->distribution_margin_discount ?? ''}}</th>
                <th>{{$order->distribution_margin_discount_amount ?? ''}}</th>
            </tr>
            @if($order->fan_extra_discount && $order->fan_extra_discount != '' && $order->fan_extra_discount != NULL)
            <tr>
                <th colspan="3">Extra Discount %</th>
                <th colspan="2">{{$order->fan_extra_discount}}</th>
                <th>{{$order->fan_extra_discount_amount ?? ''}}</th>
            </tr>
            @endif
            @if($order->cash_discount && $order->cash_discount != '' && $order->cash_discount != NULL)
            <tr>
                <th colspan="3">Cash Discount %</th>
                <th colspan="2">{{$order->cash_discount}}</th>
                <th>{{$order->cash_amount ?? ''}}</th>
            </tr>
            @endif
            <tr>
                <th colspan="3">total Discount %</th>
                <th colspan="2">{{$order->total_fan_discount ?? ''}}</th>
                <th>{{$order->total_fan_discount_amount ?? ''}}</th>
            </tr>
            @elseif($order->product_cat_id == '1')
            <tr>
                <th colspan="5">Scheme Discount</th>
                <th>{{$order->schme_amount}}</th>
            </tr>
            <tr>
                <th colspan="4">EBD Discount</th>
                <th>{{$order->ebd_discount}}</th>
                <th>{{$order->ebd_amount}}</th>
            </tr>
            <tr>
                <th colspan="4">MOU Discount%</th>
                <th>{{$order->distributor_discount}}</th>
                <th>{{$order->distributor_amount}}</th>
            </tr>
            <tr>
                <th colspan="4">Special Discount%</th>
                <th>{{$order->special_discount}}</th>
                <th>{{$order->special_amount}}</th>
            </tr>
            <tr>
                <th colspan="4">Frieght Discount%</th>
                <th>{{$order->frieght_discount}}</th>
                <th>{{$order->frieght_amount}}</th>
            </tr>
            <tr>
                <th colspan="4">Cluster Discount%</th>
                <th>{{$order->cluster_discount}}</th>
                <th>{{$order->cluster_amount}}</th>
            </tr>
            <tr>
                <th colspan="4">Deal Discount%</th>
                <th>{{$order->deal_discount}}</th>
                <th>{{$order->deal_amount}}</th>
            </tr>
            @if($order->cash_discount && $order->cash_discount != '' && $order->cash_discount != NULL)
            <tr>
                <th colspan="4">Cash Discount %</th>
                <th>{{$order->cash_discount}}</th>
                <th>{{$order->cash_amount ?? ''}}</th>
            </tr>
            @endif
            @endif
            <tr>
                <th colspan="5">Sub Total</th>
                <th>{{$order->sub_total}}</th>
            </tr>

            @if($order->gst5_amt && $order->gst5_amt != '' && $order->gst5_amt != NULL)
            <tr>
                <th colspan="5">5% Tax</th>
                <th>{{$order->gst5_amt}}</th>
            </tr>
            @endif
            @if($order->gst12_amt && $order->gst12_amt != '' && $order->gst12_amt != NULL)
            <tr>
                <th colspan="5">12% Tax</th>
                <th>{{$order->gst12_amt}}</th>
            </tr>
            @endif
            @if($order->gst18_amt && $order->gst18_amt != '' && $order->gst18_amt != NULL)
            <tr>
                <th colspan="5">18% Tax</th>
                <th>{{$order->gst18_amt}}</th>
            </tr>
            @endif
            @if($order->gst28_amt && $order->gst28_amt != '' && $order->gst28_amt != NULL)
            <tr>
                <th colspan="5">28% Tax</th>
                <th>{{$order->gst28_amt}}</th>
            </tr>
            @endif
          
            <tr>
                <th colspan="5">Total Order Value</th>
                <th>{{$order->grand_total}}</th>
            </tr>
            <tr>
                <th colspan="6">Remark - {{$order->order_remark}}</th>
            </tr>
        </tbody>
    </table>

</body>

</html>