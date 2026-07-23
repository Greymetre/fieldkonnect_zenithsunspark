<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMailWithAttachment extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $attachmentPath;
    public $orderDetails;
    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($attachmentPath, $orderDetails, $order)
    {
        $this->attachmentPath = $attachmentPath;
        $this->orderDetails = $orderDetails;
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $sub = 'ORDER '.$this->order->getuserdetails->getdivision->division_name.' Order Number : '.$this->order->orderno.', Order Date : '.date('d-m-y', strtotime($this->order->order_date));
        
        return $this->view('emails.order-email-attech')
            ->with([
                'orderDetails' => $this->orderDetails,
                'order' => $this->order,
            ])
            ->attach($this->attachmentPath)
            ->subject($sub);
    }
}
