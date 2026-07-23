<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSP Activity PDF</title>
    <style>
       table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed; /* Ensures proper alignment */
        }

        th, td {
            border: 1px solid black;
            padding: 6px;
            text-align: left;
            word-wrap: break-word; /* Prevents content from overflowing */
            font-size: 10px; /* Adjust font size for better fit */
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
     <h3 style="color: #7c7c7c;font-weight: bold !important;">{{isset($claimGeneration->service_center_details) ?  $claimGeneration->service_center_details->name : ''}} - {{$claimGeneration->claim_number ?? ''}}</h3>
    <hr>
    <table>
        <thead>
            <tr>
                <th>Comp No.</th>
                <th>Comp Date</th>
                <th>SB Approved Date</th>
                <th>Claim No</th>
                <th>Service</th>
                <th>Prod Sr</th>
                <th>Prod Name</th>
                <th>Prod Code</th>
                <th>HP</th>
                <th>Stage</th>
                <th>Phase</th>
                <th>Cust Bill Date</th>
                <th>Company Sale Bill Date</th>
                <th>BRANCH</th>
                <th>Repaired / Replacement</th>
                <th>Service Location</th>
                <th>Site Visit Category</th>
                <th>SERVICE CHARGE</th>
                <th>Site Visit Charge</th>
                <th>Rewinding Charge</th>
                <th>Local Spare Charges</th>
                <th>Ttl Charg (W/o Tax)</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($claimGeneration->claim_generation_details) && count($claimGeneration->claim_generation_details) > 0)
                @php
                    $service_charge_total = 0;
                    $site_visit_total = 0;
                    $rewinding_charge_total = 0;
                    $local_charge_total = 0;
                    $grand_total = 0;
                @endphp
                @foreach($claimGeneration->claim_generation_details as $claim_generation_detail)
                    <tr>
                        @php 
                            $service_charge_total = $service_charge_total + getServiceCharge($claim_generation_detail, 1) ?? 0;
                            $site_visit_total = $site_visit_total + getServiceCharge($claim_generation_detail, 3) ?? 0;
                            $rewinding_charge_total = $rewinding_charge_total+ getServiceCharge($claim_generation_detail, 5) ?? 0;
                            $local_charge_total = $local_charge_total + getServiceCharge($claim_generation_detail, 4) ?? 0;
                            $grand_total  = $grand_total + number_format(optional($claim_generation_detail->complaints->service_bill->service_bill_products)->sum('subtotal') ?? 0.0, 2, '.', '');
                        @endphp 
                        <td>{{$claim_generation_detail->complaints->complaint_number ?? ''}}</td>
                        <td>{{$claim_generation_detail->complaints->complaint_date ?? ''}}</td>
                        <td>{{ $claim_generation_detail['complaints']['service_bill']['status'] == 3 ? cretaDateForFront($claim_generation_detail['complaints']['service_bill']['updated_at']) : "Not Approved"; }}</td>
                        <td>{{$claim_generation_detail->claim->claim_number ?? ''}}</td>
                        <td>{{$claim_generation_detail->complaints->service_type ?? ''}}</td>
                        <td>{{$claim_generation_detail->complaints->product_serail_number ?? ''}}</td>
                        <td>{{$claim_generation_detail->complaints->product_name ?? ''}}</td>
                        <td>{{$claim_generation_detail->complaints->product_code ?? ''}}</td>
                        <td>{{$claim_generation_detail->complaints->specification ?? ''}}</td>
                        <td>{{$claim_generation_detail->complaints->product_no ?? ''}}</td>
                        <td>{{$claim_generation_detail->complaints->phase ?? ''}}</td>
                        <td>{{$claim_generation_detail->complaints->customer_bill_date ?? ''}}</td>
                        <td>{{isset($claim_generation_detail['complaints']['company_sale_bill_date']) ?  cretaDateForFront($claim_generation_detail['complaints']['company_sale_bill_date']) : '',}}</td>
                        <td>{{$claim_generation_detail->complaints->purchased_branch_details->branch_name ?? ''}}</td>
                        <td>{{$claim_generation_detail->complaints->complaint_number ?? ''}}</td>
                        <td>{{$claim_generation_detail->complaints->service_bill->service_location ?? ''}}</td>
                        <td>{{$claim_generation_detail->complaints->service_bill->category ?? ''}}</td>
                        <td>{{getServiceCharge($claim_generation_detail, 1) ?? ''}}</td>
                        <td>{{getServiceCharge($claim_generation_detail, 3) ?? ''}}</td>
                        <td>{{getServiceCharge($claim_generation_detail, 5) ?? ''}}</td>
                        <td>{{getServiceCharge($claim_generation_detail, 4) ?? ''}}</td>
                        <td>{{ number_format(optional($claim_generation_detail->complaints->service_bill->service_bill_products)->sum('subtotal') ?? 0.0, 2, '.', '') }}</td>
                    </tr>
                @endforeach
                    <tr style="font-weight: bold;">
                        <td colspan="17" style="text-align: right;">Total</td>
                        <td>{{ number_format($service_charge_total ?? 0.0, 2, '.', '') }}</td>
                        <td>{{ number_format($site_visit_total ?? 0.0, 2, '.', '') }}</td>
                        <td>{{ number_format($rewinding_charge_total ?? 0.0, 2, '.', '') }}</td>
                        <td>{{ number_format($local_charge_total ?? 0.0, 2, '.', '') }}</td>
                        <td>{{ number_format($grand_total ?? 0.0, 2, '.', '') }}</td>
                    </tr>
            @endif
        </tbody>
    </table>
    <h5 style="color: #7c7c7c; font-weight: bold !important; text-align: right; margin-left: 0;">
        Silver Consumer Electricals Limited
    </h5>
</body>

</html>
