<?php

/**
 * NFR2 Performance Benchmark Script
 * -----------------------------------
 * Verifies that the system meets the performance requirements defined in
 * Chapter 3, Section 3.6.2 — Non-Functional Requirements:
 *
 *   - A patient submission shall be processed and displayed within 3 seconds.
 *   - EMR API synchronization shall complete within 5 seconds per request.
 *
 * Usage:
 *   php benchmark_nfr2.php
 *
 * Run this from the hospital-system root directory with the server running:
 *   php artisan serve
 */

$baseUrl  = 'http://127.0.0.1:8000';
$results  = [];
$passed   = 0;
$failed   = 0;

// ── Helpers ────────────────────────────────────────────────────────────────

function bench(string $label, callable $fn, float $maxSeconds): array
{
    $start    = microtime(true);
    $response = $fn();
    $elapsed  = round(microtime(true) - $start, 4);
    $ok       = $elapsed <= $maxSeconds;

    return [
        'label'       => $label,
        'elapsed_s'   => $elapsed,
        'max_s'       => $maxSeconds,
        'passed'      => $ok,
        'http_status' => $response['status'] ?? 'N/A',
    ];
}

function httpGet(string $url, array $cookies = []): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_COOKIE         => implode('; ', array_map(
            fn($k, $v) => "$k=$v", array_keys($cookies), $cookies
        )),
    ]);
    $body   = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['status' => $status, 'body' => $body];
}

function httpPost(string $url, array $data, array $cookies = []): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_COOKIE         => implode('; ', array_map(
            fn($k, $v) => "$k=$v", array_keys($cookies), $cookies
        )),
    ]);
    $body   = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['status' => $status, 'body' => $body];
}

// ── Benchmark 1: Dashboard Load (NFR2 — 3-second SLA) ─────────────────────

$results[] = bench(
    label:      'Clinician dashboard page load (/doctor/dashboard)',
    fn:         fn() => httpGet("$baseUrl/doctor/dashboard"),
    maxSeconds: 3.0
);

// ── Benchmark 2: Patient Follow-Up Form Load (NFR2 — 3-second SLA) ────────

$results[] = bench(
    label:      'Patient follow-up form load (/patient/followup/create)',
    fn:         fn() => httpGet("$baseUrl/patient/followup/create"),
    maxSeconds: 3.0
);

// ── Benchmark 3: EMR Observations API (NFR2 — 5-second SLA) ──────────────

$samplePayload = [
    'person'           => 'test-patient-uuid-001',
    'concept'          => 'follow_up_report',
    'obsDatetime'      => date('Y-m-d\TH:i:s'),
    'value'            => json_encode(['severity' => 3, 'recovery_status' => 'improving']),
    'location'         => 'outpatient',
    'follow_up_submission_id' => 9999,
];

$results[] = bench(
    label:      'EMR observations API endpoint (POST /api/emr/observations)',
    fn:         fn() => httpPost("$baseUrl/api/emr/observations", $samplePayload),
    maxSeconds: 5.0
);

// ── Benchmark 4: Submissions List Page (NFR2 — 3-second SLA) ──────────────

$results[] = bench(
    label:      'Follow-up submissions index (/doctor/followup)',
    fn:         fn() => httpGet("$baseUrl/doctor/followup"),
    maxSeconds: 3.0
);

// ── Report ─────────────────────────────────────────────────────────────────

echo PHP_EOL;
echo "==============================================================" . PHP_EOL;
echo "  NFR2 Performance Benchmark Report" . PHP_EOL;
echo "  Generated: " . date('Y-m-d H:i:s') . PHP_EOL;
echo "==============================================================" . PHP_EOL . PHP_EOL;

printf("%-55s %-10s %-8s %-8s %s\n", 'Test', 'Elapsed', 'Max SLA', 'Status', 'HTTP');
echo str_repeat('-', 95) . PHP_EOL;

foreach ($results as $r) {
    $status = $r['passed'] ? 'PASS' : 'FAIL';
    $flag   = $r['passed'] ? '' : ' <-- EXCEEDS SLA';
    printf(
        "%-55s %-10s %-8s %-8s %s%s\n",
        $r['label'],
        $r['elapsed_s'] . 's',
        $r['max_s'] . 's',
        $status,
        $r['http_status'],
        $flag
    );

    if ($r['passed']) {
        $passed++;
    } else {
        $failed++;
    }
}

echo str_repeat('-', 95) . PHP_EOL;
echo PHP_EOL;
echo "Results: $passed passed, $failed failed." . PHP_EOL;
echo ($failed === 0)
    ? "All performance SLAs met. NFR2 is SATISFIED." . PHP_EOL
    : "One or more SLAs exceeded. Investigate before submission." . PHP_EOL;
echo PHP_EOL;
