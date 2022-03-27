<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendUploadLinkEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        private string $toName,
        private string $fromName,
        private string $url
    ) {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $head = 'ファイルをアップロードするためのリンクをお送りします';
        $msgText = "{$this->fromName}さんから、ファイルをアップロードするためのリンクが届いています。\n以下のリンクからファイルをアップロードしてください。\n";
        $subText = 'このメールがあなた宛でない場合、メールを破棄してください。';
        $btnText = 'ファイルをアップロードする';

        return $this->subject('ファイルをアップロードするためのリンクをお送りします')
            ->markdown(
                'mail.html.send-link-message',
                [
                    'head' => $head,
                    'toUser' => $this->toName,
                    'message' => $msgText,
                    'subcopy' => $subText,
                    'link' => $this->url,
                    'btnText' => $btnText
                ]
            );
    }
}
