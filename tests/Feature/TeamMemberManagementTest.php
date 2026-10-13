<?php

namespace Tests\Feature;

use App\Models\FollowUp;
use App\Models\GooglePlaceProspect;
use App\Models\GooglePlaceProspectActivity;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadImport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_admin_can_create_a_teammate_with_ajax(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson(route('settings.users.store'), [
            'name' => 'Maya Patel',
            'email' => 'maya@gymxbook.test',
            'phone' => '9876501234',
            'role' => 'sales',
            'password' => 'strong-password-24',
            'password_confirmation' => 'strong-password-24',
        ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Maya Patel has been added to the workspace.')
            ->assertJsonPath('summary.teamCount', 2)
            ->assertJsonPath('summary.salesCount', 1);
        $this->assertDatabaseHas('users', ['email' => 'maya@gymxbook.test', 'role' => 'sales', 'is_active' => true]);
        $this->assertStringContainsString('Maya Patel', $response->json('rows_html'));
    }

    public function test_admin_can_update_profile_role_and_access_with_ajax(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['name' => 'Old Name']);

        $this->actingAs($admin)->patchJson(route('settings.users.update', $member), [
            'name' => 'Updated Name',
            'email' => $member->email,
            'phone' => '9876501111',
            'role' => 'manager',
            'is_active' => false,
        ])->assertOk()
            ->assertJsonPath('message', 'Team member details and access were updated.');

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'name' => 'Updated Name',
            'role' => 'manager',
            'is_active' => false,
            'phone' => '9876501111',
        ]);
    }

    public function test_workspace_cannot_lose_its_last_active_admin_or_allow_self_removal(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->patchJson(route('settings.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'phone' => $admin->phone,
            'role' => 'manager',
            'is_active' => true,
        ])->assertUnprocessable()
            ->assertJsonPath('message', 'At least one active admin is required.');

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'admin', 'is_active' => true]);
        $this->deleteJson(route('settings.users.destroy', $admin))->assertUnprocessable()
            ->assertJsonPath('message', 'You cannot remove your own account.');
    }

    public function test_removing_a_sales_rep_transfers_leads_followups_and_import_history(): void
    {
        $manager = User::factory()->manager()->create();
        $sales = User::factory()->create(['name' => 'Leaving Rep']);
        $lead = Lead::factory()->create([
            'name' => 'Transferred Prospect', 'assigned_to' => $sales->id, 'created_by' => $manager->id,
        ]);
        $followUp = FollowUp::create([
            'lead_id' => $lead->id, 'assigned_to' => $sales->id, 'created_by' => $manager->id,
            'kind' => 'follow_up', 'due_at' => now()->addDay(),
        ]);
        $import = LeadImport::create([
            'user_id' => $sales->id, 'filename' => 'leads.csv', 'file_path' => 'imports/1/leads.csv', 'status' => 'imported',
        ]);
        $prospect = GooglePlaceProspect::query()->create([
            'place_id' => 'ChIJLeavingRepProspect001',
            'assigned_to' => $sales->id,
            'created_by' => $manager->id,
            'status' => 'contacted',
        ]);

        $this->actingAs($manager)->deleteJson(route('settings.users.destroy', $sales))
            ->assertOk()
            ->assertJsonPath('summary.teamCount', 1);

        $this->assertDatabaseMissing('users', ['id' => $sales->id]);
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'assigned_to' => $manager->id]);
        $this->assertDatabaseHas('follow_ups', ['id' => $followUp->id, 'assigned_to' => $manager->id]);
        $this->assertDatabaseHas('lead_imports', ['id' => $import->id, 'user_id' => $manager->id]);
        $this->assertDatabaseHas('google_place_prospects', ['id' => $prospect->id, 'assigned_to' => $manager->id]);
        $this->assertDatabaseHas('google_place_prospect_activities', [
            'prospect_id' => $prospect->id,
            'event' => 'reassigned',
            'actor_id' => $manager->id,
            'to_assigned_to' => $manager->id,
        ]);
        $this->assertDatabaseHas('lead_activities', ['lead_id' => $lead->id, 'event' => 'assigned', 'user_id' => $manager->id]);
        $this->assertDatabaseHas('lead_activities', ['lead_id' => $lead->id, 'event' => 'follow_up_reassigned', 'user_id' => $manager->id]);
        $this->assertSame(2, LeadActivity::query()->where('lead_id', $lead->id)->count());
    }

    public function test_manager_cannot_edit_or_remove_another_manager(): void
    {
        $manager = User::factory()->manager()->create();
        $otherManager = User::factory()->manager()->create();

        $this->actingAs($manager)->patchJson(route('settings.users.update', $otherManager), [
            'name' => $otherManager->name,
            'email' => $otherManager->email,
            'role' => 'sales',
            'is_active' => true,
        ])->assertForbidden();
        $this->deleteJson(route('settings.users.destroy', $otherManager))->assertForbidden();
    }
}
