<?php

namespace Database\Seeders;

use App\Enums\AssignmentStatus;
use App\Enums\BoqRequestStatus;
use App\Enums\EngagementType;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Enums\Suitability;
use App\Enums\TeamStatus;
use App\Enums\TrainingCycleStatus;
use App\Enums\UserRole;
use App\Enums\WorkerStatus;
use App\Models\Assignment;
use App\Models\BoqRequest;
use App\Models\Developer;
use App\Models\Project;
use App\Models\StaffingAgency;
use App\Models\Subcontractor;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TrainingCycle;
use App\Models\TrainingResult;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Admin & Coordinator
        $admin = User::factory()->create([
            'phone' => '+972500000001',
            'role' => UserRole::Admin,
            'role_locked' => true,
        ]);

        $coordinator = User::factory()->create([
            'phone' => '+972500000002',
            'role' => UserRole::Coordinator,
            'role_locked' => true,
        ]);

        // 3 Staffing Agencies
        $agencies = StaffingAgency::factory(3)->create();

        // 5 Developers
        $developers = Developer::factory(5)->create();

        // 5 Subcontractors
        $subcontractors = Subcontractor::factory(5)->create();

        // 20 Workers (some linked to agencies)
        $workers = collect();
        foreach (range(1, 20) as $i) {
            $worker = Worker::factory()->create([
                'staffing_agency_id' => $i <= 12 ? $agencies->random()->id : null,
                'status' => $i <= 15 ? WorkerStatus::Available->value : WorkerStatus::cases()[array_rand(WorkerStatus::cases())]->value,
            ]);
            $workers->push($worker);
        }

        // 5 Projects
        $projects = collect();
        foreach (range(1, 5) as $i) {
            $statuses = [
                ProjectStatus::New->value,
                ProjectStatus::UnderReview->value,
                ProjectStatus::ResourcesAssigned->value,
                ProjectStatus::InProgress->value,
                ProjectStatus::Completed->value,
            ];
            $project = Project::factory()->create([
                'developer_id' => $developers->random()->id,
                'created_by_user_id' => $admin->id,
                'status' => $statuses[$i - 1],
            ]);
            $projects->push($project);
        }

        // 3 Teams
        $leaderWorkers = $workers->where('suitable_for_leader', true)->take(3);
        if ($leaderWorkers->count() < 3) {
            $leaderWorkers = $workers->take(3);
        }

        $teamIndex = 0;
        foreach ($leaderWorkers as $leader) {
            $team = Team::factory()->create([
                'team_leader_id' => $leader->id,
                'current_project_id' => $projects->get($teamIndex)?->id,
                'status' => TeamStatus::Available->value,
            ]);

            // Add leader as team member
            TeamMember::create([
                'team_id' => $team->id,
                'worker_id' => $leader->id,
                'role' => 'team_leader',
                'joined_at' => now(),
            ]);

            // Add 3-5 more workers to each team
            $teamWorkers = $workers->whereNotIn('id', $leaderWorkers->pluck('id'))->random(min(4, $workers->count() - 3));
            foreach ($teamWorkers as $tw) {
                TeamMember::create([
                    'team_id' => $team->id,
                    'worker_id' => $tw->id,
                    'role' => 'general',
                    'joined_at' => now(),
                ]);
            }

            $teamIndex++;
        }

        // 2 Training Cycles
        $completedCycle = TrainingCycle::factory()->create([
            'name' => 'Training Cycle March 2026',
            'start_date' => '2026-03-01',
            'end_date' => '2026-03-14',
            'status' => TrainingCycleStatus::Completed->value,
        ]);

        $plannedCycle = TrainingCycle::factory()->create([
            'name' => 'Training Cycle April 2026',
            'start_date' => '2026-04-01',
            'end_date' => '2026-04-14',
            'status' => TrainingCycleStatus::Planned->value,
        ]);

        // Training results for completed cycle
        foreach ($workers->take(10) as $worker) {
            TrainingResult::create([
                'training_cycle_id' => $completedCycle->id,
                'worker_id' => $worker->id,
                'attendance_day1' => true,
                'attendance_day2' => fake()->boolean(80),
                'professional_score' => fake()->randomFloat(2, 50, 100),
                'suitability' => fake()->randomElement(Suitability::cases())->value,
                'classification' => fake()->randomElement(['team_leader', 'formwork', 'rebar', 'general']),
                'primary_field' => fake()->randomElement(['formwork', 'rebar', 'general', 'finishing']),
            ]);
        }

        // Some assignments
        foreach ($projects->take(3) as $project) {
            Assignment::factory()->create([
                'project_id' => $project->id,
                'resource_type' => Worker::class,
                'resource_id' => $workers->random()->id,
                'engagement_type' => EngagementType::Daily->value,
                'status' => AssignmentStatus::InProgress->value,
            ]);
        }

        // 2 BOQ Requests
        BoqRequest::create([
            'requested_by_user_id' => $developers->first()->user_id,
            'project_name' => 'Tower Haifa',
            'city' => 'Haifa',
            'region' => 'north',
            'project_type' => ProjectType::Residential->value,
            'scope_description' => 'Full skeleton work for 20-story residential tower.',
            'urgency' => 'standard',
            'status' => BoqRequestStatus::New->value,
        ]);

        BoqRequest::create([
            'requested_by_user_id' => $developers->last()->user_id,
            'project_name' => 'Mall Center TLV',
            'city' => 'Tel Aviv',
            'region' => 'center',
            'project_type' => ProjectType::Commercial->value,
            'scope_description' => 'Commercial mall — formwork and rebar for underground parking.',
            'urgency' => 'urgent',
            'status' => BoqRequestStatus::InPreparation->value,
            'prepared_by_user_id' => $coordinator->id,
        ]);
    }
}
