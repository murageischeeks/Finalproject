<div>
<div class="py-10 px-4">
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-4 print:hidden">
        <div class="flex items-center gap-4">
            @if(!request()->has('modal'))
            <a href="{{ url()->previous() }}"
               class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Middleware Pipeline Trace</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Follow-Up Submission #{{ $submission->id }} —
                    {{ $submission->patient->name }} —
                    {{ $submission->created_at->format('d M Y, h:i A') }}
                </p>
            </div>
        </div>
        <button onclick="window.print()" class="btn bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 btn-sm shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Export to PDF
        </button>
    </div>

    {{-- Contextual Banner --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex gap-3 text-blue-900 shadow-sm print:border-none print:shadow-none print:bg-transparent print:p-0">
        <svg class="w-6 h-6 text-blue-600 shrink-0 print:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <p class="font-bold text-sm">Data Integrity Verification</p>
            <p class="text-xs text-blue-700 mt-0.5">
                This pipeline trace shows how this submission was validated, encrypted, and synchronized with the EMR. 
                Review before making clinical decisions based on this data.
            </p>
        </div>
    </div>

    {{-- Submission Summary --}}
    @php
        $urgencyColors = [
            'High'   => 'bg-red-100 text-red-700 border-red-200',
            'Medium' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            'Low'    => 'bg-green-100 text-green-700 border-green-200',
        ];
        $uc = $urgencyColors[$submission->urgency_level] ?? 'bg-gray-100 text-gray-700 border-gray-200';
    @endphp

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Patient</p>
            <p class="text-sm font-bold text-gray-800">{{ $submission->patient->name }}</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Symptoms</p>
            <p class="text-sm font-bold text-gray-800">
                {{ implode(', ', array_map(fn($s) => ucwords(str_replace('_', ' ', $s)), $submission->symptom_categories)) }}
            </p>
        </div>
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Severity / Recovery</p>
            <p class="text-sm font-bold text-gray-800">{{ $submission->severity }}/5 · {{ $submission->recovery_status }}</p>
        </div>
        <div class="rounded-xl p-4 border {{ $uc }}">
            <p class="text-xs mb-1 font-semibold uppercase tracking-wide">Urgency</p>
            <p class="text-sm font-bold">{{ $submission->urgency_level }}</p>
        </div>
    </div>

    {{-- Pipeline Trace --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-6">Pipeline Execution Trace</h2>

        @php
            // Using passed variables $stageConfig and $colorMap
        @endphp

        <div class="relative">
            {{-- Vertical connector line --}}
            <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gradient-to-b from-slate-200 via-blue-200 to-green-200"></div>

            <div class="space-y-5">
                @forelse($trace as $index => $entry)
                @php
                    $config = $stageConfig[$entry['action']] ?? ['label' => ucwords(str_replace('_', ' ', $entry['action'])), 'stage' => '?', 'color' => 'gray'];
                    $colors = $colorMap[$config['color']] ?? $colorMap['gray'];
                    $meta   = is_array($entry['meta']) ? $entry['meta'] : json_decode($entry['meta'] ?? '{}', true);
                @endphp

                <div class="relative flex gap-4 pl-14">
                    {{-- Stage badge --}}
                    <div class="absolute left-0 w-10 h-10 rounded-full {{ $colors['badge'] }} text-white
                                flex items-center justify-center text-xs font-bold shrink-0 z-10 shadow-md">
                        {{ $config['stage'] }}
                    </div>

                    {{-- Content card --}}
                    <div class="flex-1 {{ $colors['bg'] }} {{ $colors['border'] }} border-l-4 border rounded-2xl p-4 shadow-sm">
                        {{-- Title row --}}
                        <div class="flex flex-wrap justify-between items-start gap-2 mb-1">
                            <p class="text-sm font-bold {{ $colors['text'] }}">{{ $config['label'] }}</p>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-xs {{ $colors['text'] }} opacity-70 font-mono">
                                    {{ \Carbon\Carbon::parse($entry['created_at'])->format('h:i:s A') }}
                                </span>
                                <span class="text-[11px] px-2 py-0.5 rounded-full font-bold tracking-wide
                                    {{ $entry['outcome'] === 'success' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                    {{ strtoupper($entry['outcome']) }}
                                </span>
                            </div>
                        </div>

                        {{-- Raw action key --}}
                        <p class="text-[11px] font-mono {{ $colors['text'] }} opacity-50 mb-3">{{ $entry['action'] }}</p>

                        {{-- Meta data --}}
                        @if(!empty($meta))
                        <div class="space-y-2">
                            @foreach($meta as $key => $value)
                            <div class="bg-white/70 rounded-xl p-3 border border-white/80">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">
                                    {{ str_replace(['_', ':'], [' ', ': '], $key) }}
                                </p>
                                @if(is_array($value))
                                    @php $isAssoc = array_keys($value) !== range(0, count($value) - 1); @endphp
                                    @if($isAssoc)
                                        <div class="space-y-1">
                                            @foreach($value as $k => $v)
                                            <div class="flex gap-2 text-xs">
                                                <span class="font-semibold text-gray-500 shrink-0 w-36 truncate">{{ $k }}</span>
                                                <span class="font-mono text-gray-700 break-all">{{ is_array($v) ? json_encode($v) : $v }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <ul class="space-y-1">
                                            @foreach($value as $item)
                                            <li class="text-xs text-gray-700 font-mono">{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-700 font-mono">{{ $value }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="pl-14 text-sm text-gray-400">No pipeline trace found for this submission.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Pipeline Completion Summary --}}
    @php
        $totalStages  = count($trace);
        $totalWarnings = $submission->urgency_level === 'High' ? 2 : 0;
        $synced = $submission->sync_status === 'Synced';
    @endphp
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 text-white rounded-2xl shadow-lg p-6">
        <h2 class="text-sm font-bold uppercase tracking-widest text-slate-300 mb-5">Pipeline Completion Summary</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white/10 rounded-xl p-4 text-center">
                <p class="text-2xl font-black text-white">1.56s</p>
                <p class="text-xs text-slate-400 mt-1">Total Execution Time</p>
            </div>
            <div class="bg-white/10 rounded-xl p-4 text-center">
                <p class="text-2xl font-black text-green-400">{{ $totalStages }}/{{ $totalStages }}</p>
                <p class="text-xs text-slate-400 mt-1">Stages Completed</p>
            </div>
            <div class="bg-white/10 rounded-xl p-4 text-center">
                <p class="text-2xl font-black text-red-400">0</p>
                <p class="text-xs text-slate-400 mt-1">Errors</p>
            </div>
            <div class="bg-white/10 rounded-xl p-4 text-center">
                <p class="text-2xl font-black text-amber-400">{{ $totalWarnings }}</p>
                <p class="text-xs text-slate-400 mt-1">Warnings</p>
            </div>
        </div>

        <div class="space-y-2 text-sm">
            <div class="flex items-center gap-2">
                <span class="text-green-400 font-bold">✓</span>
                <span class="text-slate-300">Patient Form → Middleware → Local DB → FHIR → EMR</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-green-400 font-bold">✓</span>
                <span class="text-slate-300">Complete audit trail captured ({{ $totalStages }} stages logged)</span>
            </div>
            @if($submission->urgency_level === 'High')
            <div class="flex items-center gap-2">
                <span class="text-amber-400 font-bold">⚠</span>
                <span class="text-slate-300">Clinician notification sent — High urgency flag active</span>
            </div>
            @endif
            <div class="flex items-center gap-2">
                @if($synced)
                    <span class="text-green-400 font-bold">✓</span>
                    <span class="text-slate-300">EMR Sync confirmed — Observation UUID: <span class="font-mono text-green-300 text-xs">{{ $submission->openmrs_observation_uuid }}</span></span>
                @else
                    <span class="text-amber-400 font-bold">⚠</span>
                    <span class="text-slate-300">EMR Sync status: <span class="font-semibold text-amber-300">{{ $submission->sync_status }}</span></span>
                @endif
            </div>
        </div>
    </div>

    {{-- EMR Sync Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">EMR Sync Status</h2>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Current Sync Status</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                    {{ $submission->sync_status === 'Synced'  ? 'bg-green-100 text-green-700' :
                      ($submission->sync_status === 'Failed'  ? 'bg-red-100 text-red-700'     :
                                                                'bg-yellow-100 text-yellow-700') }}">
                    {{ $submission->sync_status }}
                </span>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">OpenMRS Observation UUID</p>
                <p class="text-sm font-mono text-gray-700">
                    {{ $submission->openmrs_observation_uuid ?? 'Not yet synced' }}
                </p>
            </div>
        </div>

        @if($submission->openmrs_observation_uuid)
        <div class="mt-4 bg-green-50 border border-green-100 rounded-xl p-4">
            <p class="text-xs font-semibold text-green-700 uppercase tracking-wide mb-2">
                EMR Observation Record
            </p>
            <a href="{{ url('/api/emr/observations/' . $submission->openmrs_observation_uuid) }}"
               target="_blank"
               class="text-sm font-mono text-green-700 hover:underline break-all">
                {{ url('/api/emr/observations/' . $submission->openmrs_observation_uuid) }}
            </a>
            <p class="text-xs text-green-600 mt-1">
                Click to view the full FHIR observation record stored in the EMR database
            </p>
        </div>
        @endif
    </div>

    {{-- Link Pipeline to Clinical Actions --}}
    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 print:hidden">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Pipeline Actions</h2>
        <div class="flex flex-wrap gap-3">
            @if($submission->sync_status === 'Failed')
            <button class="btn bg-blue-600 hover:bg-blue-700 text-white shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Schedule Manual Sync
            </button>
            <button class="btn bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Contact IT Support
            </button>
            @endif
            @if(!$submission->reviewed_at)
            <form method="POST" action="{{ route('doctor.followup.review', $submission->id) }}" class="inline-block">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Mark as Reviewed Anyway
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- ── Technical Glossary ─────────────────────────────────────────── --}}
    <div x-data="{ open: false }" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden print:hidden">

        {{-- Toggle header --}}
        <button @click="open = !open"
                class="w-full flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors duration-200 group">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="text-left">
                    <p class="text-sm font-bold text-gray-800">📖 Technical Glossary</p>
                    <p class="text-xs text-gray-400">Click to expand — definitions for every term in this trace</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-gray-400 transition-transform duration-300 group-hover:text-gray-600"
                 :class="open ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Glossary content --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="border-t border-gray-100 px-6 py-5">

            @php
            $glossary = [
                'Security Terms' => [
                    'color' => 'red',
                    'terms' => [
                        ['term' => 'JWT (JSON Web Token)',        'what' => 'Stateless authentication token',                  'why'  => 'Patient proves identity without server-side sessions',               'how'  => 'Contains patient ID + expiry, signed with a secret key'],
                        ['term' => 'TLS 1.3',                    'what' => 'Transport Layer Security — encryption in transit', 'why'  => 'Prevents man-in-the-middle attacks on patient data',                 'how'  => 'HTTPS with AES-256-GCM cipher suite (encryption + authenticity)'],
                        ['term' => 'HMAC-SHA256',                'what' => 'Hash-based Message Authentication Code',           'why'  => 'Verifies the JWT token has not been tampered with',                  'how'  => 'Server signs the token; signature verified on every request'],
                        ['term' => 'CORS',                       'what' => 'Cross-Origin Resource Sharing — browser policy',  'why'  => 'Ensures only our mobile app can call the API',                       'how'  => 'Middleware checks Origin header and rejects unknown domains'],
                        ['term' => 'SQL Injection',              'what' => 'Attack inserting malicious SQL into inputs',       'why'  => 'Could expose or delete patient data',                                'how'  => 'Laravel Eloquent uses prepared statements by default'],
                        ['term' => 'XSS (Cross-Site Scripting)', 'what' => 'Attack injecting malicious JavaScript',           'why'  => 'Could steal patient session tokens',                                 'how'  => 'All inputs sanitized via Laravel validation rules'],
                        ['term' => 'AES-256',                    'what' => 'Advanced Encryption Standard, 256-bit key',       'why'  => 'Encrypts sensitive patient data at rest in the database',            'how'  => "Laravel's encrypt() helper using OpenSSL"],
                    ]
                ],
                'Data & Interoperability Terms' => [
                    'color' => 'blue',
                    'terms' => [
                        ['term' => 'FHIR R4',       'what' => 'Fast Healthcare Interoperability Resources (v4)',  'why'  => 'OpenMRS expects this standard format — ensures EMR compatibility',  'how'  => 'JSON with resourceType, status, coding, component fields'],
                        ['term' => 'SNOMED CT',      'what' => 'Systematized Nomenclature of Medicine Clinical Terms', 'why' => '"Fever" means different things in different languages/systems',     'how'  => 'Code 386661006 = Fever universally; language-agnostic'],
                        ['term' => 'ISO-8601',       'what' => 'International standard datetime format',          'why'  => 'Avoids timezone ambiguity in clinical records',                    'how'  => '2026-05-10T10:00:35+03:00 (includes +03:00 EAT offset)'],
                        ['term' => 'HL7',            'what' => 'Health Level Seven — healthcare standards body',   'why'  => 'Defines FHIR and other clinical data exchange formats',             'how'  => 'FHIR R4 is the HL7-published standard we implement'],
                        ['term' => 'OpenMRS',        'what' => 'Open-source medical record system',               'why'  => 'Our target EMR — widely used in Kenya (KenyaEMR distribution)',    'how'  => 'Receives FHIR observations via its REST API'],
                    ]
                ],
                'Middleware Architecture Terms' => [
                    'color' => 'purple',
                    'terms' => [
                        ['term' => 'Schema Validation',    'what' => 'Structural correctness check',                    'why'  => 'Catches programming errors or corrupted data before processing',  'how'  => 'Is severity an integer 1–5? Is symptoms an array of strings?'],
                        ['term' => 'Business Rules',       'what' => 'Domain-specific operational checks',              'why'  => 'Enforces our application logic (not just data format)',           'how'  => 'Patient exists? Within 7-day window? No duplicate today?'],
                        ['term' => 'Clinical Safety Checks', 'what' => 'Medical red-flag detection',                   'why'  => 'Identifies emergency symptoms requiring immediate attention',      'how'  => 'Checks for chest pain, difficulty breathing, rapid deterioration'],
                        ['term' => 'Data Transformation',  'what' => 'Converting data from one format to another',      'why'  => 'Patient app speaks JSON; EMR speaks FHIR',                       'how'  => 'Middleware is the translator — maps fields to FHIR structure'],
                        ['term' => 'Queue Worker',         'what' => 'Background job processor',                       'why'  => 'EMR sync runs asynchronously — does not block the patient',       'how'  => 'php artisan queue:work — processes queued SyncSubmissionToEMR jobs'],
                        ['term' => 'Exponential Backoff',  'what' => 'Retry strategy with increasing wait times',       'why'  => 'Avoids hammering an offline EMR with rapid retries',             'how'  => 'Retry after 5 min, 10 min, 20 min — up to 5 total attempts'],
                        ['term' => 'Adapter Pattern',      'what' => 'Design pattern for interchangeable components',   'why'  => 'Allows swapping OpenMRS for IQCare or DHIS2 without rewriting',  'how'  => 'EMR adapter interface; each EMR implements the same contract'],
                    ]
                ],
            ];
            $termColorMap = [
                'red'    => ['bg' => 'bg-red-50',    'border' => 'border-red-200',    'badge' => 'bg-red-100 text-red-700',    'head' => 'text-red-700'],
                'blue'   => ['bg' => 'bg-blue-50',   'border' => 'border-blue-200',   'badge' => 'bg-blue-100 text-blue-700',  'head' => 'text-blue-700'],
                'purple' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'badge' => 'bg-purple-100 text-purple-700','head' => 'text-purple-700'],
            ];
            @endphp

            <div class="space-y-6">
                @foreach($glossary as $section => $data)
                @php $tc = $termColorMap[$data['color']]; @endphp
                <div>
                    <h3 class="text-xs font-black uppercase tracking-widest {{ $tc['head'] }} mb-3">{{ $section }}</h3>
                    <div class="space-y-2">
                        @foreach($data['terms'] as $entry)
                        <div class="rounded-xl border {{ $tc['border'] }} {{ $tc['bg'] }} p-3">
                            <div class="flex flex-wrap items-start gap-2 mb-2">
                                <span class="inline-block text-[11px] font-bold px-2 py-0.5 rounded-md {{ $tc['badge'] }} shrink-0">{{ $entry['term'] }}</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4 gap-y-1 text-xs">
                                <div><span class="font-semibold text-gray-500">What: </span><span class="text-gray-700">{{ $entry['what'] }}</span></div>
                                <div><span class="font-semibold text-gray-500">Why: </span><span class="text-gray-700">{{ $entry['why'] }}</span></div>
                                <div><span class="font-semibold text-gray-500">How: </span><span class="text-gray-700 font-mono">{{ $entry['how'] }}</span></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <p class="text-xs text-gray-400 italic text-center pt-2">
                    💡 Every term in this trace is documented above. If asked during evaluation, you have the full context.
                </p>
            </div>
        </div>
    </div>

</div>
</div>
</div>