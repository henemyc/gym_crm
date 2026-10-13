<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadImport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('local');
    }

    public function test_sales_imports_are_assigned_only_to_their_own_queue(): void
    {
        $sales = User::factory()->create();
        $otherSales = User::factory()->create();
        $file = UploadedFile::fake()->createWithContent(
            'assigned-leads.csv',
            "Contact Name,Gym Name,Phone,Assigned To\nAarohi Shah,Core Club,9876543210,{$otherSales->email}\n"
        );

        $this->actingAs($sales)->post(route('imports.upload'), ['file' => $file])->assertRedirect();
        $import = LeadImport::query()->firstOrFail();
        $this->get(route('imports.show', ['leadImport' => $import, 'step' => 'mapping']))
            ->assertOk()->assertDontSee('Assigned to (email or name)');
        $this->post(route('imports.mapping', $import), [
            'mapping' => [0 => 'name', 1 => 'gym_name', 2 => 'phone', 3 => 'assigned_to'],
        ])->assertRedirect();
        $this->post(route('imports.commit', $import))->assertRedirect();

        $lead = Lead::query()->where('name', 'Aarohi Shah')->firstOrFail();
        $this->assertSame($sales->id, $lead->assigned_to);
        $this->assertNotSame($otherSales->id, $lead->assigned_to);
    }

    public function test_csv_upload_mapping_validation_and_commit_imports_only_valid_rows(): void
    {
        $user = User::factory()->manager()->create();
        $file = UploadedFile::fake()->createWithContent('gym-leads.csv', "Contact Name,Gym Name,Phone,City,Priority\nAarohi Shah,Core Club,9876543210,Indore,Hot\n,Iron House,9876543211,Jaipur,Warm\n");
        $this->actingAs($user)->post(route('imports.upload'), ['file' => $file])->assertRedirect();
        $import = LeadImport::query()->firstOrFail();
        $this->get(route('imports.show', ['leadImport' => $import, 'step' => 'preview']))->assertOk()->assertSee('Preview your data');
        $this->get(route('imports.show', ['leadImport' => $import, 'step' => 'mapping']))->assertOk()->assertSee('Match your columns');

        $this->post(route('imports.mapping', $import), ['mapping' => [0 => 'name', 1 => 'gym_name', 2 => 'phone', 3 => 'location', 4 => 'priority']])->assertRedirect();
        $import->refresh();
        $this->assertSame('validated', $import->status);
        $this->assertSame(1, $import->rows_valid);
        $this->assertSame(1, $import->rows_invalid);
        $this->get(route('imports.show', ['leadImport' => $import, 'step' => 'validation']))->assertOk()->assertSee('Validation results');

        $this->post(route('imports.commit', $import))->assertRedirect();
        $this->assertDatabaseHas('leads', ['name' => 'Aarohi Shah', 'phone' => '9876543210', 'priority' => 'hot']);
        $this->assertSame(1, Lead::query()->count());
        $this->assertSame('imported', $import->fresh()->status);
        $this->get(route('imports.show', ['leadImport' => $import, 'step' => 'summary']))->assertOk()->assertSee('Your leads are in the pipeline.');
    }
}
