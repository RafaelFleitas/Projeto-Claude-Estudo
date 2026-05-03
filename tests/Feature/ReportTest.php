<?php

namespace Tests\Feature;

use App\Enums\GeneratedReportStatus;
use App\Enums\ReportFormat;
use App\Enums\ReportModule;
use App\Jobs\GenerateReportJob;
use App\Models\GeneratedReport;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Index
    // -----------------------------------------------------------------------

    public function test_guest_is_redirected_to_login_on_reports_index(): void
    {
        $response = $this->get(route('reports.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_list_all_reports(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        GeneratedReport::factory()->create(['generated_by' => $admin->id, 'module' => ReportModule::Contracts->value, 'format' => ReportFormat::Pdf->value, 'status' => GeneratedReportStatus::Pending->value]);
        GeneratedReport::factory()->create(['generated_by' => $user->id, 'module' => ReportModule::Contracts->value, 'format' => ReportFormat::Pdf->value, 'status' => GeneratedReportStatus::Pending->value]);

        $response = $this->actingAs($admin)->get(route('reports.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('reports/index'));
    }

    public function test_regular_user_sees_only_own_reports(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();

        $ownReport   = GeneratedReport::factory()->create(['generated_by' => $user->id, 'module' => ReportModule::Contracts->value, 'format' => ReportFormat::Pdf->value, 'status' => GeneratedReportStatus::Pending->value]);
        $otherReport = GeneratedReport::factory()->create(['generated_by' => $other->id, 'module' => ReportModule::Contracts->value, 'format' => ReportFormat::Pdf->value, 'status' => GeneratedReportStatus::Pending->value]);

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('reports/index')
            ->where('reports.data', fn ($data) => collect($data)->contains('id', $ownReport->id)
                && collect($data)->doesntContain('id', $otherReport->id)
            )
        );
    }

    // -----------------------------------------------------------------------
    // Store
    // -----------------------------------------------------------------------

    public function test_authenticated_user_can_create_report_and_job_is_dispatched(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('reports.store'), [
            'module' => 'contracts',
            'format' => 'pdf',
        ]);

        $response->assertRedirect(route('reports.index'));

        $this->assertDatabaseHas('generated_reports', [
            'generated_by' => $user->id,
            'module'       => 'contracts',
            'format'       => 'pdf',
            'status'       => GeneratedReportStatus::Pending->value,
        ]);

        Queue::assertPushed(GenerateReportJob::class);
    }

    public function test_store_report_validation_fails_without_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('reports.store'), []);

        $response->assertInvalid(['module', 'format']);
    }

    // -----------------------------------------------------------------------
    // Show
    // -----------------------------------------------------------------------

    public function test_user_can_view_own_report(): void
    {
        $user   = User::factory()->create();
        $report = GeneratedReport::factory()->create([
            'generated_by' => $user->id,
            'module'       => ReportModule::Contracts->value,
            'format'       => ReportFormat::Pdf->value,
            'status'       => GeneratedReportStatus::Pending->value,
        ]);

        $response = $this->actingAs($user)->get(route('reports.show', $report));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('reports/show'));
    }

    public function test_user_cannot_view_other_users_report(): void
    {
        $user   = User::factory()->create();
        $other  = User::factory()->create();
        $report = GeneratedReport::factory()->create([
            'generated_by' => $other->id,
            'module'       => ReportModule::Contracts->value,
            'format'       => ReportFormat::Pdf->value,
            'status'       => GeneratedReportStatus::Pending->value,
        ]);

        $response = $this->actingAs($user)->get(route('reports.show', $report));

        $response->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Download
    // -----------------------------------------------------------------------

    public function test_user_can_download_completed_report(): void
    {
        Storage::fake();

        $user = User::factory()->create();
        Storage::put('reports/exports/report-completed.pdf', '%PDF-1.4 content');

        $report = GeneratedReport::factory()->create([
            'generated_by' => $user->id,
            'module'       => ReportModule::Contracts->value,
            'format'       => ReportFormat::Pdf->value,
            'status'       => GeneratedReportStatus::Completed->value,
            'file_path'    => 'reports/exports/report-completed.pdf',
            'file_name'    => 'report-completed.pdf',
            'generated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('reports.download', $report));

        $response->assertOk();
    }

    public function test_download_returns_404_when_report_is_pending(): void
    {
        Storage::fake();

        $user   = User::factory()->create();
        $report = GeneratedReport::factory()->create([
            'generated_by' => $user->id,
            'module'       => ReportModule::Contracts->value,
            'format'       => ReportFormat::Pdf->value,
            'status'       => GeneratedReportStatus::Pending->value,
            'file_path'    => null,
            'file_name'    => null,
        ]);

        $response = $this->actingAs($user)->get(route('reports.download', $report));

        $response->assertNotFound();
    }

    public function test_download_returns_404_when_file_missing(): void
    {
        Storage::fake();

        $user   = User::factory()->create();
        $report = GeneratedReport::factory()->create([
            'generated_by' => $user->id,
            'module'       => ReportModule::Contracts->value,
            'format'       => ReportFormat::Pdf->value,
            'status'       => GeneratedReportStatus::Completed->value,
            'file_path'    => 'reports/exports/missing.pdf',
            'file_name'    => 'missing.pdf',
            'generated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('reports.download', $report));

        $response->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // Destroy
    // -----------------------------------------------------------------------

    public function test_user_can_delete_own_report(): void
    {
        Storage::fake();

        $user = User::factory()->create();
        Storage::put('reports/exports/to-delete.pdf', '%PDF-1.4 content');

        $report = GeneratedReport::factory()->create([
            'generated_by' => $user->id,
            'module'       => ReportModule::Contracts->value,
            'format'       => ReportFormat::Pdf->value,
            'status'       => GeneratedReportStatus::Completed->value,
            'file_path'    => 'reports/exports/to-delete.pdf',
            'file_name'    => 'to-delete.pdf',
            'generated_at' => now(),
        ]);

        $response = $this->actingAs($user)->delete(route('reports.destroy', $report));

        $response->assertRedirect(route('reports.index'));
        $this->assertDatabaseMissing('generated_reports', ['id' => $report->id]);
        Storage::assertMissing('reports/exports/to-delete.pdf');
    }

    public function test_user_cannot_delete_other_users_report(): void
    {
        Storage::fake();

        $user   = User::factory()->create();
        $other  = User::factory()->create();
        $report = GeneratedReport::factory()->create([
            'generated_by' => $other->id,
            'module'       => ReportModule::Contracts->value,
            'format'       => ReportFormat::Pdf->value,
            'status'       => GeneratedReportStatus::Pending->value,
            'file_path'    => null,
            'file_name'    => null,
        ]);

        $response = $this->actingAs($user)->delete(route('reports.destroy', $report));

        $response->assertForbidden();
        $this->assertDatabaseHas('generated_reports', ['id' => $report->id]);
    }
}
