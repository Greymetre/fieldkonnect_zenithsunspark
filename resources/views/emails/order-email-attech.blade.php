<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORDER {{$order->getuserdetails->getdivision!=null?$order->getuserdetails->getdivision->division_name:'-'}} Order Number : {{$order->orderno}}, Order Date : {{date('d-m-y', strtotime($order->order_date))}}</title>
</head>
<body>
    <p>
    Dear Sir, <br> <br>

    Please find the attachment order file <br> <br>
    
    
    Party Name  -#({{$order->buyers->customertypes->customertype_name}}) - {{$order->buyers->name}}# - {{$order->buyers->customeraddress->cityname->city_name}} <br> <br>
    
    Thanks
    <br>
    Created By Username
    <br>
    <b>{{$order->createdbyname->name}}</b>
    </p>
</body>
</html>
