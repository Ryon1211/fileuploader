<?php

namespace Tests\Feature;

use App\Models\UploadLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class FileUploadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_create_form()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.create.upload'));

        $response->assertStatus(200);
    }

    public function test_create_link()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('user.create.upload'), [
                'message' => 'Test message',
                'expire_date' => '2022-10-01 00:00:00',
            ]);

        $response
            ->assertRedirect(route('user.create.upload'))
            ->assertSessionHas('uploadUrl');
    }

    public function test_show_upload_form_with_authenticated()
    {
        $user = User::factory()->create();

        $key = Str::random(20);
        UploadLink::create(
            [
                'user_id' => $user->id,
                'query' => $key,
                'message' => 'Test Message',
                'expire_date' => '2022-02-10 10:00:00',
            ]
        );

        $response = $this
            ->actingAs($user)->get(route('user.upload', ['key' => $key]));

        $response->assertStatus(200);
    }

    public function test_show_upload_form_with_no_authenticated()
    {
        $user = User::factory()->create();

        $key = Str::random(20);
        UploadLink::create(
            [
                'user_id' => $user->id,
                'query' => $key,
                'message' => 'Test Message',
                'expire_date' => '2022-02-10 10:00:00',
            ]
        );

        $response = $this->get(route('user.upload', ['key' => $key]));

        $response->assertStatus(200);
    }

    public function test_upload_files()
    {
        Storage::fake('local');

        $files = [
            new UploadedFile(
                './tests/data/100M.pdf',
                '100M.pdf',
                'application/pdf',
                null,
                true
            ),
            new UploadedFile(
                './tests/data/100M.pdf',
                '100M.pdf',
                'application/pdf',
                null,
                true
            ),
        ];

        $user = User::factory()->create();

        $key = Str::random(20);
        UploadLink::create(
            [
                'user_id' => $user->id,
                'query' => $key,
                'message' => 'Test Message',
                'expire_date' => '2022-02-25 10:00:00',
            ]
        );

        $response = $this
            ->from(route('user.upload', [$key]))
            ->post(route('user.upload', [$key]), [
                'file' => $files,
                'sender' => 'test man',
                'message' => 'test file sended',
                'expire_date' => '2022-02-10 10:00:00',
            ]);

        foreach ($files as $file) {
            Storage::disk('local')
                ->assertExists('public/upload/' . $file->hashName());
        }
        $response->assertRedirect(route('user.upload', [$key]));
    }
}
