<?php

namespace Tests\Feature\Users;

use App\Enums\UserRole;
use App\Filters\UserFilter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_filters_by_name(): void
    {
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);

        $filter = new UserFilter(search: 'John');
        $results = $filter->apply()->get();

        $this->assertCount(1, $results);
        $this->assertSame('John Doe', $results->first()->name);
    }

    public function test_search_filters_by_email(): void
    {
        User::factory()->create(['email' => 'john@example.com']);
        User::factory()->create(['email' => 'jane@example.com']);

        $filter = new UserFilter(search: 'john@');
        $results = $filter->apply()->get();

        $this->assertCount(1, $results);
        $this->assertSame('john@example.com', $results->first()->email);
    }

    public function test_role_filter(): void
    {
        User::factory()->superAdmin()->create();
        User::factory()->admin()->create();
        User::factory()->editor()->create();
        User::factory()->pending()->create();

        $filter = new UserFilter(role: UserRole::Admin->value);
        $results = $filter->apply()->get();

        $this->assertCount(1, $results);
        $this->assertSame(UserRole::Admin, $results->first()->role);
    }

    public function test_combined_filters(): void
    {
        User::factory()->create(['name' => 'John Admin', 'role' => UserRole::Admin]);
        User::factory()->create(['name' => 'John Editor', 'role' => UserRole::Editor]);
        User::factory()->create(['name' => 'Jane Admin', 'role' => UserRole::Admin]);

        $filter = new UserFilter(search: 'John', role: UserRole::Admin->value);
        $results = $filter->apply()->get();

        $this->assertCount(1, $results);
        $this->assertSame('John Admin', $results->first()->name);
    }

    public function test_empty_filters_return_all_users(): void
    {
        User::factory()->count(3)->create();

        $filter = new UserFilter;
        $results = $filter->apply()->get();

        $this->assertCount(3, $results);
    }

    public function test_results_are_ordered_by_id_desc(): void
    {
        $first = User::factory()->create();
        $second = User::factory()->create();
        $third = User::factory()->create();

        $filter = new UserFilter;
        $results = $filter->apply()->get();

        $this->assertSame($third->id, $results->first()->id);
        $this->assertSame($first->id, $results->last()->id);
    }
}
