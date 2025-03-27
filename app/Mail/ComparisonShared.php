<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComparisonShared extends Mailable
{
    use Queueable, SerializesModels;

    public $comparison;
    public $shareUrl;
    public $message;

    public function __construct($comparison, $shareUrl, $message)
    {
        $this->comparison = $comparison;
        $this->shareUrl = $shareUrl;
        $this->message = $message;
    }

    public function build()
    {
        return $this->markdown('emails.comparison-shared')
                    ->subject('Property Comparison Shared With You');
    }
} 