<?php

namespace Tests\Feature;

use App\Enums\GeneratedReportStatus;
use App\Enums\ReportFormat;
use App\Enums\ReportModule;
use App\Models\GeneratedReport;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TelegramTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Send via controller
    // -----------------------------------------------------------------------

    public function test_completed_report_can_be_sent_to_telegram(): void
    {
        Storage::fake();
        Storage::put('reports/exports/telegram-test.pdf', '%PDF-1.4 content');

        $user   = User::factory()->create();
        $report = GeneratedReport::factory()->create([
            'generated_by' => $user->id,
            'module'       => ReportModule::Contracts->value,
            'format'       => ReportFormat::Pdf->value,
            'status'       => GeneratedReportStatus::Completed->value,
            'file_path'    => 'reports/exports/telegram-test.pdf',
            'file_name'    => 'telegram-test.pdf',
            'generated_at' => now(),
        ]);

        $this->mock(TelegramService::class, function ($mock): void {
            $mock->shouldReceive('sendDocument')->once();
        });

        $response = $this->actingAs($user)->post(route('reports.telegram', $report));

        $response->assertRedirect();
    }

    public function test_pending_report_cannot_be_sent_to_telegram(): void
    {
        $user   = User::factory()->create();
        $report = GeneratedReport::factory()->create([
            'generated_by' => $user->id,
            'module'       => ReportModule::Contracts->value,
            'format'       => ReportFormat::Pdf->value,
            'status'       => GeneratedReportStatus::Pending->value,
            'file_path'    => null,
            'file_name'    => null,
        ]);

        $response = $this->actingAs($user)->post(route('reports.telegram', $report));

        $response->assertStatus(422);
    }

    public function test_non_owner_cannot_send_report_to_telegram(): void
    {
        $user   = User::factory()->create();
        $other  = User::factory()->create();
        $report = GeneratedReport::factory()->create([
            'generated_by' => $other->id,
            'module'       => ReportModule::Contracts->value,
            'format'       => ReportFormat::Pdf->value,
            'status'       => GeneratedReportStatus::Completed->value,
            'file_path'    => 'reports/exports/other.pdf',
            'file_name'    => 'other.pdf',
            'generated_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('reports.telegram', $report));

        $response->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // TelegramService HTTP integration
    // -----------------------------------------------------------------------

    public function test_telegram_service_send_message_calls_correct_url(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $service = app(TelegramService::class);
        $service->sendMessage('Test message');

        Http::assertSent(fn ($request) => str_contains($request->url(), 'https://api.telegram.org/'));
    }

    public function test_telegram_service_send_document_calls_correct_url(): void
    {
        Storage::fake();
        Storage::put('reports/exports/send-doc.pdf', '%PDF-1.4 content');

        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $service = app(TelegramService::class);
        $service->sendDocument('reports/exports/send-doc.pdf', 'Test caption');

        Http::assertSent(fn ($request) => str_contains($request->url(), 'https://api.telegram.org/'));
    }
}
