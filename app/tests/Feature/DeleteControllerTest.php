<?php

namespace Tests\Feature;

use App\Models\DownloadLink;
use App\Models\File;
use App\Models\UploadLink;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeleteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_file_all()
    {
        // ユーザー
        $user = User::factory()->create();

        // アップロードリンク
        $key = Str::random(20);
        $uploadLink = UploadLink::create([
            'user_id' => $user->id,
            'query' => $key,
            'message' => 'Test Message',
            'expire_date' => '2022-02-25 10:00:00',
        ]);

        // アップロード
        $upload =  Upload::create([
            'upload_link_id' => $uploadLink->id,
            'sender' => 'test user',
            'message' => 'test message',
            'expire_date' => '2022-10-01 00:00:00',
        ]);

        // ファイル
        $file1 = File::create([
            'upload_id' => $upload->id,
            'path' => '/example/test',
            'name' => 'test',
            'type' => 'type/type',
            'size' => 2000,
        ]);

        $file2 = File::create([
            'upload_id' => $upload->id,
            'path' => '/example/test',
            'name' => 'test',
            'type' => 'type/type',
            'size' => 2000,
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('user.upload', ['key' => $key]))
            ->post(
                route('user.delete.file'),
                ['id' => [$file1->id, $file2->id]]
            );

        $response->assertRedirect(route('user.dashboard'));
    }

    public function test_delete_file_part()
    {
        // ユーザー
        $user = User::factory()->create();

        // アップロードリンク
        $key = Str::random(20);
        $uploadLink = UploadLink::create([
            'user_id' => $user->id,
            'query' => $key,
            'message' => 'Test Message',
            'expire_date' => '2022-02-25 10:00:00',
        ]);

        // アップロード
        $upload =  Upload::create([
            'upload_link_id' => $uploadLink->id,
            'sender' => 'test user',
            'message' => 'test message',
            'expire_date' => '2022-10-01 00:00:00',
        ]);

        // ファイル
        $file1 = File::create([
            'upload_id' => $upload->id,
            'path' => '/example/test',
            'name' => 'test',
            'type' => 'type/type',
            'size' => 2000,
        ]);

        $file2 = File::create([
            'upload_id' => $upload->id,
            'path' => '/example/test',
            'name' => 'test',
            'type' => 'type/type',
            'size' => 2000,
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('user.upload', ['key' => $key]))
            ->post(
                route('user.delete.file'),
                ['id' => [$file1->id]]
            );

        $response
            ->assertRedirect(route('user.upload', ['key' => $key]))
            ->assertSessionHasErrors('error');
    }

    public function test_delete_file_error()
    {
        // ユーザー
        $user = User::factory()->create();

        // アップロードリンク
        $key = Str::random(20);
        $uploadLink = UploadLink::create([
            'user_id' => $user->id,
            'query' => $key,
            'message' => 'Test Message',
            'expire_date' => '2022-02-25 10:00:00',
        ]);

        // アップロード
        $upload =  Upload::create([
            'upload_link_id' => $uploadLink->id,
            'sender' => 'test user',
            'message' => 'test message',
            'expire_date' => '2022-10-01 00:00:00',
        ]);

        // ファイル
        $file1 = File::create([
            'upload_id' => $upload->id,
            'path' => '/example/test',
            'name' => 'test',
            'type' => 'type/type',
            'size' => 2000,
        ]);

        $file2 = File::create([
            'upload_id' => $upload->id,
            'path' => '/example/test',
            'name' => 'test',
            'type' => 'type/type',
            'size' => 2000,
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('user.upload', ['key' => $key]))
            ->post(
                route('user.delete.file'),
                ['id' => []]
            );

        $response
            ->assertRedirect(route('user.upload', ['key' => $key]))
            ->assertSessionHasErrors('error');
    }

    public function test_delete_upload_link()
    {
        // ユーザー
        $user = User::factory()->create();

        // アップロードリンク
        $key = Str::random(20);
        $uploadLink = UploadLink::create([
            'user_id' => $user->id,
            'query' => $key,
            'message' => 'Test Message',
            'expire_date' => '2022-02-25 10:00:00',
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('user.dashboard'))
            ->post(route('user.delete.upload'), ['id' => [$uploadLink->id]]);

        $response
            ->assertRedirect(route('user.dashboard'));
    }

    public function test_delete_upload_link_error()
    {
        // ユーザー
        $user = User::factory()->create();

        // アップロードリンク
        $key = Str::random(20);
        $uploadLink = UploadLink::create([
            'user_id' => $user->id,
            'query' => $key,
            'message' => 'Test Message',
            'expire_date' => '2022-02-25 10:00:00',
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('user.dashboard'))
            ->post(route('user.delete.upload'), ['id' => []]);

        $response
            ->assertRedirect(route('user.dashboard'))
            ->assertSessionHasErrors('error');
    }

    public function test_delete_download_link()
    {
        // ユーザー
        $user = User::factory()->create();

        // アップロードリンク
        $uploadKey = Str::random(20);
        UploadLink::create([
            'user_id' => $user->id,
            'query' => $uploadKey,
            'message' => 'Test Message',
            'expire_date' => '2022-02-25 10:00:00',
        ]);

        $downloadKey = Str::random(20);
        $downloadLink = DownloadLink::create([
            'upload_link_id' => 1,
            'query' => $downloadKey,
            'expire_date' => '2022-02-25 10:00:00',
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('user.upload', ['key' => $uploadKey]))
            ->post(route('user.delete.download'), ['id' => [$downloadLink->id]]);

        $response->assertRedirect(route('user.upload', ['key' => $uploadKey]));
    }

    public function test_delete_download_link_error()
    {
        // ユーザー
        $user = User::factory()->create();

        // アップロードリンク
        $uploadKey = Str::random(20);
        UploadLink::create([
            'user_id' => $user->id,
            'query' => $uploadKey,
            'message' => 'Test Message',
            'expire_date' => '2022-02-25 10:00:00',
        ]);

        $downloadKey = Str::random(20);
        $downloadLink = DownloadLink::create([
            'upload_link_id' => 1,
            'query' => $downloadKey,
            'expire_date' => '2022-02-25 10:00:00',
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('user.upload', ['key' => $uploadKey]))
            ->post(route('user.delete.download'), ['id' => []]);

        $response
            ->assertRedirect(route('user.upload', ['key' => $uploadKey]))
            ->assertSessionHasErrors('error');
    }
}
