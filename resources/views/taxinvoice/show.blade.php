<x-app-layout>
  <style>
    .page {
      width: 100%;
      max-width: 900px;
      margin: 0 auto;
      background: white;
      box-shadow: 0 6px 18px rgba(20, 30, 40, 0.08);
      padding: 20px 28px;
      box-sizing: border-box;
      border-radius: 6px;
    }

    .qrcode {
      display: flex;
      align-items: center;
    }

    header {
      display: flex;
      justify-content: space-between;
      gap: 18px;
      align-items: center;
      margin-bottom: 18px;
    }

    .company {
      font-weight: 700;
      font-size: 18px;
      color: #111;
    }

    .company small {
      display: block;
      font-weight: 400;
      color: #000;
      margin-top: 0px;
      font-size: 12px;
    }

    .meta {
      text-align: right;
      font-size: 14px;
    }

    .meta .label {
      color: var(--muted);
      display: block;
      font-size: 12px;
    }

    .meta .value {
      font-weight: 700;
      font-size: 15px;
    }

    .addresses {
      display: flex;
      border: 1px solid #00000057;
    }

    .addr {
      flex: 1;
      border: 1px solid var(--border);
      padding: 12px;
      border-radius: 0px;
      min-width: 0;
    }

    .addr h4 {
      margin: 0 0 6px 0;
      font-size: 13px;
    }

    .addr p {
      margin: 0;
      font-size: 13px;
      color: var(--muted);
      line-height: 1.4;
    }

    table.items {
      width: 100%;
      border-collapse: collapse;
      /*margin-top: 12px;*/
    }

    table.items thead th {
      text-align: left;
      font-weight: 700;
      font-size: 12px;
      padding: 1px 8px;
      border-bottom: 2px solid var(--border);
    }

    table.items tbody td {
      padding: 3px 8px;
      vertical-align: top;
      font-size: 13px;
      border-bottom: 1px dashed #eee;
    }

    .right {
      text-align: right;
    }

    .center {
      text-align: center;
    }

    .totals {
      margin-top: 12px;
      display: flex;
      justify-content: flex-end;
    }

    .totals table {
      width: 360px;
      border-collapse: collapse;
    }

    .totals td {
      padding: 8px 10px;
      font-size: 13px;
    }

    .totals tr.total-row td {
      font-weight: 700;
      font-size: 15px;
      border-top: 2px solid var(--border);
    }

    .notes {
      margin-top: 20px;
      display: flex;
      gap: 16px;
      align-items: flex-start;
    }

    .notes .left,
    .notes .right {
      flex: 1;
      font-size: 13px;
      color: var(--muted);
    }

    .signature {
      margin-top: 30px;
      text-align: right;
      font-size: 13px;
    }

    .bank {
      margin-top: 12px;
      font-size: 13px;
      color: var(--muted);
    }

    .qr {
      width: 110px;
      height: 110px;
      border: 1px dashed #ddd;
      display: inline-block;
      text-align: center;
      line-height: 110px;
      color: #999;
      font-size: 12px;
      border-radius: 6px;
    }

    /* Controls */
    .controls {
      max-width: 900px;
      margin: 18px auto;
      display: flex;
      gap: 10px;
      justify-content: flex-end;
    }

    .btn {
      background: var(--accent);
      border: none;
      color: white;
      padding: 10px 16px;
      font-size: 14px;
      border-radius: 6px;
      cursor: pointer;
    }

    .btn.secondary {
      background: #fff;
      color: var(--accent);
      border: 1px solid var(--accent);
    }

    .logo img {
      width: 200px;
      height: auto;
    }

    table.items {
      border: 1px solid #00000057;
      border-bottom: 0px !important;
    }

    table.items thead th {
      border: 1px solid #00000057;
      background: #eee;
      color: #000;
      font-weight: 600 !important;
    }

    table.items tbody td {
      border: 1px solid #00000057;
    }


    /* Print-friendly / A4 sizing when generating PDF */
    @media print {
      body {
        background: white;
        padding: 0;
      }

      .page {
        box-shadow: none;
        margin: 0;
        border-radius: 0;
        max-width: none;
      }

      .controls {
        display: none;
      }
    }
  </style>
  <section class="invocie_main">
    <div class="container-fluid">
      <div class="controls">
        <button id="downloadPdf" class="btn" title="Download PDF"> <i class="material-icons">picture_as_pdf</i></button>
        <button id="printBtn" class="btn secondary text-white" title="Print"> <i class="material-icons">print</i></button>
      </div>

      <div id="invoice" class="page">
        <header>
          <div class="logo">
            @if($settings && $settings->hasMedia('invoice_logo'))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($settings->getFirstMediaUrl('invoice_logo'))) }}" alt="Invoice Logo" style="max-height:80px;">
            @else
            <img src="{{ asset('assets/img/g_logo.png') }}" alt="Default Logo" style="max-height:80px;">
            @endif

          </div>
          <div>
            <div class="company">{{$settings->company_name ?? ''}}</div>
            <small style="display:block;margin-top:0px;color:#000;font-size:12px;font-weight: 400;line-height: 18px;">
              {{$settings->address ? $settings->address->address1 : ''}}<br />
              {{$settings->address ? $settings->address->districtname?->district_name : ''}} {{$settings->address ? $settings->address->statename?->state_name : ''}} {{$settings->address ? $settings->address->pincodename?->pincode : ''}}, {{$settings->address ? $settings->address->countryname?->country_name : ''}}<br />
              GSTIN: {{$settings->gst_number ?? ''}}
            </small>
          </div>

          <div class="meta">

            <h2 style="text-transform: uppercase;font-size: 20px;color: #000; font-weight: 600;">Tax Invoice</h2>
          </div>
        </header>

        <div class="addresses" style="border-bottom:0px!important;">
          <div class="addr" style="border-right: 1px solid #00000057;">
            <table style="width:100%;">
              <tr>
                <td style="color:#000; font-weight: 400; width: 60%;font-size: 12px; line-height: 16px;">#</td>
                <td style="color:#000; font-weight: 500; width: 40%; font-size: 12px; line-height: 16px;"> : {{$tax_invoice->invoice_no}}</td>
              </tr>
              <tr>
                <td style="color:#000; font-weight: 400;width: 60%;font-size: 12px; line-height: 16px;">Invoice Date</td>
                <td style="color:#000; font-weight: 500;width: 40%;font-size: 12px; line-height: 16px;"> : {{date('d M Y', strtotime($tax_invoice->invoice_date))}}</td>
              </tr>
              <tr>
                <td style="color:#000; font-weight: 400;width: 60%;font-size: 12px; line-height: 16px;">Terms</td>
                <td style="color:#000; font-weight: 500;width: 40%;font-size: 12px; line-height: 16px;"> : {{$tax_invoice->term?->term_name}}</td>
              </tr>
              <tr>
                <td style="color:#000; font-weight: 400;width: 60%;font-size: 12px; line-height: 16px;">Due Date</td>
                <td style="color:#000; font-weight: 500;width: 40%;font-size: 12px; line-height: 16px;"> : {{date('d M Y', strtotime($tax_invoice->due_date))}}</td>
              </tr>
            </table>
          </div>

          <div class="addr">
            <table style="width:100%;">
              <tr>
                <td style="color:#000; font-weight: 400;width: 60%;font-size: 12px; line-height: 16px;">Place Of Supply</td>
                <td style="color:#000; font-weight:500; width:40%; font-size:12px; line-height:16px;">
                  : {{ $tax_invoice->state ? '['.$tax_invoice->state->gst_code . ']' . $tax_invoice->state->state_name : '' }}
                </td>

              </tr>
            </table>
          </div>
        </div>
        <div class="addresses" style="border-bottom:0px!important;">
          <div class="addr" style="border-right: 1px solid #00000057; padding: 0px;">
            <table style="width:100%;">
              <thead>
                <tr>
                  <th style="background:#eee;padding: 5px 5px;color: #000;font-weight: 600 !important;">Bill To</th>
                </tr>
              </thead>
              <tr>
                <td style="color:#000; font-weight: 500;font-size: 13px; padding: 0px 5px; line-height: 20px; padding-top:5px;">
                  {{ $tax_invoice->customer?->name ?? '' }}
                </td>
              </tr>
              <tr>
                <td style="color:#000; font-weight: 400;font-size: 12px; padding: 0px 5px; line-height: 20px;">
                  {{ $address['billing_address']['address1'] ?? '' }}
                  {{ $address['billing_address']['cityname']['city_name'] ?? '' }}
                </td>
              </tr>
              <tr>
                <td style="color:#000; font-weight: 400;font-size: 12px; padding: 0px 5px; line-height: 20px;">
                  {{ $address['billing_address']['districtname']['district_name'] ?? '' }}
                </td>
              </tr>
              <tr>
                <td style="color:#000; font-weight: 400;font-size: 12px; padding: 0px 5px; line-height: 20px;">
                  {{ $address['billing_address']['pincodename']['pincode'] ?? '' }}
                  {{ $address['billing_address']['statename']['state_name'] ?? '' }}
                </td>
              </tr>
              <tr>
                <td style="color:#000; font-weight: 400;font-size: 12px; padding: 0px 5px; line-height: 20px;">
                  {{ $address['billing_address']['countryname']['country_name'] ?? '' }}
                </td>
              </tr>
              <tr>
                <td style="color:#000; font-weight: 400;font-size: 12px; padding: 0px 5px; line-height: 20px;">
                  GSTIN : {{ $address['gstin_no'] ?? '' }}
                </td>
              </tr>
            </table>

          </div>
          @if(($address['same_address'] ?? 1) == 0)
          <div class="addr" style="padding:0px;">
            <table style="width:100%;">
              <thead>
                <tr>
                  <th style="background:#eee;padding: 5px 5px;color: #000;font-weight: 600 !important;">Ship To</th>
                </tr>
              </thead>
              <tr>
                <td style="color:#000; font-weight: 400;font-size: 12px; padding: 0px 5px; line-height: 20px;">
                  {{ $address['shipping_address']['address1'] ?? '' }}
                  {{ $address['shipping_address']['cityname']['city_name'] ?? '' }}
                </td>
              </tr>
              <tr>
                <td style="color:#000; font-weight: 400;font-size: 12px; padding: 0px 5px; line-height: 20px;">
                  {{ $address['shipping_address']['districtname']['district_name'] ?? '' }}
                </td>
              </tr>
              <tr>
                <td style="color:#000; font-weight: 400;font-size: 12px; padding: 0px 5px; line-height: 20px;">
                  {{ $address['shipping_address']['pincodename']['pincode'] ?? '' }}
                  {{ $address['shipping_address']['statename']['state_name'] ?? '' }}
                </td>
              </tr>
              <tr>
                <td style="color:#000; font-weight: 400;font-size: 12px; padding: 0px 5px; line-height: 20px;">
                  {{ $address['shipping_address']['countryname']['country_name'] ?? '' }}
                </td>
              </tr>
            </table>
          </div>
          @else
          <div class="addr" style="padding:0px;">
            <table style="width:100%;">
              <thead>
                <tr>
                  <th style="background:#eee;padding: 5px 5px;color: #000;font-weight: 600 !important;">Ship To</th>
                </tr>
              </thead>
              <tr>
                <td style="color:#000; font-weight: 400;font-size: 12px; padding: 0px 5px; line-height: 20px;">
                  {{ $address['billing_address']['address1'] ?? '' }}
                  {{ $address['billing_address']['cityname']['city_name'] ?? '' }}
                </td>
              </tr>
              <tr>
                <td style="color:#000; font-weight: 400;font-size: 12px; padding: 0px 5px; line-height: 20px;">
                  {{ $address['billing_address']['districtname']['district_name'] ?? '' }}
                </td>
              </tr>
              <tr>
                <td style="color:#000; font-weight: 400;font-size: 12px; padding: 0px 5px; line-height: 20px;">
                  {{ $address['billing_address']['pincodename']['pincode'] ?? '' }}
                  {{ $address['billing_address']['statename']['state_name'] ?? '' }}
                </td>
              </tr>
              <tr>
                <td style="color:#000; font-weight: 400;font-size: 12px; padding: 0px 5px; line-height: 20px;">
                  {{ $address['billing_address']['countryname']['country_name'] ?? '' }}
                </td>
              </tr>
            </table>
          </div>
          @endif
        </div>
        <table class="items" aria-labelledby="items">
          <thead>
            <tr>
              <th rowspan="2" style="width:45%; color: #000; ">Item & Description</th>
              <th rowspan="2" style="width:12%; color: #000; ">HSN/SAC</th>
              <th rowspan="2" style="width:8%; color: #000; " class="center">Qty</th>
              <th rowspan="2" style="width:8%; color: #000;" class="center">Rate</th>
              @if($tax_invoice->place_of_supply == 1)
              <th colspan="2" style="width:10%; color: #000; text-align: center;" class="center">CGST</th>
              <th colspan="2" style="width:10%; color: #000; text-align: center;" class="center">SGST</th>
              @else
              <th colspan="2" style="width:10%; color: #000; text-align: center;" class="center">IGST</th>
              @endif

              <th rowspan="2" style="width:13%; color: #000;" class="right">Amount</th>
            </tr>
            <tr>
              @if($tax_invoice->place_of_supply == 1)
              <th style="width:5%; color: #000;" class="center">%</th>
              <th style="width:5%; color: #000;" class="center">Amt</th>
              <th style="width:5%; color: #000;" class="center">%</th>
              <th style="width:5%; color: #000;" class="center">Amt</th>
              @else
              <th style="width:10%; color: #000;" class="center">%</th>
              <th style="width:10%; color: #000;" class="center">Amt</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @foreach($tax_invoice->details as $item)
            <tr>
              <td style="color:#000; font-weight: 400;">
                <strong>{{$item->product?->description}}</strong>
                <div style="color:#000;font-size:12px;margin-top:0px; line-height: 15px;">{{$item->product_dec}}</div>
              </td>
              <td style="color:#000;font-weight: 400;">{{$item->hsn_sac}}</td>
              <td style="color:#000;font-weight: 400;" class="center">{{$item->quantity}}</td>
              <td style="color:#000;font-weight: 400;" class="right">{{$item->mrp}}</td>

              @if($tax_invoice->place_of_supply == ($settings->address ? $settings->address->state_id : 1))
              @php
              $halfRate = $item->tax_details?$item->tax_details->tax_percentage:0 / 2;
              $halfAmount = $item->tax_amount / 2;
              @endphp
              <td style="color:#000;font-weight:400;" class="center">
                CGST {{ $halfRate }}%
              </td>
              <td style="color:#000;font-weight:400;" class="center">
                {{ number_format($halfAmount, 2) }}
              </td>

              <td style="color:#000;font-weight:400;" class="center">
                SGST {{ $halfRate }}%
              </td>
              <td style="color:#000;font-weight:400;" class="center">
                {{ number_format($halfAmount, 2) }}
              </td>
              @else
              <td style="color:#000;font-weight:400;" class="center">
                {{ $item->tax_details?->tax_name }} {{ $item->tax_details?->tax_percentage }}%
              </td>
              <td style="color:#000;font-weight:400;" class="center">
                {{ number_format($item->tax_amount, 2) }}
              </td>
              @endif

              <td style="color:#000;font-weight: 400;" class="right">{{$item->amount}}</td>
            </tr>
            @endforeach
            <tr>
              <td colspan="3" style="border:0px!important;">
                <p style="margin-bottom:0px; color:#000; font-weight: 400;">Total In Words</p>
                <p style="font-weight: bold; color:#000;line-height: 10px;"> {{ numberToWords($tax_invoice->grand_total) }} </p>
              </td>
              @if($tax_invoice->place_of_supply == 1)
              <td colspan="6">
                @else
              <td colspan="4">
                @endif
                <table style="width: 100%; border:0px;" border="0">
                  <tr>
                    <td style="border:0px; padding: 0px 8px; text-align:right;color: #000;font-weight: 400;line-height: 20px;">Sub Total</td>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400;line-height: 20px;">{{$tax_invoice->sub_total}}</td>
                  </tr>
                  @if($tax_invoice->discount != 0)
                  <tr>
                    <td style="border:0px; padding: 0px 8px; text-align:right;color: #000;font-weight: 400;line-height: 20px;">Discount</td>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400;line-height: 20px;">{{$tax_invoice->discount}} {{$tax_invoice->discount_type == 'percentage' ? '%' : ''}}</td>
                  </tr>
                  @endif
                  @if($taxSummary && count($taxSummary) > 0)
                  @foreach($taxSummary as $tax)
                  <tr>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400;line-height: 20px;">{{$tax['tax_name']}}</td>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400;line-height: 20px;">{{$tax['total_tax_amount']}}</td>
                  </tr>
                  @endforeach
                  @endif
                  @if($tax_invoice->tds > 0)
                  <tr>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400; line-height: 20px;">Amount Withheld</td>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400;line-height: 20px;color:red;">{{number_format($tax_invoice->tds_amount, 2)}}</td>
                  </tr>
                  @endif
                  @if($tax_invoice->adjustment != 0)
                  <tr>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400; line-height: 20px;">Adjustment</td>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400;line-height: 20px;">{{number_format($tax_invoice->adjustment, 2)}}</td>
                  </tr>
                  @endif
                  <tr>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400; line-height: 20px; font-weight: bold;">Total</td>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400; line-height: 20px;font-weight: bold;">₹{{number_format($tax_invoice->grand_total, 2)}}</td>
                  </tr>
                  <tr>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400; line-height: 20px; font-weight: bold;">Balance Due</td>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400; line-height: 20px; font-weight: bold;">₹0.00</td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr></tr>
            <tr>
              <td rowspan="2" colspan="3" style="border:0px!important;">

                <p style="color:#000; font-weight: 400;">Note</p>
                <p style="color:#000; font-weight: 400; margin-bottom: 0px; line-height: 16px;">{!! nl2br(e($tax_invoice->customer_notes)) !!}</p><br>
              </td>
              @if($tax_invoice->place_of_supply == 1)
              <td colspan="6" style="text-align: center; vertical-align: middle;">
                @else
              <td colspan="4" style="text-align: center; vertical-align: middle;">
                @endif
                @if($settings && $settings->hasMedia('invoice_esign'))
                {{-- Dynamic E-Sign from media, converted to Base64 --}}
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($settings->getFirstMediaUrl('invoice_esign'))) }}"
                  alt="E-Sign" style="max-height:80px;">
                @else
                {{-- Default E-Sign from assets, converted to Base64 --}}
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/sill.png'))) }}"
                  alt="Default E-Sign" style="max-height:80px;">
                @endif
                <p style="color:#000; font-size:12px; font-weight: 400; margin-bottom: 0px;">Authorized Signature</p>
              </td>
            </tr>
            <tr></tr>
            <tr>
              <td rowspan="2" colspan="3" style="border:0px!important;">
                <p style="color:#000; font-weight: 400; font-size: 12px;">Thanks for your business.</p>
                <!-- <div class="qrcode">
                  <img src="https://demo.fieldkonnect.io/public/assets/img/qr_code.png" style="width:100px;height: 100px;object-fit:cover;">
                  <p style="margin-left: 10px; color:#000; font-weight: 400; font-size:12px;"> Scan the QR code to view the configured information.</p>
                </div> -->
              </td>
            </tr>
            <!-- <tr>
              <td colspan="3">
                <p style="margin-bottom:0px; color:#000;">Total In Words</p>
                <p style="font-weight: bold; color:#000;">Indian Rupee Twenty-Nine Thousand Five Hundred Only</p>

                <p style="color:#000; font-weight: 400;">Note</p>
                <p style="color:#000; font-weight: 400; margin-bottom: 0px; line-height: 16px;">Bank Details :-<br>
                  Bank Name - Asix Bank Ltd<br>
                  Account No -920020063736921<br>
                  IFSC Code -UTIB0000456<br>
                  Branch -Dewas MP</p><br>
                      
                  <p style="color:#000; font-weight: 400; font-size: 12px;">Thanks for your business.</p>
                   <div class="qrcode">
                  <img src="https://demo.fieldkonnect.io/public/assets/img/qr_code.png" style="width:100px;height: 100px;object-fit:cover;">
                 <p style="margin-left: 10px; color:#000; font-weight: 400; font-size:12px;"> Scan the QR code to view the configured information.</p>
                </div>
              </td>
              <td colspan="4">

                <table style="width: 100%; border:0px;" border="0">
                  <tr>
                    <td style="border:0px; padding: 0px 8px; text-align:right;color: #000;font-weight: 400;">Sub Total</td>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400;">25,000.00</td>
                  </tr>
                  <tr>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400;">IGST18 (18%)</td>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400;">4,500.00</td>
                  </tr>
                  <tr>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400; font-weight: bold;">Total</td>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400; font-weight: bold;">₹29,500.00</td>
                  </tr>
                  <tr>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400;">Payment Made</td>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400; color:red;">(-) 27,625.00</td>
                  </tr>
                  <tr>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400;">Amount Withheld</td>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400;color:red;">(-) 1,875.00</td>
                  </tr>
                  <tr>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400; font-weight: bold;">Balance Due</td>
                    <td style="border:0px;padding: 0px 8px; text-align:right;color: #000;font-weight: 400; font-weight: bold;">₹0.00</td>
                  </tr>
                  <tr>
                    <td  colspan="2" style="height: 50px; border:0px;">  
                    </td>
                   
                  </tr>
                  <tr>
                    <td  colspan="2" style="border:0px;padding: 0px 8px; text-align:center;color: #000;font-weight: 400; font-weight: bold;">
                        <img src="https://demo.fieldkonnect.io/public/assets/img/sill.png">
                       <p style="color:#000; font-size:12px; font-weight: 400;"> Authorized Signature</p>
                    </td>
                   
                  </tr>
                </table>
              </td>
            </tr> -->
          </tbody>
        </table>
        <table class="items" aria-labelledby="items">
          <thead>
            <tr>
              <th rowspan="2" style="width:45%; color: #000;">HSN/SAC</th>
              <th rowspan="2" style="width:12%; color: #000;"> Taxable Amount</th>
              <!-- <th style="width:8%; color: #000; text-align: center;" class="center" colspan="2">IGST</th> -->
              @if($tax_invoice->place_of_supply == ($settings->address ? $settings->address?->state_id : 1))
              <th colspan="2" style="width:8%; color: #000; text-align: center;" class="center">CGST</th>
              <th colspan="2" style="width:8%; color: #000; text-align: center;" class="center">SGST</th>
              @else
              <th colspan="2" style="width:8%; color: #000; text-align: center;" class="center">IGST</th>
              @endif
              <th style="width:13%; color: #000;" class="right" rowspan="2">Total Tax Amount </th>
            </tr>
            <tr>
              @if($tax_invoice->place_of_supply == ($settings->address ? $settings->address?->state_id : 1))
              <th style="width:8%; color: #000; text-align: center;" class="center">Rate</th>
              <th style="width:12%; color: #000; text-align: center;" class="right">Anount</th>
              <th style="width:8%; color: #000; text-align: center;" class="center">Rate</th>
              <th style="width:12%; color: #000; text-align: center;" class="right">Anount</th>
              @else
              <th style="width:8%; color: #000; text-align: center;" class="center">Rate</th>
              <th style="width:12%; color: #000; text-align: center;" class="right">Anount</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @if($hsnSacSummary && $hsnSacSummary->count() > 0)
            @foreach($hsnSacSummary as $hsnSac)
            <tr>
              <td style="color:#000; font-weight: 400;">{{ $hsnSac['hsn_sac'] }}</td>
              <td style="color:#000;font-weight: 400; text-align: right;">{{ $hsnSac['total_amount'] }}</td>
              @if($tax_invoice->place_of_supply == ($settings->address ? $settings->address?->state_id : 1))
              <td style="color:#000;font-weight: 400; text-align: center;" class="center">{{ $hsnSac['tax_name']/2 }}%</td>
              <td style="color:#000;font-weight: 400; text-align: right;" class="right">{{ round(($hsnSac['total_tax_amount']/2), 2) }}</td>
              <td style="color:#000;font-weight: 400; text-align: center;" class="center">{{ $hsnSac['tax_name']/2 }}%</td>
              <td style="color:#000;font-weight: 400; text-align: right;" class="right">{{ round(($hsnSac['total_tax_amount']/2), 2) }}</td>
              @else
              <td style="color:#000;font-weight: 400; text-align: center;" class="center">{{ $hsnSac['tax_name'] }}</td>
              <td style="color:#000;font-weight: 400; text-align: right;" class="right">{{ $hsnSac['total_tax_amount'] }}</td>
              @endif
              <td style="color:#000;font-weight: 400; text-align: right;" class="right">{{ $hsnSac['total_tax_amount'] }}</td>
            </tr>
            @endforeach
            @endif
            <tr>
              <td style="color:#000; font-weight: 400;">
                Total
              </td>
              <td style="color:#000;font-weight: 700; text-align: right;">{{$hsnSacSummary ? round($hsnSacSummary->sum('total_amount'), 2) : '0.00'}}</td>
              @if($tax_invoice->place_of_supply == ($settings->address ? $settings->address?->state_id : 1))
              <td colspan="2" style="color:#000;font-weight: 700; text-align: right;" class="right">{{$hsnSacSummary ? round(($hsnSacSummary->sum('total_tax_amount')/2), 2) : '0.00'}}</td>
              <td colspan="2" style="color:#000;font-weight: 700; text-align: right;" class="right">{{$hsnSacSummary ? round(($hsnSacSummary->sum('total_tax_amount')/2), 2) : '0.00'}}</td>
              @else
              <td colspan="2" style="color:#000;font-weight: 700; text-align: right;" class="right">{{$hsnSacSummary ? round($hsnSacSummary->sum('total_tax_amount'), 2) : '0.00'}}</td>
              @endif
              <td style="color:#000;font-weight: 700; text-align: right;" class="center">{{$hsnSacSummary ? round($hsnSacSummary->sum('total_tax_amount'), 2) : '0.00'}}</td>
            </tr>
          </tbody>
        </table>

      </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
      // Download as PDF using html2pdf
      const btn = document.getElementById('downloadPdf');
      const printBtn = document.getElementById('printBtn');

      btn.addEventListener('click', () => {
        const element = document.getElementById('invoice');

        const opt = {
          margin: [8, 8, 8, 8], // top, left, bottom, right (mm treated by html2pdf)
          filename: 'Invoice_INV-000002.pdf',
          image: {
            type: 'jpeg',
            quality: 0.98
          },
          html2canvas: {
            scale: 2,
            useCORS: true,
            logging: false
          },
          jsPDF: {
            unit: 'mm',
            format: 'a4',
            orientation: 'portrait'
          }
        };

        // generate the pdf
        html2pdf().set(opt).from(element).save();
      });

      printBtn.addEventListener('click', () => {
        window.print();
      });
    </script>
  </section>
</x-app-layout>