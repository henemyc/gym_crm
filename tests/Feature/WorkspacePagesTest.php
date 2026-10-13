<?php

namespace Tests\Feature;

use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspacePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_authenticated_workspace_pages_render_without_template_errors(): void
    {
        $manager = User::factory()->manager()->create();
        $sales = User::factory()->create();
        $lead = Lead::factory()->create([
            'name' => 'Aarohi Shah', 'gym_name' => 'Core Club',
            'assigned_to' => $sales->id, 'created_by' => $manager->id,
        ]);
        FollowUp::create([
            'lead_id' => $lead->id, 'assigned_to' => $sales->id, 'created_by' => $manager->id,
            'kind' => 'follow_up', 'due_at' => now()->addDay(),
        ]);

        $this->actingAs($manager);
        $this->get(route('leads.create'))->assertOk();
        $this->get(route('leads.show', $lead))->assertOk()->assertSee('Aarohi Shah');
        $this->get(route('leads.edit', $lead))->assertOk();
        $this->get(route('follow-ups.index'))->assertOk();
        $this->get(route('follow-ups.create'))->assertOk();
        $this->get(route('calendar.index', ['view' => 'month']))->assertOk();
        $this->get(route('calendar.index', ['view' => 'week']))->assertOk();
        $this->get(route('calendar.index', ['view' => 'day']))->assertOk();
        $this->get(route('imports.index'))->assertOk();
        $this->get(route('reports.index'))->assertOk();
        $this->get(route('prospecting.index'))->assertOk()->assertSee('Gym prospecting')->assertSee('Saved place prospects');
        $this->get(route('settings.index'))->assertOk();
        $this->get(route('settings.users'))->assertOk();
    }

    public function test_admin_settings_render_the_google_places_key_card_and_local_caps(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('settings.index'))
            ->assertOk()
            ->assertSee('Google Places integration')
            ->assertSee('25,000')
            ->assertSee('5,000')
            ->assertSee('per-app safeguards');
    }

    public function test_staff_panel_displays_last_login_and_online_presence(): void
    {
        $admin = User::factory()->admin()->create([
            'last_login_at' => now()->subDay(),
            'last_activity_at' => now(),
        ]);
        User::factory()->create(['name' => 'New Sales Rep']);

        $this->actingAs($admin)->get(route('settings.users'))
            ->assertOk()
            ->assertSee('Last login')
            ->assertSee('Online now')
            ->assertSee('Never signed in');

        $this->getJson(route('settings.users.presence'))
            ->assertOk()
            ->assertJsonPath('online_count', 1);
    }

    public function test_login_and_password_reset_pages_render_for_guests(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Welcome back');
        $this->get(route('password.request'))->assertOk()->assertSee('Reset your password');
        $this->get(route('password.reset', ['token' => 'demo-token']))->assertOk()->assertSee('Choose a new password');
    }

    public function test_crm_global_helpers_are_loaded_by_the_application(): void
    {
        $this->assertTrue(function_exists('crm_label'));
        $this->assertSame('Follow Up', crm_label('follow_up'));
    }
}
