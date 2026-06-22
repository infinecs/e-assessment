<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('images/logos/Infinecs-Logo-Square.ico') }}">
    <title>Assessment Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }

        /* Score ring animation */
        .score-ring { transform: rotate(-90deg); }
        .score-ring circle {
            stroke-dasharray: 440;
            stroke-dashoffset: 440;
            transition: stroke-dashoffset 1.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Counter animation */
        @keyframes countUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .score-value { animation: countUp 0.6s ease 0.4s both; }

        /* Badge pulse */
        @keyframes badgePop {
            0%  { transform: scale(0.5); opacity: 0; }
            70% { transform: scale(1.1); }
            100%{ transform: scale(1);   opacity: 1; }
        }
        .grade-badge { animation: badgePop 0.5s ease 1.6s both; }

        /* Card slide-in */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .result-card { animation: slideUp 0.5s ease both; }

        /* Confetti (only for 100%) */
        .confetti-piece {
            position: fixed;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            opacity: 0;
        }
        @keyframes confettiFall {
            0%   { transform: translateY(-100px) rotate(0deg);   opacity: 1; }
            100% { transform: translateY(110vh)  rotate(720deg); opacity: 0; }
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex flex-col">

    @php
        $score      = $result['score'];
        $total      = $result['total'];
        $percentage = $total > 0 ? ($score / $total) * 100 : 0;

        if ($percentage == 100) {
            $grade      = 'Perfect Score!';
            $gradeColor = 'bg-green-100 text-green-700 border-green-200';
            $ringColor  = '#22c55e'; // green-500
        } elseif ($percentage >= 75) {
            $grade      = 'Excellent';
            $gradeColor = 'bg-blue-100 text-blue-700 border-blue-200';
            $ringColor  = '#3b82f6'; // blue-500
        } elseif ($percentage >= 50) {
            $grade      = 'Good';
            $gradeColor = 'bg-yellow-100 text-yellow-700 border-yellow-200';
            $ringColor  = '#eab308'; // yellow-500
        } else {
            $grade      = 'Keep Practising';
            $gradeColor = 'bg-red-100 text-red-700 border-red-200';
            $ringColor  = '#ef4444'; // red-500
        }

        $circumference = 440; // 2 * π * 70 ≈ 440
        $offset        = $circumference - ($percentage / 100) * $circumference;
    @endphp

    <!-- Header bar -->
    <header class="w-full px-6 py-4 flex items-center">
        <img src="{{ asset('images/Infinecs-with-slogan.png') }}" alt="Infinecs" class="h-10" style="filter: brightness(0) invert(1);">
    </header>

    <!-- Main content -->
    <main class="flex-1 flex items-center justify-center px-4 py-8">
        <div class="result-card bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

            <!-- Top accent bar -->
            <div class="h-1.5 w-full" style="background: linear-gradient(to right, #7c3aed, #6366f1);"></div>

            <div class="p-8 text-center">

                <!-- Checkmark header -->
                <div class="flex items-center justify-center gap-2 mb-6">
                    <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h1 class="text-lg font-semibold text-gray-700">Assessment Completed</h1>
                </div>

                <!-- Assessment name -->
                <p class="text-sm text-gray-500 mb-6">
                    <span class="font-medium text-gray-700">{{ $eventCode }}</span>
                </p>

                <!-- Score ring -->
                <div class="relative inline-flex items-center justify-center mb-4">
                    <svg width="160" height="160" class="score-ring">
                        <!-- Track -->
                        <circle cx="80" cy="80" r="70"
                            fill="none" stroke="#f3f4f6" stroke-width="12"/>
                        <!-- Progress -->
                        <circle id="progress-ring" cx="80" cy="80" r="70"
                            fill="none"
                            stroke="{{ $ringColor }}"
                            stroke-width="12"
                            stroke-linecap="round"/>
                    </svg>
                    <!-- Score text in the middle -->
                    <div class="absolute text-center score-value">
                        <div class="text-3xl font-extrabold text-gray-900 leading-none">
                            <span id="score-display">{{ $score }}</span>
                        </div>
                        <div class="text-sm text-gray-400 mt-1">of {{ $total }}</div>
                        <div class="text-base font-bold mt-1" style="color: {{ $ringColor }};">
                            {{ number_format($percentage, 1) }}%
                        </div>
                    </div>
                </div>

                <!-- Grade badge -->
                <div class="grade-badge inline-block">
                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold border {{ $gradeColor }}">
                        @if($percentage == 100)
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endif
                        {{ $grade }}
                    </span>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-100 my-6"></div>

                <!-- Score breakdown -->
                <div class="grid grid-cols-3 gap-4 mb-6 text-center">
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $score }}</div>
                        <div class="text-xs text-gray-500 mt-1">Correct</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $total - $score }}</div>
                        <div class="text-xs text-gray-500 mt-1">Incorrect</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $total }}</div>
                        <div class="text-xs text-gray-500 mt-1">Total</div>
                    </div>
                </div>

                <!-- Progress bar -->
                <div class="mb-6">
                    <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                        <span>Score</span>
                        <span>{{ number_format($percentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                        <div id="progress-bar"
                            class="h-2.5 rounded-full transition-all duration-1000 ease-out"
                            style="width: 0%; background: {{ $ringColor }};">
                        </div>
                    </div>
                </div>

                <!-- Finish button -->
                <button onclick="finishQuiz()" type="button"
                    class="w-full py-3 px-6 bg-violet-600 hover:bg-violet-700 active:bg-violet-800 text-white font-semibold rounded-xl transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2">
                    Finish
                </button>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center pb-6 text-slate-400 text-xs">
        &copy; {{ date('Y') }} Infinecs Systems &mdash; E-Assessment Platform
    </footer>

</body>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var eventCode      = @json($eventCode);
    var participantEmail = @json(session('participant_email', 'guest'));
    var percentage     = {{ $percentage }};
    var circumference  = {{ $circumference }};
    var offset         = {{ $offset }};

    // Clear quiz finished flags so future attempts start fresh
    localStorage.removeItem(`quiz_finished_${eventCode}`);
    localStorage.removeItem(`quiz_finished_${eventCode}_${participantEmail}`);

    // Animate the score ring
    requestAnimationFrame(function() {
        setTimeout(function() {
            var ring = document.getElementById('progress-ring');
            if (ring) ring.style.strokeDashoffset = offset;

            var bar = document.getElementById('progress-bar');
            if (bar) bar.style.width = percentage.toFixed(1) + '%';
        }, 200);
    });

    // Confetti for perfect score
    if (percentage === 100) {
        launchConfetti();
    }
});

function finishQuiz() {
    window.close();
    // If still open after 300ms the tab wasn't a popup — redirect home
    setTimeout(function() {
        if (!document.hidden) {
            window.location.href = '/';
        }
    }, 300);
}

function launchConfetti() {
    var colors = ['#7c3aed','#6366f1','#22c55e','#eab308','#ef4444','#3b82f6'];
    for (var i = 0; i < 80; i++) {
        (function(i) {
            setTimeout(function() {
                var el = document.createElement('div');
                el.className = 'confetti-piece';
                el.style.left        = Math.random() * 100 + 'vw';
                el.style.top         = '-20px';
                el.style.background  = colors[Math.floor(Math.random() * colors.length)];
                el.style.width       = (6 + Math.random() * 8) + 'px';
                el.style.height      = (6 + Math.random() * 8) + 'px';
                el.style.borderRadius= Math.random() > 0.5 ? '50%' : '2px';
                el.style.animation   = `confettiFall ${1.5 + Math.random() * 2}s ease ${Math.random() * 0.5}s forwards`;
                document.body.appendChild(el);
                setTimeout(function() { el.remove(); }, 3500);
            }, i * 30);
        })(i);
    }
}
</script>
</html>
