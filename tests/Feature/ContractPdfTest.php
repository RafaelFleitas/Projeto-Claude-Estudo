<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\ContractPdf;
use App\Models\User;
use App\Services\ContractPdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContractPdfTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Generate PDF (store)
    // -----------------------------------------------------------------------

    public function test_authenticated_user_can_generate_pdf_for_own_contract(): void
    {
        Storage::fake();

        $user     = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $user->id]);

        $contractPdf = ContractPdf::factory()->create([
            'contract_id'  => $contract->id,
            'generated_by' => $user->id,
            'file_path'    => 'reports/contracts/contract-1-fake.pdf',
            'file_name'    => 'contract-1-fake.pdf',
            'generated_at' => now(),
        ]);

        $this->mock(ContractPdfService::class, function ($mock) use ($contractPdf): void {
            $mock->shouldReceive('generate')->once()->andReturn($contractPdf);
        });

        $response = $this->actingAs($user)->post(route('contract-pdfs.store', $contract));

        $response->assertRedirect(route('contracts.show', $contract));
    }

    public function test_admin_can_generate_pdf_for_any_contract(): void
    {
        Storage::fake();

        $admin    = User::factory()->admin()->create();
        $owner    = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $owner->id]);

        $contractPdf = ContractPdf::factory()->create([
            'contract_id'  => $contract->id,
            'generated_by' => $admin->id,
            'file_path'    => 'reports/contracts/contract-admin.pdf',
            'file_name'    => 'contract-admin.pdf',
            'generated_at' => now(),
        ]);

        $this->mock(ContractPdfService::class, function ($mock) use ($contractPdf): void {
            $mock->shouldReceive('generate')->once()->andReturn($contractPdf);
        });

        $response = $this->actingAs($admin)->post(route('contract-pdfs.store', $contract));

        $response->assertRedirect(route('contracts.show', $contract));
    }

    public function test_pdf_generation_creates_contract_pdf_record(): void
    {
        Storage::fake();

        $user     = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $user->id]);

        $contractPdf = ContractPdf::factory()->create([
            'contract_id'  => $contract->id,
            'generated_by' => $user->id,
            'file_path'    => 'reports/contracts/contract-record.pdf',
            'file_name'    => 'contract-record.pdf',
            'generated_at' => now(),
        ]);

        $this->mock(ContractPdfService::class, function ($mock) use ($contractPdf): void {
            $mock->shouldReceive('generate')->once()->andReturn($contractPdf);
        });

        $this->actingAs($user)->post(route('contract-pdfs.store', $contract));

        $this->assertDatabaseHas('contract_pdfs', ['contract_id' => $contract->id]);
    }

    // -----------------------------------------------------------------------
    // Download
    // -----------------------------------------------------------------------

    public function test_authenticated_user_can_download_contract_pdf(): void
    {
        Storage::fake();

        $user     = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $user->id]);

        Storage::put('reports/contracts/test-download.pdf', '%PDF-1.4 fake content');

        $contractPdf = ContractPdf::factory()->create([
            'contract_id'  => $contract->id,
            'generated_by' => $user->id,
            'file_path'    => 'reports/contracts/test-download.pdf',
            'file_name'    => 'test-download.pdf',
            'generated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('contract-pdfs.download', $contractPdf));

        $response->assertOk();
    }

    public function test_download_returns_404_when_file_does_not_exist(): void
    {
        Storage::fake();

        $user     = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $user->id]);

        $contractPdf = ContractPdf::factory()->create([
            'contract_id'  => $contract->id,
            'generated_by' => $user->id,
            'file_path'    => 'reports/contracts/nonexistent.pdf',
            'file_name'    => 'nonexistent.pdf',
            'generated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('contract-pdfs.download', $contractPdf));

        $response->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // Destroy
    // -----------------------------------------------------------------------

    public function test_admin_can_delete_contract_pdf(): void
    {
        Storage::fake();

        $admin    = User::factory()->admin()->create();
        $owner    = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $owner->id]);

        Storage::put('reports/contracts/to-delete.pdf', '%PDF-1.4 fake content');

        $contractPdf = ContractPdf::factory()->create([
            'contract_id'  => $contract->id,
            'generated_by' => $admin->id,
            'file_path'    => 'reports/contracts/to-delete.pdf',
            'file_name'    => 'to-delete.pdf',
            'generated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->delete(route('contract-pdfs.destroy', $contractPdf));

        $response->assertRedirect(route('contracts.show', $contract->id));
        $this->assertDatabaseMissing('contract_pdfs', ['id' => $contractPdf->id]);
        Storage::assertMissing('reports/contracts/to-delete.pdf');
    }

    public function test_non_admin_cannot_delete_contract_pdf(): void
    {
        Storage::fake();

        $user     = User::factory()->create();
        $other    = User::factory()->create();
        $contract = Contract::factory()->create(['user_id' => $other->id]);

        Storage::put('reports/contracts/protected.pdf', '%PDF-1.4 fake content');

        $contractPdf = ContractPdf::factory()->create([
            'contract_id'  => $contract->id,
            'generated_by' => $other->id,
            'file_path'    => 'reports/contracts/protected.pdf',
            'file_name'    => 'protected.pdf',
            'generated_at' => now(),
        ]);

        $response = $this->actingAs($user)->delete(route('contract-pdfs.destroy', $contractPdf));

        $response->assertForbidden();
        $this->assertDatabaseHas('contract_pdfs', ['id' => $contractPdf->id]);
    }
}
