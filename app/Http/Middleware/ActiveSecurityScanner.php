<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActiveSecurityScanner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only run the deep scan on data-modifying requests
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $next($request);
        }

        $input = $request->all();

        // 🛡️ Comprehensive SQL Injection Signatures
        $sqlPatterns = [
            '/(?:\b(SELECT|INSERT|UPDATE|DELETE|DROP|UNION|ALTER|CREATE|TRUNCATE|EXEC|DECLARE|CAST)\b.*\b(FROM|INTO|TABLE|DATABASE|INDEX|VIEW)\b)/i',
            '/(\bUNION\b\s+\bALL\b\s+\bSELECT\b)/i',
            '/(\bUNION\b\s+\bSELECT\b)/i',
            '/(\bOR\b\s+1\s*=\s*1)/i',
            '/(\bOR\b\s+\'1\'\s*=\s*\'1\')/i',
            '/(\bOR\b\s+"1"\s*=\s*"1")/i',
            '/(\bOR\b\s+\w+\s*=\s*\w+)/i', // Catches OR a=a
            '/(--|\/\*.*?\*\/|;\s*(DROP|SELECT|UPDATE|INSERT|DELETE|TRUNCATE|ALTER))/i', // SQL comments and stacked queries
            '/(\bSLEEP\s*\(\s*\d+\s*\))/i',
            '/(\bWAITFOR\s+DELAY\b)/i',
            '/(\bBENCHMARK\s*\()/i',
            '/(\bINFORMATION_SCHEMA\b)/i',
            '/(xp_cmdshell)/i',
            '/(\bHAVING\b\s+1\s*=\s*1)/i',
            '/(\bAND\b\s+1\s*=\s*0)/i', // Blind SQLi probes
            '/(\bDROP\b\s+\bTABLE\b)/i',
            '/(\bDROP\b\s+\bDATABASE\b)/i',
        ];

        // 🛡️ Comprehensive Cross-Site Scripting (XSS) & HTML Injection Signatures
        $xssPatterns = [
            '/(<\s*script.*?>.*?<\s*\/\s*script\s*>)/is', // Full script tags
            '/(<\s*script.*?>)/is', // Opening script tags
            '/(javascript\s*:)/i', // inline javascript execution
            '/(vbscript\s*:)/i',
            '/(onload\s*=)/i', // Event handlers
            '/(onerror\s*=)/i',
            '/(onmouseover\s*=)/i',
            '/(onclick\s*=)/i',
            '/(onfocus\s*=)/i',
            '/(onblur\s*=)/i',
            '/(onchange\s*=)/i',
            '/(eval\s*\()/i', // Execution wrappers
            '/(alert\s*\()/i',
            '/(prompt\s*\()/i',
            '/(confirm\s*\()/i',
            '/(document\.(cookie|location|write))/i', // DOM manipulators
            '/(window\.location)/i',
            '/(<iframe.*?>)/i', // Hidden frames
            '/(<object.*?>)/i',
            '/(<embed.*?>)/i',
            '/(<applet.*?>)/i',
            '/(<svg.*?>)/i', // SVG injection
            '/(<link.*?>)/i',
            '/(<meta.*?>)/i',
            '/(data:text\/html)/i', // Data URI injection
            '/(base64,)/i',
            '/(<[a-z]+.*?\bon[a-z]+\s*=\s*)/i', // Catch-all for ANY tag with ANY event handler
        ];

        // 🛡️ The Text Mining / Lexical Pattern Matching Engine
        // Use a recursive function to deeply mine arrays and multiline text blocks
        $detectedThreats = [];
        $matchedPatterns = [];
        $matchedPayloads = [];
        $matchedSubstrings = [];

        $scanValue = function($value) use (&$scanValue, $sqlPatterns, $xssPatterns, &$detectedThreats, &$matchedPatterns, &$matchedPayloads, &$matchedSubstrings) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    $scanValue($val);
                }
            } elseif (is_string($value)) {
                // 1. Scan for SQL Injection
                foreach ($sqlPatterns as $pattern) {
                    if (preg_match($pattern, $value, $matches)) {
                        $detectedThreats['SQL Injection'] = true;
                        $matchedPatterns[] = $pattern;
                        $matchedSubstrings[] = $matches[0];
                        $matchedPayloads[] = $value;
                    }
                }

                // 2. Scan for Cross-Site Scripting (XSS)
                foreach ($xssPatterns as $pattern) {
                    if (preg_match($pattern, $value, $matches)) {
                        $detectedThreats['Cross-Site Scripting (XSS)'] = true;
                        $matchedPatterns[] = $pattern;
                        $matchedSubstrings[] = $matches[0];
                        $matchedPayloads[] = $value;
                    }
                }
            }
        };

        foreach ($input as $key => $value) {
            $scanValue($value);
        }

        // If threats were detected, block the request!
        if (!empty($detectedThreats)) {
            $threatNames = array_keys($detectedThreats);
            $threatTypeString = implode(' & ', $threatNames);

            Log::warning("ActiveSecurityScanner: {$threatTypeString} detected from IP " . $request->ip());

            $submissionId = null;
            if ($request->routeIs('patient.followup.store') && auth()->check()) {
                $submission = \App\Models\FollowUpSubmission::create([
                    'patient_id'         => auth()->id(),
                    'doctor_id'          => $request->input('doctor_id'),
                    'symptom_categories' => $request->input('symptom_categories', []),
                    'severity'           => $request->input('severity', 1),
                    'recovery_status'    => $request->input('recovery_status', 'Uncertain'),
                    'notes'              => $request->input('notes'),
                    'sync_status'        => 'Failed',
                    'urgency_level'      => 'High',
                ]);
                $submissionId = $submission->id;

                \App\Services\AuditLogService::log(
                    action:       'security_checkpoint_failed',
                    resourceType: 'follow_up_submission',
                    resourceId:   $submission->id,
                    outcome:      'failure',
                    meta:         [
                        'threat_type' => $threatTypeString,
                        'patterns'    => $matchedPatterns,
                        'matched_substrings' => $matchedSubstrings,
                        'payloads'    => $matchedPayloads,
                        'ip'          => $request->ip(),
                        'user_agent'  => $request->userAgent(),
                    ]
                );
            }

            // Construct custom error message
            if (count($threatNames) > 1) {
                $errorMessage = "SECURITY VIOLATION: Malicious payloads (SQL Injection & Cross-Site Scripting (XSS) patterns) were detected and actively blocked by the Middleware Text Mining Engine.";
            } else {
                $type = $threatNames[0];
                $errorMessage = "SECURITY VIOLATION: Malicious payload ({$type} pattern) was detected and actively blocked by the Middleware Text Mining Engine.";
            }

            $redirect = redirect()->back()
                ->withInput()
                ->withErrors(['security_block' => $errorMessage]);

            if ($submissionId) {
                $redirect = $redirect->with('failed_submission_id', $submissionId);
            }

            abort($redirect);
        }

        // If the payload is clean, allow the request to proceed deeper into the system
        return $next($request);
    }
}
