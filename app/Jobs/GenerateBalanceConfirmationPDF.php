<?php

namespace App\Jobs;

use App\Models\Attachment;
use Dompdf\Dompdf;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use App\Models\BalanceConfirmation; // Your model to store PDF path

class GenerateBalanceConfirmationPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customer;
    protected $bal_date;
    protected $logoBase64;
    protected $footerLogoImage64;
    protected $logoBase642;

    public function __construct($customer, $bal_date, $logoBase64, $footerLogoImage64, $logoBase642)
    {
        $this->customer = $customer;
        $this->bal_date = $bal_date;
        $this->logoBase64 = $logoBase64;
        $this->footerLogoImage64 = $footerLogoImage64;
        $this->logoBase642 = $logoBase642;
    }

    public function handle()
    {
        $data = [
            'image' => $this->logoBase64,
            'image2' => $this->footerLogoImage64,
            'image3' => $this->logoBase642,
            'date' => $this->bal_date,
            'data' => $this->customer
        ];

        // Generate PDF
        $html = View::make('customers.BalanceConfirmationPDF', $data)->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();

        $pdf = $dompdf->output();
        $filename = 'balance_confirmation_' . $this->customer['id'] . '.pdf';
        $path = 'balance_confirmations/' . $filename;

        $filePath = fileupload($pdf, $path, $filename);

        $existingAttachment = Attachment::where('document_name', 'balance_confirmations')
            ->where('customer_id', $this->customer->customer->id)
            ->first();

        if ($existingAttachment) {
            $existingAttachment->update($filePath);
        } else {
            $new_data = [
                'active'        => 'Y',
                'file_path'     => $filePath,
                'document_name' =>  'balance_confirmations',

            ];
            Attachment::create(array_merge($new_data, ['customer_id' => $this->customer->customer->id]));
        }
    }
}
