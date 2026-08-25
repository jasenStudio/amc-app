<?php

namespace Tests\Feature\Services;

use App\Livewire\Services\ServicesIndex;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ServicesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('dashboard.services.index'))
            ->assertRedirect(route('login'));
    }

    public function test_pending_user_is_redirected_to_pending_approval(): void
    {
        $pending = User::factory()->pending()->create();

        $this->actingAs($pending)
            ->get(route('dashboard.services.index'))
            ->assertRedirect(route('pending.approval'));
    }

    public function test_editor_cannot_view_index(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('dashboard.services.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard.services.index'))
            ->assertOk();
    }

    public function test_super_admin_can_view_index(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->get(route('dashboard.services.index'))
            ->assertOk();
    }

    public function test_search_filters_by_title(): void
    {
        $admin = User::factory()->admin()->create();
        Service::factory()->create(['title' => 'Anclaje Service']);
        Service::factory()->create(['title' => 'Other Service']);

        Livewire::actingAs($admin)
            ->test(ServicesIndex::class)
            ->set('search', 'Anclaje')
            ->assertViewHas('services', fn ($services) => $services->total() === 1);
    }

    public function test_status_filter(): void
    {
        $admin = User::factory()->admin()->create();
        Service::factory()->count(2)->active()->create();
        Service::factory()->count(3)->inactive()->create();

        Livewire::actingAs($admin)
            ->test(ServicesIndex::class)
            ->set('status', 'active')
            ->assertViewHas('services', fn ($services) => $services->total() === 2)
            ->set('status', 'inactive')
            ->assertViewHas('services', fn ($services) => $services->total() === 3);
    }

    public function test_featured_filter(): void
    {
        $admin = User::factory()->admin()->create();
        Service::factory()->count(2)->featured()->create();
        Service::factory()->count(3)->create();

        Livewire::actingAs($admin)
            ->test(ServicesIndex::class)
            ->set('featured', 'yes')
            ->assertViewHas('services', fn ($services) => $services->total() === 2);
    }

    public function test_admin_can_soft_delete_service(): void
    {
        $admin = User::factory()->admin()->create();
        $service = Service::factory()->create();

        Livewire::actingAs($admin)
            ->test(ServicesIndex::class)
            ->call('delete', $service->id);

        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }

    public function test_editor_cannot_delete_service(): void
    {
        $editor = User::factory()->editor()->create();
        $service = Service::factory()->create();

        Livewire::actingAs($editor)
            ->test(ServicesIndex::class)
            ->call('delete', $service->id);

        $this->assertNotSoftDeleted('services', ['id' => $service->id]);
    }
}
