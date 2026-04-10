<?php

namespace Tests\Feature\Filament;

use App\Enums\WorkerStatus;
use App\Filament\Resources\WorkerResource\Pages\CreateWorker;
use App\Models\User;
use App\Models\Worker;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class CreateWorkerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'session.driver' => 'array',
            'cache.default' => 'array',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_create_requires_a_linked_user(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin);

        Livewire::test(CreateWorker::class)
            ->set('data.full_name', 'Worker Without Linked User')
            ->set('data.status', WorkerStatus::Available->value)
            ->call('create')
            ->assertHasFormErrors([
                'user_id' => 'required',
            ]);

        $this->assertDatabaseMissing('workers', [
            'full_name' => 'Worker Without Linked User',
        ]);
    }

    public function test_create_rejects_a_user_that_is_already_linked_to_another_worker(): void
    {
        $admin = User::factory()->admin()->create();
        $linkedUser = User::factory()->worker()->create();

        Worker::factory()->create([
            'user_id' => $linkedUser->id,
        ]);

        Livewire::actingAs($admin);

        Livewire::test(CreateWorker::class)
            ->set('data.full_name', 'Duplicate Linked User Worker')
            ->set('data.user_id', $linkedUser->id)
            ->set('data.status', WorkerStatus::Available->value)
            ->call('create')
            ->assertHasFormErrors([
                'user_id',
            ]);

        $this->assertSame(1, Worker::where('user_id', $linkedUser->id)->count());
    }

    public function test_create_persists_a_worker_when_the_form_is_valid(): void
    {
        $admin = User::factory()->admin()->create();
        $freeUser = User::factory()->worker()->create();

        Livewire::actingAs($admin);

        Livewire::test(CreateWorker::class)
            ->set('data.full_name', 'Created From Admin')
            ->set('data.user_id', $freeUser->id)
            ->set('data.status', WorkerStatus::Available->value)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('workers', [
            'full_name' => 'Created From Admin',
            'user_id' => $freeUser->id,
            'status' => WorkerStatus::Available->value,
        ]);
    }

    public function test_create_another_persists_a_worker_without_throwing_a_server_error(): void
    {
        $admin = User::factory()->admin()->create();
        $freeUser = User::factory()->worker()->create();

        Livewire::actingAs($admin);

        Livewire::test(CreateWorker::class)
            ->set('data.full_name', 'Created From Admin Create Another')
            ->set('data.user_id', $freeUser->id)
            ->set('data.status', WorkerStatus::Available->value)
            ->call('createAnother')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('workers', [
            'full_name' => 'Created From Admin Create Another',
            'user_id' => $freeUser->id,
            'status' => WorkerStatus::Available->value,
        ]);
    }
}
