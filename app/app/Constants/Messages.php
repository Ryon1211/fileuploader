<?php

namespace App\Constants;

class Messages
{
    const SUCCESS = [
        'fileDeleted' => 'ファイルを削除しました。',
        'linkDeleted' => 'リンクを削除しました。'
    ];

    const ERROR = [
        'linkExpired' => 'リンクがの有効期限が切れています。',
        'linkDisabled' => 'リンクが無効または存在しません。',
        'fileExpired' => 'ファイルの有効期限が切れています。',
        'fileNotFound' => 'ファイルが指定されていないか、存在していません。',
    ];
}
