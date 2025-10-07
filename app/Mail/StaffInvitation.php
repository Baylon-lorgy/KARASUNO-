<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\StaffUser;

class StaffInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $staffUser;

    /**
     * Create a new message instance.
     */
    public function __construct(StaffUser $staffUser)
    {
        $this->staffUser = $staffUser;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to RainBasin Pro - Staff Invitation',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.staff-invitation',
            with: [
                'staffUser' => $this->staffUser,
                'invitationUrl' => $this->staffUser->getInvitationUrl(),
                'role' => ucfirst($this->staffUser->role),
                'permissions' => $this->staffUser->getRolePermissions()
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
} 