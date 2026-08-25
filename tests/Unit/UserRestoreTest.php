<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRestoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_restores_soft_deleted_user_by_email(): void
    {
        $user = User::factory()->create(['email' => 'deleted@example.com']);
        $user->delete();

        $this->assertTrue(User::restoreByEmailOrId('deleted@example.com'));
        $this->assertNotNull(User::find($user->id));
        $this->assertNull(User::withTrashed()->find($user->id)->deleted_at);
    }

    public function test_restores_soft_deleted_user_by_id(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $this->assertTrue(User::restoreByEmailOrId($user->id));
        $this->assertNotNull(User::find($user->id));
    }

    public function test_returns_false_for_non_deleted_user(): void
    {
        $user = User::factory()->create();

        $this->assertFalse(User::restoreByEmailOrId($user->id));
        $this->assertFalse(User::restoreByEmailOrId($user->email));
    }

    public function test_returns_false_for_unknown_email_or_id(): void
    {
        $this->assertFalse(User::restoreByEmailOrId('unknown@example.com'));
        $this->assertFalse(User::restoreByEmailOrId(999999));
    }
}
