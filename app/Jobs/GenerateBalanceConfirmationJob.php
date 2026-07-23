<?php

namespace App\Jobs;

use App\Models\Attachment;
use Dompdf\Dompdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateBalanceConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $batch;
    public $logoBase64;
    public $footerLogoImage64;
    public $logoBase642;
    public $bal_date;

    public function __construct($batch, $logoBase64, $footerLogoImage64, $logoBase642, $bal_date)
    {
        $this->batch = $batch;
        $this->logoBase64 = $logoBase64;
        $this->footerLogoImage64 = $footerLogoImage64;
        $this->logoBase642 = $logoBase642;
        $this->bal_date = $bal_date;
    }

    public function handle()
    {
        foreach ($this->batch as $value) {
            $main_data = [
                'image' => $this->logoBase64,
                'image2' => $this->footerLogoImage64,
                'image3' => $this->logoBase642,
                'date' => $this->bal_date,
                'data' => $value
            ];

            $html = view('customers.BalanceConfirmationPDF', $main_data)->render();
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();

            $filename = 'balance_confirmation_' . $value->customer->id . '.pdf';
            $tempPath = storage_path('app/temp/' . $filename);
            file_put_contents($tempPath, $dompdf->output());

            $s3Path = 'uploads/balance_confirmations/' . $filename;
            $uploaded = Storage::disk('s3')->put($s3Path, fopen($tempPath, 'r+'));
            unlink($tempPath);

            if ($uploaded) {
                $filePath = Storage::disk('s3')->url($s3Path);
                Attachment::updateOrCreate(
                    ['document_name' => 'balance_confirmations', 'customer_id' => $value->customer->id],
                    ['file_path' => $filePath, 'active' => 'Y']
                );
            }
        }
    }
}
