<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendUserSeller extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    public $order;
    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from = rrt_get_config_by('core', 'mail', 'from');
        $brand = rrt_get_config_by('core', 'mail', 'brand');

        $data = $this->data;
        $subject = $data['name'] ?? "Subject Notice";
        $code = $data['code'] ?? "";
        return $this->view('mail.mail_seller_transaction_success')->from($from, $brand)->subject("[{$brand}] {$subject}: {$code} ")->with(['data' => $this->data]);
    }
}
