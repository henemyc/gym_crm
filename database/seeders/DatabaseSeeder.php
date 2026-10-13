<?php

namespace Database\Seeders;

use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = collect([
            ['name' => 'Rhea Kapoor', 'email' => 'admin@gymxbook.test', 'role' => 'admin', 'phone' => '9876501200'],
            ['name' => 'Arjun Mehta', 'email' => 'manager@gymxbook.test', 'role' => 'manager', 'phone' => '9876501201'],
            ['name' => 'Isha Verma', 'email' => 'isha@gymxbook.test', 'role' => 'sales', 'phone' => '9876501202'],
            ['name' => 'Kabir Singh', 'email' => 'kabir@gymxbook.test', 'role' => 'sales', 'phone' => '9876501203'],
        ])->mapWithKeys(function (array $data): array {
            $user = User::updateOrCreate(['email' => $data['email']], [
                ...$data,
                'password' => Hash::make('password'),
                'is_active' => true,
                'timezone' => 'Asia/Kolkata',
                'notifications_enabled' => true,
                'email_verified_at' => now(),
            ]);

            return [$data['email'] => $user];
        });

        if (Lead::withTrashed()->exists()) {
            return;
        }

        $leadData = [
            ['name' => 'Ananya Sharma', 'gym_name' => 'PulseFit Studio', 'phone' => '9876543210', 'location' => 'Lucknow', 'source' => 'Instagram', 'priority' => 'hot', 'status' => 'meeting_fixed', 'days' => 0, 'assignee' => 'isha@gymxbook.test', 'notes' => 'Looking to upgrade member management and automate renewals. Wants a demo for the Gomti Nagar branch.'],
            ['name' => 'Rohan Malhotra', 'gym_name' => 'Iron District Gym', 'phone' => '9811122233', 'location' => 'Noida', 'source' => 'Referral', 'priority' => 'hot', 'status' => 'trial', 'days' => 1, 'assignee' => 'kabir@gymxbook.test', 'notes' => 'Two locations, roughly 780 active members. Trial access shared; follow up after owner reviews reporting.'],
            ['name' => 'Priya Nair', 'gym_name' => 'Elevate Wellness Club', 'phone' => '9922445566', 'location' => 'Pune', 'source' => 'Website', 'priority' => 'warm', 'status' => 'follow_up', 'days' => 2, 'assignee' => 'isha@gymxbook.test', 'notes' => 'Asked for a call after 4 PM. Interested in class booking and UPI renewals.'],
            ['name' => 'Vikram Joshi', 'gym_name' => 'Core Culture Fitness', 'phone' => '9826012345', 'location' => 'Indore', 'source' => 'Google', 'priority' => 'warm', 'status' => 'contacted', 'days' => 3, 'assignee' => 'kabir@gymxbook.test', 'notes' => 'Owner requested pricing for a single-site gym with a growing personal training team.'],
            ['name' => 'Meera Iyer', 'gym_name' => 'The Movement Room', 'phone' => '9890011122', 'location' => 'Mumbai', 'source' => 'Walk-in', 'priority' => 'cold', 'status' => 'new', 'days' => 4, 'assignee' => 'isha@gymxbook.test', 'notes' => 'Met at the Fitness Owners Meetup. Send a concise overview before scheduling.'],
            ['name' => 'Aditya Gupta', 'gym_name' => 'Titan Strength Arena', 'phone' => '9810045678', 'location' => 'Delhi', 'source' => 'Facebook', 'priority' => 'hot', 'status' => 'negotiation', 'days' => 5, 'assignee' => 'kabir@gymxbook.test', 'notes' => 'Ready to move forward pending annual plan discount and data migration timeline.'],
            ['name' => 'Sana Khan', 'gym_name' => 'Balance House Yoga', 'phone' => '9795123456', 'location' => 'Jaipur', 'source' => 'Instagram', 'priority' => 'warm', 'status' => 'new', 'days' => 6, 'assignee' => 'isha@gymxbook.test', 'notes' => 'Small studio, 140 members. Wants simple membership reminders and class check-ins.'],
            ['name' => 'Kunal Agarwal', 'gym_name' => 'Apex Fitness Lab', 'phone' => '9871010203', 'location' => 'Gurugram', 'source' => 'Referral', 'priority' => 'hot', 'status' => 'converted', 'days' => 8, 'assignee' => 'kabir@gymxbook.test', 'notes' => 'Converted to annual plan. Onboarding kickoff confirmed for Monday.'],
            ['name' => 'Nisha Patel', 'gym_name' => 'Prana Active Studio', 'phone' => '9898987654', 'location' => 'Ahmedabad', 'source' => 'Website', 'priority' => 'warm', 'status' => 'follow_up', 'days' => 9, 'assignee' => 'isha@gymxbook.test', 'notes' => 'Requested a WhatsApp summary with the pricing tiers and mobile app screenshots.'],
            ['name' => 'Devendra Yadav', 'gym_name' => 'Southside Barbell', 'phone' => '9450011223', 'location' => 'Kanpur', 'source' => 'Phone', 'priority' => 'cold', 'status' => 'contacted', 'days' => 10, 'assignee' => 'kabir@gymxbook.test', 'notes' => 'Spoke briefly with the front desk; owner returns next week.'],
            ['name' => 'Ishita Roy', 'gym_name' => 'Bloom Fitness Collective', 'phone' => '9830098765', 'location' => 'Kolkata', 'source' => 'Campaign', 'priority' => 'warm', 'status' => 'meeting_fixed', 'days' => 11, 'assignee' => 'isha@gymxbook.test', 'notes' => 'Wants to compare check-in and coach scheduling workflows.'],
            ['name' => 'Rahul Bansal', 'gym_name' => 'Grindhouse Training Co.', 'phone' => '9812345678', 'location' => 'Bhopal', 'source' => 'Google', 'priority' => 'cold', 'status' => 'lost', 'days' => 13, 'assignee' => 'kabir@gymxbook.test', 'notes' => 'Went with their current billing provider; revisit after the contract renewal.'],
            ['name' => 'Simran Kaur', 'gym_name' => 'Northstar Athletics', 'phone' => '9815567890', 'location' => 'Chandigarh', 'source' => 'Instagram', 'priority' => 'hot', 'status' => 'trial', 'days' => 14, 'assignee' => 'isha@gymxbook.test', 'notes' => 'Trial account activated; focus on lead capture and membership freezes.'],
            ['name' => 'Aarav Mishra', 'gym_name' => 'Forge Fitness House', 'phone' => '9452112345', 'location' => 'Prayagraj', 'source' => 'Walk-in', 'priority' => 'warm', 'status' => 'negotiation', 'days' => 16, 'assignee' => 'kabir@gymxbook.test', 'notes' => 'Discussing onboarding support for migrating old member data.'],
            ['name' => 'Tanya Das', 'gym_name' => 'Studio Eight Pilates', 'phone' => '9831022445', 'location' => 'Kolkata', 'source' => 'Referral', 'priority' => 'cold', 'status' => 'new', 'days' => 17, 'assignee' => 'isha@gymxbook.test', 'notes' => 'Referral from Ishita. First call not attempted yet.'],
            ['name' => 'Mohit Sethi', 'gym_name' => 'FitRoot Gym & Spa', 'phone' => '9818003344', 'location' => 'Delhi', 'source' => 'Facebook', 'priority' => 'warm', 'status' => 'converted', 'days' => 19, 'assignee' => 'kabir@gymxbook.test', 'notes' => 'Converted after a successful two-week trial.'],
            ['name' => 'Pooja Chaturvedi', 'gym_name' => 'Wildwood Wellness', 'phone' => '9793004455', 'location' => 'Lucknow', 'source' => 'Google', 'priority' => 'warm', 'status' => 'contacted', 'days' => 21, 'assignee' => 'isha@gymxbook.test', 'notes' => 'Asked for references from boutique studios using GymXBook.'],
            ['name' => 'Siddharth Rao', 'gym_name' => 'Peak Performance Club', 'phone' => '9845011223', 'location' => 'Bengaluru', 'source' => 'Website', 'priority' => 'hot', 'status' => 'follow_up', 'days' => 23, 'assignee' => 'kabir@gymxbook.test', 'notes' => 'Expansion planned this quarter; needs role-based access for two branches.'],
            ['name' => 'Aditi Deshmukh', 'gym_name' => 'Mile Zero Strength', 'phone' => '9822001122', 'location' => 'Pune', 'source' => 'Referral', 'priority' => 'cold', 'status' => 'lost', 'days' => 27, 'assignee' => 'isha@gymxbook.test', 'notes' => 'Budget on pause until the next financial quarter.'],
            ['name' => 'Harsh Vardhan', 'gym_name' => 'BluePeak Fitness', 'phone' => '9810019876', 'location' => 'Noida', 'source' => 'Phone', 'priority' => 'warm', 'status' => 'new', 'days' => 30, 'assignee' => 'kabir@gymxbook.test', 'notes' => 'Inbound query for lead management, attendance, and online payments.'],
        ];

        DB::transaction(function () use ($leadData, $users): void {
            foreach ($leadData as $index => $data) {
                $assignee = $users[$data['assignee']];
                $createdAt = now()->subDays($data['days'])->subHours(($index * 3) % 15);
                $lead = Lead::create([
                    'name' => $data['name'],
                    'gym_name' => $data['gym_name'],
                    'phone' => $data['phone'],
                    'location' => $data['location'],
                    'source' => $data['source'],
                    'priority' => $data['priority'],
                    'status' => $data['status'],
                    'notes' => $data['notes'],
                    'assigned_to' => $assignee->id,
                    'created_by' => $users['manager@gymxbook.test']->id,
                ]);
                $lead->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();

                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'user_id' => $users['manager@gymxbook.test']->id,
                    'event' => 'lead_created',
                    'title' => 'Lead added to pipeline',
                    'description' => "Added from {$lead->source} and assigned to {$assignee->name}.",
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                if ($index < 12) {
                    $dueOffset = match ($index % 4) { 0 => 0, 1 => 1, 2 => -1, default => 3 };
                    $dueAt = now()->startOfDay()->addDays($dueOffset)->setTime(10 + ($index % 6), ($index % 2) * 30);
                    $completed = in_array($index, [2, 5, 9], true);
                    $followUp = FollowUp::create([
                        'lead_id' => $lead->id,
                        'assigned_to' => $assignee->id,
                        'created_by' => $users['manager@gymxbook.test']->id,
                        'kind' => $index % 5 === 0 ? 'meeting' : ($index % 7 === 0 ? 'trial' : 'follow_up'),
                        'due_at' => $dueAt,
                        'completed_at' => $completed ? $dueAt->copy()->addMinutes(20) : null,
                        'outcome' => $completed ? 'interested' : null,
                        'remark' => $completed ? 'Had a positive conversation; shared a tailored walkthrough.' : null,
                        'meeting_at' => $index % 5 === 0 ? $dueAt->copy()->addDay() : null,
                        'meeting_with' => $index % 5 === 0 ? $data['name'] : null,
                        'trial_at' => $index % 7 === 0 ? $dueAt->copy()->addDays(2) : null,
                        'trial_details' => $index % 7 === 0 ? 'Guided product trial and membership import walkthrough.' : null,
                    ]);

                    if ($completed) {
                        LeadActivity::create([
                            'lead_id' => $lead->id,
                            'user_id' => $assignee->id,
                            'event' => 'follow_up_completed',
                            'title' => 'Follow-up completed',
                            'description' => 'Interested — shared a tailored walkthrough.',
                            'metadata' => ['follow_up_id' => $followUp->id],
                            'created_at' => $followUp->completed_at,
                            'updated_at' => $followUp->completed_at,
                        ]);
                    }
                }
            }
        });
    }
}
