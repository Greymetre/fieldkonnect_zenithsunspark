<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSP Activity PDF</title>
    <style>
        table,
        td {
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
            table-layout: fixed;
            padding: 5px;
        }

        .signature {
            width: 100%;
            text-align: right;
        }

        .footer {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            text-align: center;
            border-top: 2px solid #007bff;
            padding: 10px 20px;
            width: 100%;
            position: fixed;
            bottom: 0;
            left: 0;
        }

        .footer strong {
            font-size: 14px;
            color: #000;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .footer-content {
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <th><img width="180" src="{{$image}}" alt=""></th>
            <th><img width="80" src="{{$image2}}" alt=""></th>
            <th><img width="200" src="{{$image3}}" alt=""></th>
        </tr>
    </table>
    <div>
        <h5>Date: {{$date}}</h5>
    </div>
    <table>
        <tr>
            <td>M/s Silver Consumer Electricals Limited</td>
            <td>FROM: <br> M/s [{{$data->customer->sap_code}}]-{{$data->customer->name}} <br><br> {{$data->customer->full_address}} <br><br> GSTN: {{$data->customer?->customerdetails?->gstin_no}} <br> PAN: {{$data->customer?->customerdetails?->pan_no}}</td>
        </tr>
    </table>
    <div>
        <h5>Subject: Balance confirmation and No claim as on {{$date}}</h5>
        <p style="font-family: Arial, Helvetica, sans-serif;font-size: 12px;font-weight: 400;">We hereby confirm that all the accounts of our dealings with your company up to {{$date}} have been verified and entire process of reconciliation has been completed. We hereby admit the correction, confirm and acknowledge that an amount of Rs. INR ({{round($data->total_amounts, 2)}}) is due and payable by your Company to us. <br><br> We further acknowledge and confirm that no claim of our firm against your company as on {{$date}} and aforesaid balance is found due and <b>payable</b> by our firm to your company and we hereby undertake to pay the same. <br><br> Also we hereby confirm that our firm’s current GST No. PAN No. & Bank A/c No mentioned above are correct and there is no change, </p>
        <br>
        <h5>Below mentioned Documents are required while taking Balance Confirmation.</h5>
        <ul>
            <li>GST No.</li>
            <li>PAN No.</li>
            <li>Aadhar Card No.</li>
            <li>Cancelled Cheque No.</li>
            <li>Physical Stock Verification.</li>
        </ul>
        <div class="signature">
            {{$data->customer->name}} <br>
            [Dealer Signature with Stamp] <br><br>
            Date: ____________
        </div>
    </div>
    <div class="footer">
        <strong>SILVER CONSUMER ELECTRICALS LIMITED</strong>
        <div class="footer-content">
            <span><strong>Reg. Office :</strong> Rajkot-Gondal Highway, Nr. Kishan Petrol Pump, B/h. Magotteaux Industries Pvt. Ltd., Kangsiyali, Rajkot-360022, Gujarat (India).</span>
            <br>
            <span>CIN: U29100GJ2021PTC122633 | +91-99250 15610 | <a href="mailto:sales@silverpumps.com">sales@silverpumps.com</a> | <a href="https://www.silverglobal.com">www.silverglobal.com</a></span>
        </div>
    </div>
</body>

</html>