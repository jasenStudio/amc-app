<?php

namespace Tests\Feature\Services;

use App\Livewire\Services\ServiceForm;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('dashboard.services.create'))
            ->assertRedirect(route('login'));
    }

    public function test_editor_cannot_access_create_form(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('dashboard.services.create'))
            ->assertForbidden();
    }

    public function test_admin_can_access_create_form(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard.services.create'))
            ->assertOk();
    }

    public function test_admin_can_access_edit_form(): void
    {
        $admin = User::factory()->admin()->create();
        $service = Service::factory()->create();

        $this->actingAs($admin)
            ->get(route('dashboard.services.edit', $service))
            ->assertOk();
    }

    public function test_editor_cannot_access_edit_form(): void
    {
        $editor = User::factory()->editor()->create();
        $service = Service::factory()->create();

        $this->actingAs($editor)
            ->get(route('dashboard.services.edit', $service))
            ->assertForbidden();
    }

    public function test_validation_requires_title(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ServiceForm::class)
            ->set('title', '')
            ->call('save')
            ->assertHasErrors(['title' => 'required']);
    }

    public function test_validation_requires_unique_slug(): void
    {
        $admin = User::factory()->admin()->create();
        $existingService = Service::factory()->create(['title' => 'Existing Service']);

        Livewire::actingAs($admin)
            ->test(ServiceForm::class)
            ->set('title', 'New Service')
            ->set('description', 'Test description')
            ->set('slug', $existingService->slug)
            ->call('save')
            ->assertHasErrors(['slug' => 'unique']);
    }

    public function test_validation_requires_description(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ServiceForm::class)
            ->set('description', '')
            ->call('save')
            ->assertHasErrors(['description' => 'required']);
    }

    public function test_validation_accepts_valid_price(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ServiceForm::class)
            ->set('title', 'Test Service')
            ->set('description', 'Test description')
            ->set('price', '1500.50')
            ->set('status', 'active')
            ->call('save')
            ->assertHasNoErrors(['price']);
    }

    public function test_validation_rejects_negative_price(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ServiceForm::class)
            ->set('title', 'Test Service')
            ->set('description', 'Test description')
            ->set('price', '-100')
            ->set('status', 'active')
            ->call('save')
            ->assertHasErrors(['price' => 'min']);
    }

    public function test_creates_service_successfully(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ServiceForm::class)
            ->set('title', 'New Service')
            ->set('slug', 'new-service')
            ->set('description', 'Test description')
            ->set('price', '2500.00')
            ->set('status', 'active')
            ->call('save')
            ->assertRedirect(route('dashboard.services.index'));

        $this->assertDatabaseHas('services', [
            'title' => 'New Service',
            'slug' => 'new-service',
            'price' => '2500.00',
        ]);
    }

    public function test_updates_service_successfully(): void
    {
        $admin = User::factory()->admin()->create();
        $service = Service::factory()->create(['title' => 'Old Title']);

        Livewire::actingAs($admin)
            ->test(ServiceForm::class, ['serviceId' => $service->id])
            ->set('title', 'New Title')
            ->call('save')
            ->assertRedirect(route('dashboard.services.index'));

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'title' => 'New Title',
        ]);
    }

    public function test_optional_fields_can_be_empty(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ServiceForm::class)
            ->set('title', 'New Service')
            ->set('slug', 'new-service')
            ->set('description', 'Test description')
            ->set('excerpt', '')
            ->set('price', '')
            ->set('title_seo', '')
            ->set('status', 'active')
            ->call('save')
            ->assertRedirect(route('dashboard.services.index'));

        $this->assertDatabaseHas('services', [
            'title' => 'New Service',
            'excerpt' => null,
            'price' => null,
            'title_seo' => null,
        ]);
    }

    public function test_slug_is_auto_generated_from_title_for_new_services(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ServiceForm::class)
            ->set('title', 'My New Service')
            ->assertSet('slug', 'my-new-service');
    }

    public function test_regenerate_slug_works(): void
    {
        $admin = User::factory()->admin()->create();
        $service = Service::factory()->create(['title' => 'Original Title', 'slug' => 'original-title']);

        Livewire::actingAs($admin)
            ->test(ServiceForm::class, ['serviceId' => $service->id])
            ->set('title', 'Updated Title')
            ->call('regenerateSlug')
            ->assertSet('slug', 'updated-title');
    }
}
