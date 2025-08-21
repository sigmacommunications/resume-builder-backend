<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\LoadPost;

class LoadPostNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $load;

    public function __construct(LoadPost $load)
    {
        $this->load = $load;
    }

    public function build()
    {
        return $this->subject('New Load Post Available')
                    ->markdown('emails.load_notification');
    }
}
