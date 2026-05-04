<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendNoticeMail extends Mailable
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
        $fromEmail = rrt_get_config_by('core', 'mail', 'from');
        $brandName = rrt_get_config_by('core', 'mail', 'brand');

        $data = $this->data;

        $subject = $data['name'] ?? 'Subject Notice';
        $validateCode = $data['validate_code'] ?? '';

        $emailSubject = "[{$brandName}] {$subject}: {$validateCode}";

        return $this->view('mail.mail_notice')
            ->from($fromEmail, $brandName)
            ->subject($emailSubject)
            ->with(['data' => $data]);
    }
}
