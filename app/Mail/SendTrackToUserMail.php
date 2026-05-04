<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class SendTrackToUserMail extends Mailable
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
        $from = rrt_get_config_by('core','mail','from');
        $brand = rrt_get_config_by('core', 'mail', 'brand');
        $subject = rrt_get_config_by('core', 'mail', 'subject_send_track_to_user');
        $data = $this->data;
        $code = $data['itemCode'] ?? "";
        return $this->view('mail.mail_send_track')->from($from, $brand)->subject("[{$brand}] {$subject}: Order #{$code}")->with(['data' => $this->data]);
    }
}
