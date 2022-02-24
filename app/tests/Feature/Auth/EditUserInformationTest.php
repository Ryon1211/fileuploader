<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditUserInformationTest extends TestCase
{
    use RefreshDatabase;
    // showメソッド
    // ログイン済みのユーザーであれば、現在の情報を取得できるか
    public function test_show_edit_user_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('user.account', ['id' => $user->id]));

        $response->assertStatus(200);
    }

    // editメソッド
    // 正常にユーザー情報が変更できるか
    public function test_edit_user_information_has_password_with_confirm()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('user.account', ['id' => $user->id]), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123'
            ]);

        $response
            ->assertRedirect(route('user.account', ['id' => $user->id]))
            ->assertSessionHas('message', function ($value) {
                return $value === 'アカウント情報を更新しました';
            });
    }

    public function test_edit_user_information_has_password_with_out_confirm()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('user.account', ['id' => $user->id]))
            ->post(route('user.account', ['id' => $user->id]), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);

        $response->assertRedirect(route('user.account', ['id' => $user->id]));
    }

    public function test_edit_user_information_has_not_password()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('user.account', ['id' => $user->id]), [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertRedirect(route('user.account', ['id' => $user->id]))
            ->assertSessionHas('message', function ($value) {
                return $value === 'アカウント情報を更新しました';
            });
    }
}
