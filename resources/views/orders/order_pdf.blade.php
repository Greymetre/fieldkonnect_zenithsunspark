<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $documentNumber }} - {{ $documentTitle }}</title>
  <style>
    @page { margin: 28px 34px; }
    * { box-sizing: border-box; }
    body { margin: 0; color: #37394d; font-family: DejaVu Sans, sans-serif; font-size: 10px; }
    h1 { margin: 0 0 14px; text-align: center; font-size: 20px; }
    table { width: 100%; border-collapse: collapse; }
    .bordered td, .bordered th { border: 1px solid #55586b; padding: 6px; vertical-align: top; }
    .company-table td { height: 98px; }
    .company-logo { width: 34%; text-align: center; vertical-align: middle !important; }
    .company-logo img { max-width: 210px; max-height: 82px; }
    .company-name { margin: 0 0 5px; font-size: 18px; font-weight: bold; text-transform: uppercase; }
    .company-meta { line-height: 1.55; }
    .section-head { background: #f3f3f5; font-weight: bold; }
    .details td { width: 50%; line-height: 1.55; }
    .items { margin-top: 8px; }
    .items th { background: #f3f3f5; font-weight: bold; text-align: left; }
    .items .num { text-align: right; white-space: nowrap; }
    .items .center { text-align: center; }
    .totals-wrap td { border: 0; padding: 0; }
    .totals { width: 42%; margin-left: auto; }
    .totals td { border: 1px solid #55586b; padding: 6px; }
    .totals .grand td { font-weight: bold; }
    .amount-words { margin-top: 8px; }
    .signature { margin-top: 10px; }
    .signature td { height: 105px; }
    .signature-box { text-align: center; vertical-align: bottom !important; }
    .signature-box img { display: block; max-width: 150px; max-height: 62px; margin: 2px auto 8px; }
    .bank-details { line-height: 1.55; }
    .bank-details img { width: 92px; height: 92px; float: left; margin: 0 10px 4px 0; }
    .bank-details .upi-label { display: inline-block; margin-top: 4px; padding: 2px 6px; background: #27c979; color: #fff; font-size: 8px; }
    .muted { color: #66697a; }
  </style>
</head>
<body>
@php
  $companyAddress = optional($settings)->address;
  $partyAddress = optional($party)->customeraddress;
  $companyLogo = $settings ? $settings->getFirstMedia('invoice_logo') : null;
  $companySign = $settings ? $settings->getFirstMedia('invoice_esign') : null;
  $logoPath = $companyLogo && is_file($companyLogo->getPath()) ? $companyLogo->getPath() : null;
  $signPath = $companySign && is_file($companySign->getPath()) ? $companySign->getPath() : null;
  $bankQrPath = public_path('assets/img/qr_code.png');
  $partyGstin = optional(optional($party)->customerdetails)->gstin_no;
  $isPurchaseOrder = $order instanceof \App\Models\PurchaseOrder;
@endphp

<h1>{{ $documentTitle }}</h1>

<table class="bordered company-table">
  <tr>
    <td class="company-logo">
      @if($logoPath)
        <img src="{{ $logoPath }}" alt="Company Logo">
      @endif
    </td>
    <td>
      <div class="company-name">{{ optional($settings)->company_name ?: config('app.name') }}</div>
      <div class="company-meta">
        @if($companyAddress && $companyAddress->full_address)
          {{ $companyAddress->full_address }}<br>
        @endif
        @if(optional($settings)->gst_number)<strong>GSTIN:</strong> {{ $settings->gst_number }}@endif
        @if(optional($settings)->pan_number)&nbsp;&nbsp; <strong>PAN:</strong> {{ $settings->pan_number }}@endif
      </div>
    </td>
  </tr>
</table>

<table class="bordered details">
  <tr>
    <td class="section-head">{{ $documentTitle }} For:</td>
    <td class="section-head">{{ $documentTitle }} Details:</td>
  </tr>
  <tr>
    <td>
      <strong>{{ optional($party)->name ?: '-' }}</strong><br>
      @if($partyAddress && $partyAddress->full_address){{ $partyAddress->full_address }}<br>@endif
      @if(optional($party)->mobile)<strong>Contact:</strong> {{ $party->mobile }}<br>@endif
      @if(optional($party)->email)<strong>Email:</strong> {{ $party->email }}<br>@endif
      @if($partyGstin)<strong>GSTIN:</strong> {{ $partyGstin }}@endif
    </td>
    <td>
      <strong>No:</strong> {{ $documentNumber ?: '-' }}<br>
      <strong>Date:</strong> {{ optional($documentDate)->format('d-m-Y') ?: '-' }}<br>
      <strong>Status:</strong> {{ ucwords(str_replace('_', ' ', $order->status)) }}<br>
      @if($warehouse)<strong>Warehouse:</strong> {{ $warehouse->warehouse_name }}<br>@endif
      @if(!$isPurchaseOrder)<strong>Order Type:</strong> {{ strtoupper($order->order_type) }}@endif
    </td>
  </tr>
</table>

<table class="bordered items">
  <thead>
    <tr>
      <th style="width:4%">#</th>
      <th>Item Name</th>
      <th style="width:13%">Product Code</th>
      @if($isPurchaseOrder)<th style="width:14%">Warehouse</th>@endif
      <th style="width:9%" class="num">Quantity</th>
      <th style="width:8%" class="center">Unit</th>
      <th style="width:12%" class="num">Rate</th>
      <th style="width:8%" class="num">GST%</th>
      <th style="width:13%" class="num">Amount</th>
    </tr>
  </thead>
  <tbody>
    @foreach($order->items as $index => $item)
    <tr>
      <td>{{ $index + 1 }}</td>
      <td><strong>{{ optional($item->product)->product_name ?: '-' }}</strong></td>
      <td>{{ optional($item->product)->product_code ?: '-' }}</td>
      @if($isPurchaseOrder)<td>{{ optional($item->warehouse)->warehouse_name ?: '-' }}</td>@endif
      <td class="num">{{ rtrim(rtrim(number_format($item->quantity, 3, '.', ''), '0'), '.') }}</td>
      <td class="center">{{ optional(optional($item->product)->unitmeasures)->unit_name ?: '-' }}</td>
      <td class="num">Rs. {{ number_format($item->rate, 2) }}</td>
      <td class="num">{{ number_format($item->gst_percent, 2) }}</td>
      <td class="num">Rs. {{ number_format($item->total_amount, 2) }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<table class="totals-wrap">
  <tr><td>
    <table class="totals">
      <tr><td>Sub Total</td><td class="num">Rs. {{ number_format($order->subtotal, 2) }}</td></tr>
      <tr><td>Total GST</td><td class="num">Rs. {{ number_format($order->total_gst, 2) }}</td></tr>
      <tr class="grand"><td>Total</td><td class="num">Rs. {{ number_format($order->grand_total, 2) }}</td></tr>
    </table>
  </td></tr>
</table>

<table class="bordered amount-words">
  <tr><td class="section-head">{{ $documentTitle }} Amount In Words:</td></tr>
  <tr><td>{{ numberToWords($order->grand_total) }}</td></tr>
</table>

<table class="bordered signature">
  <tr>
    <td style="width:58%" class="{{ !$isPurchaseOrder ? 'bank-details' : '' }}">
      @if(!$isPurchaseOrder)
        <strong>Bank Details:</strong><br>
        @if(is_file($bankQrPath))
          <img src="{{ $bankQrPath }}" alt="Payment QR Code">
        @endif
        Name: <strong>Hdfc Bank, Gidc,anklesvar</strong><br>
        Account No.: <strong>50200107439744</strong><br>
        IFSC code: <strong>HDFC0002677</strong><br>
        Account Holder's Name: <strong>ZENITH Sunspark</strong><br>
        <span class="upi-label">UPI | CLICK TO PAY</span>
      @else
        <strong>Notes:</strong><br>
        <span class="muted">{{ $order->notes ?: 'Thank you for doing business with us.' }}</span>
      @endif
    </td>
    <td class="signature-box">
      <strong>For {{ optional($settings)->company_name ?: config('app.name') }}</strong>
      @if($signPath)<img src="{{ $signPath }}" alt="Authorized Signature">@endif
      <div>Authorized Signatory</div>
    </td>
  </tr>
</table>
</body>
</html>
