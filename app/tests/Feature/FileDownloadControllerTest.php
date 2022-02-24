<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Upload;
use App\Models\UploadLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class FileDownloadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_create_form_single_file()
    {
        $key = Str::random(20);

        $user = User::factory()
            ->has(
                UploadLink::factory()
                    ->count(1)
                    ->state(function (array $attributes, User $user) use ($key) {
                        return [
                            'user_id' => $user->id,
                            'query' => $key,
                        ];
                    })
            )
            ->create();

        // $uploadLink = UploadLink::create([
        //     'user_id' => $user->id,
        //     'query' => $key,
        //     'message' => 'Test Message',
        //     'expire_date' => '2022-02-25 10:00:00',
        // ]);

        $upload = Upload::create([
            'upload_link_id' => UploadLink::where('query', $key)->first()->id,
            'sender' => 'Sender',
            'message' => 'Sending test',
            'expire_date' => '2022-02-25 10:00:00',
        ]);

        $file = File::create([
            'upload_id' => $upload->id,
            'path' => 'dummy/path',
            'name' => 'dummy name',
            'type' => 'dummy/file',
            'size' => 200,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('user.create.download'), [
                'id' => [$file->id],
            ]);

        $response
            ->assertStatus(200)
            ->assertViewIs('user.create-download-link')
            ->assertViewHas('status', true);
    }

    public function test_show_create_form_multi_file()
    {
        $user = User::factory()->create();

        $file1 = File::create(
            [
                'upload_id' => 1,
                'path' => 'dummy/path',
                'name' => 'dummy name',
                'type' => 'dummy/file',
                'size' => 200,
            ]
        );

        $file2 = File::create(
            [
                'upload_id' => 1,
                'path' => 'dummy/path',
                'name' => 'dummy name',
                'type' => 'dummy/file',
                'size' => 200,
            ]
        );

        $response = $this
            ->actingAs($user)
            ->post(route('user.create.download'), [
                'id' => [$file1->id, $file2->id],
            ]);

        $response
            ->assertStatus(200)
            ->assertViewIs('user.create-download-link')
            ->assertViewHas('status', false);
    }

    public function test_show_create_form_error()
    {
        $user = User::factory()->create();

        $file1 = File::create(
            [
                'upload_id' => 1,
                'path' => 'dummy/path',
                'name' => 'dummy name',
                'type' => 'dummy/file',
                'size' => 200,
            ]
        );

        $file2 = File::create(
            [
                'upload_id' => 1,
                'path' => 'dummy/path',
                'name' => 'dummy name',
                'type' => 'dummy/file',
                'size' => 200,
            ]
        );

        $response = $this
            ->actingAs($user)
            ->post(route('user.create.download'), [
                'id' => [],
            ]);

        $response
            ->assertStatus(200)
            ->assertViewIs('user.create-download-link')
            ->assertViewHas('status', false);
    }
}
