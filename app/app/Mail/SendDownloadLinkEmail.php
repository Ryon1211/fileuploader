<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendDownloadLinkEmail extends Mailable
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
        private string $userMessage,
        private string $url
    ) {
        $this->afterCommit();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $head = 'ファイルをダウンロードするためのリンクをお送りします';
        $msgText = "{$this->fromName}さんから、ファイルをダウンロードするためのリンクが届いています。\n以下のリンクからファイルをダウンロードできます。\n";
        $subText = 'このメールがあなた宛でない場合、メールを破棄してください。';
        $btnText = 'ダウンロードページを見る';

        return $this->subject('ファイルをダウンロードするためのリンクをお送りします')
            ->markdown(
                'mail.html.send-link-message',
                [
                    'head' => $head,
                    'toUser' => $this->toName,
                    'fromName' => $this->fromName,
                    'message' => $msgText,
                    'userMessage' => $this->userMessage,
                    'subcopy' => $subText,
                    'link' => $this->url,
                    'btnText' => $btnText
                ]
            );
    }
}
