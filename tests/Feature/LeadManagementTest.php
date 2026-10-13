<?php

namespace Tests\Feature;

use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_dashboard_and_lead_list_render_for_an_authenticated_user(): void
    {
        $user = User::factory()->manager()->create();
        Lead::factory()->create(['name' => 'Ananya Sharma', 'gym_name' => 'PulseFit Studio', 'assigned_to' => $user->id, 'created_by' => $user->id]);

        $this->actingAs($user)->get(route('dashboard'))->assertOk()->assertSee('Total leads')->assertSee('Lead pipeline');
        $this->actingAs($user)->get(route('leads.index'))->assertOk()->assertSee('Ananya Sharma')->assertSee('PulseFit Studio');
    }

    public function test_sales_user_only_sees_leads_assigned_directly_to_them(): void
    {
        $sales = User::factory()->create(['name' => 'Isha Verma']);
        $other = User::factory()->create(['name' => 'Other Rep']);
        Lead::factory()->create(['name' => 'Assigned Prospect', 'assigned_to' => $sales->id, 'created_by' => $sales->id]);
        $touchpointOnlyLead = Lead::factory()->create([
            'name' => 'Touchpoint-only Prospect', 'assigned_to' => $other->id, 'created_by' => $other->id,
        ]);
        FollowUp::create([
            'lead_id' => $touchpointOnlyLead->id, 'assigned_to' => $sales->id, 'created_by' => $other->id,
            'kind' => 'follow_up', 'due_at' => now(),
        ]);

        $this->actingAs($sales)->get(route('leads.index'))
            ->assertOk()->assertSee('Assigned Prospect')->assertDontSee('Touchpoint-only Prospect');
        $this->get(route('leads.show', $touchpointOnlyLead))->assertForbidden();
        $this->get(route('follow-ups.index', ['view' => 'all']))
            ->assertOk()->assertDontSee('Touchpoint-only Prospect');
    }

    public function test_managers_and_admins_keep_workspace_wide_lead_access(): void
    {
        $sales = User::factory()->create();
        $lead = Lead::factory()->create([
            'name' => 'Team-wide Prospect', 'assigned_to' => $sales->id, 'created_by' => $sales->id,
        ]);

        $manager = User::factory()->manager()->create();
        $this->actingAs($manager)->get(route('leads.show', $lead))->assertOk()->assertSee('Team-wide Prospect');

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('leads.show', $lead))->assertOk()->assertSee('Team-wide Prospect');
    }

    public function test_sales_user_cannot_edit_a_lead_assigned_to_another_rep(): void
    {
        $sales = User::factory()->create();
        $other = User::factory()->create();
        $lead = Lead::factory()->create(['assigned_to' => $other->id, 'created_by' => $other->id]);

        $this->actingAs($sales)->get(route('leads.edit', $lead))->assertForbidden();
    }

    public function test_sales_user_can_create_a_lead_and_audit_activity(): void
    {
        $sales = User::factory()->create(['name' => 'Isha Verma']);
        $this->actingAs($sales)->post(route('leads.store'), [
            'name' => 'Neha Kulkarni',
            'gym_name' => 'MoveWell Studio',
            'phone' => '+91 98765 43210',
            'location' => 'Pune',
            'source' => 'Referral',
            'priority' => 'hot',
            'status' => 'new',
            'notes' => 'Owner wants a walkthrough after 4 PM.',
        ])->assertRedirect();

        $lead = Lead::query()->where('name', 'Neha Kulkarni')->firstOrFail();
        $this->assertSame($sales->id, $lead->assigned_to);
        $this->assertDatabaseHas('lead_activities', ['lead_id' => $lead->id, 'event' => 'lead_created']);
        $this->assertDatabaseHas('lead_activities', ['lead_id' => $lead->id, 'event' => 'note']);
    }

    public function test_sales_cannot_assign_a_lead_to_a_different_rep(): void
    {
        $sales = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($sales)->post(route('leads.store'), [
            'name' => 'Neha Kulkarni', 'gym_name' => 'MoveWell Studio', 'phone' => '9876543210',
            'source' => 'Website', 'priority' => 'warm', 'status' => 'new', 'assigned_to' => $other->id,
        ])->assertForbidden();
    }

    public function test_manager_can_update_and_soft_delete_a_lead_with_audit_events(): void
    {
        $manager = User::factory()->manager()->create();
        $sales = User::factory()->create();
        $lead = Lead::factory()->create(['assigned_to' => $sales->id, 'created_by' => $manager->id, 'status' => 'contacted']);

        $this->actingAs($manager)->put(route('leads.update', $lead), [
            'name' => 'Revised Gym Owner', 'gym_name' => 'Core Club', 'phone' => '9876543210',
            'location' => 'Indore', 'source' => 'Referral', 'priority' => 'hot', 'status' => 'negotiation',
            'notes' => 'Pricing review scheduled.', 'assigned_to' => $manager->id,
        ])->assertRedirect(route('leads.show', $lead));

        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => 'negotiation', 'assigned_to' => $manager->id]);
        $this->assertDatabaseHas('lead_activities', ['lead_id' => $lead->id, 'event' => 'status_changed']);
        $this->assertDatabaseHas('lead_activities', ['lead_id' => $lead->id, 'event' => 'assigned']);

        $this->delete(route('leads.destroy', $lead))->assertRedirect(route('leads.index'));
        $this->assertSoftDeleted('leads', ['id' => $lead->id]);
        $this->assertDatabaseHas('lead_activities', ['lead_id' => $lead->id, 'event' => 'lead_deleted']);
    }

    public function test_login_accepts_valid_demo_credentials(): void
    {
        $user = User::factory()->admin()->create(['email' => 'admin@gymxbook.test', 'password' => 'password']);
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $user->refresh();
        $this->assertNotNull($user->last_login_at);
        $this->assertNotNull($user->last_activity_at);
    }

    public function test_presence_ping_refreshes_the_authenticated_users_activity(): void
    {
        $sales = User::factory()->create(['last_activity_at' => now()->subMinutes(12)]);

        $this->actingAs($sales)->post(route('presence.ping'))->assertNoContent();

        $this->assertTrue($sales->fresh()->last_activity_at->greaterThan(now()->subMinute()));
    }

    public function test_csv_export_is_available_to_authenticated_users(): void
    {
        $user = User::factory()->manager()->create();
        Lead::factory()->create(['name' => '=SUM(1,1)', 'assigned_to' => $user->id, 'created_by' => $user->id]);
        $response = $this->actingAs($user)->get(route('leads.export'));

        $response->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString("'=SUM(1,1)", $response->streamedContent());
    }
}
