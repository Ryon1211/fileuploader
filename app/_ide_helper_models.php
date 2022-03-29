<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Admin
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin query()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereUpdatedAt($value)
 */
	class Admin extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Download
 *
 * @property int $id
 * @property int $download_link_id
 * @property int $file_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property string|null $deleted_at
 * @property-read \App\Models\DownloadLink $downloadLink
 * @property-read \App\Models\File $file
 * @method static \Illuminate\Database\Eloquent\Builder|Download newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Download newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Download query()
 * @method static \Illuminate\Database\Eloquent\Builder|Download whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Download whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Download whereDownloadLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Download whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Download whereId($value)
 */
	class Download extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DownloadLink
 *
 * @property int $id
 * @property int $upload_link_id
 * @property string $path
 * @property string|null $expire_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Download[] $download
 * @property-read int|null $download_count
 * @property-read \App\Models\UploadLink $uploadLink
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadLink authUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadLink query()
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadLink whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadLink whereExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadLink wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DownloadLink whereUploadLinkId($value)
 */
	class DownloadLink extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Favorite
 *
 * @property int $id
 * @property int $user_id
 * @property int $upload_link_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereUploadLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereUserId($value)
 */
	class Favorite extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\File
 *
 * @property int $id
 * @property int $upload_id
 * @property string $path
 * @property string $name
 * @property string $type
 * @property int $size
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Download[] $download
 * @property-read int|null $download_count
 * @property-read \App\Models\Upload $upload
 * @method static \Illuminate\Database\Eloquent\Builder|File authUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|File beforeExpired()
 * @method static \Illuminate\Database\Eloquent\Builder|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File query()
 * @method static \Illuminate\Database\Eloquent\Builder|File searchKeyword($keyword)
 * @method static \Illuminate\Database\Eloquent\Builder|File sortOrder($order)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUploadId($value)
 */
	class File extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Mylist
 *
 * @property int $id
 * @property int $user_id
 * @property int $registered_user_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Mylist authUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|Mylist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mylist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mylist query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mylist searchKeyword($keyword)
 * @method static \Illuminate\Database\Eloquent\Builder|Mylist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mylist whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mylist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mylist whereRegisteredUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mylist whereUserId($value)
 */
	class Mylist extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Upload
 *
 * @property int $id
 * @property int $upload_link_id
 * @property string $sender
 * @property string|null $message
 * @property string|null $expire_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $files
 * @property-read int|null $files_count
 * @property-read \App\Models\UploadLink $uploadLink
 * @method static \Illuminate\Database\Eloquent\Builder|Upload newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Upload newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Upload query()
 * @method static \Illuminate\Database\Eloquent\Builder|Upload whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Upload whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Upload whereExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Upload whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Upload whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Upload whereSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Upload whereUploadLinkId($value)
 */
	class Upload extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UploadLink
 *
 * @property int $id
 * @property int $user_id
 * @property string $path
 * @property string|null $title
 * @property string|null $expire_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Upload|null $upload
 * @method static \Database\Factories\UploadLinkFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|UploadLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UploadLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UploadLink query()
 * @method static \Illuminate\Database\Eloquent\Builder|UploadLink searchKeyword($keyword)
 * @method static \Illuminate\Database\Eloquent\Builder|UploadLink sortOrder($order)
 * @method static \Illuminate\Database\Eloquent\Builder|UploadLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UploadLink whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UploadLink whereExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UploadLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UploadLink wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UploadLink whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UploadLink whereUserId($value)
 */
	class UploadLink extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User searchName($keyword)
 * @method static \Illuminate\Database\Eloquent\Builder|User sortOrder($order)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserCreatekey
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string|null $expire_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserCreatekey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCreatekey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCreatekey query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCreatekey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCreatekey whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCreatekey whereExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCreatekey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCreatekey whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCreatekey whereName($value)
 */
	class UserCreatekey extends \Eloquent {}
}

