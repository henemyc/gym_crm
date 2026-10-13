<?php

namespace Tests\Feature;

use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowUpWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_completing_a_touchpoint_records_outcome_updates_pipeline_and_schedules_next_step(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create(['assigned_to' => $user->id, 'created_by' => $user->id, 'status' => 'contacted']);
        $followUp = FollowUp::create([
            'lead_id' => $lead->id, 'assigned_to' => $user->id, 'created_by' => $user->id,
            'kind' => 'follow_up', 'due_at' => now()->subHour(),
        ]);

        $this->actingAs($user)->patch(route('follow-ups.complete', $followUp), [
            'outcome' => 'meeting_fixed',
            'remark' => 'Owner confirmed a product demo for their team.',
            'meeting_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'meeting_with' => 'Ananya Sharma',
            'next_follow_up_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ])->assertRedirect();

        $this->assertDatabaseHas('follow_ups', ['id' => $followUp->id, 'outcome' => 'meeting_fixed']);
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => 'meeting_fixed']);
        $this->assertSame(2, FollowUp::query()->where('lead_id', $lead->id)->count());
        $this->assertDatabaseHas('lead_activities', ['lead_id' => $lead->id, 'event' => 'follow_up_completed']);
        $this->assertDatabaseHas('lead_activities', ['lead_id' => $lead->id, 'event' => 'follow_up_scheduled']);
    }

    public function test_calendar_and_reports_are_available_to_authenticated_users(): void
    {
        $user = User::factory()->manager()->create();
        $lead = Lead::factory()->create(['assigned_to' => $user->id, 'created_by' => $user->id, 'status' => 'converted']);
        FollowUp::create([
            'lead_id' => $lead->id, 'assigned_to' => $user->id, 'created_by' => $user->id,
            'kind' => 'meeting', 'due_at' => now()->addDay(), 'meeting_at' => now()->addDay(), 'meeting_with' => $lead->name,
        ]);

        $this->actingAs($user)->get(route('calendar.index'))->assertOk()->assertSee('Calendar');
        $this->actingAs($user)->get(route('reports.index'))->assertOk()->assertSee('Leads by source')->assertSee('Conversion rate');
        $this->actingAs($user)->get(route('follow-ups.index', ['view' => 'upcoming']))->assertOk();
    }
}
