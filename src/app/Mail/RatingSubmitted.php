<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class RatingSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $rater;
    public $ratee;
    public $rating;

    public function __construct(User $rater, User $ratee, int $rating)
    {
        $this->rater = $rater;
        $this->ratee = $ratee;
        $this->rating = $rating;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('あなたの商品に評価が届きました')
            ->view('emails.rating_submitted');
    }
}
