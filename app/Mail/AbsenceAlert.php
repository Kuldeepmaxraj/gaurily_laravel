<?php

namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbsenceAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Employee $employee,
        public string $date,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Attendance Alert – Absence Recorded on ' . $this->date,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.absence-alert',
        );
    }
}
