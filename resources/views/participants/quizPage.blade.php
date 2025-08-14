<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .error-star {
            color: red;
            margin-left: 5px;
            font-weight: bold;
            display: none;
        }

        .error-highlight {
            border: 2px solid red !important;
            background-color: #ffe6e6 !important;
            animation: shake 0.3s ease-in-out;
        }

        @keyframes shake {
            0% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            50% {
                transform: translateX(5px);
            }

            75% {
                transform: translateX(-5px);
            }

            100% {
                transform: translateX(0);
            }
        }
    </style>
   <script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Fix: Clear quiz data if participant email has changed ---
        const currentEmail = "{{ session('participant_email', 'guest') }}";
        const emailKey = 'quiz_last_email_{{ $eventCode }}';
        const lastEmail = localStorage.getItem(emailKey);
        if (lastEmail && lastEmail !== currentEmail) {
            // Remove all quiz-related keys for this event
            const keysToRemove = [
                'quiz_timer_{{ $eventCode }}_' + lastEmail,
                'quiz_active_tab_{{ $eventCode }}_' + lastEmail,
                'quiz_active_tab_{{ $eventCode }}_' + lastEmail + '_timestamp',
                'quiz_answers_{{ $eventCode }}_' + lastEmail,
                'quiz_session_{{ $eventCode }}_' + lastEmail
            ];
            keysToRemove.forEach(k => localStorage.removeItem(k));
        }
        // Always update to current email
        localStorage.setItem(emailKey, currentEmail);
        const form = document.getElementById('quiz-form');
        
        // Timer functionality - calculate total time
        let totalSeconds = 0;
        @foreach($questions as $q)
            totalSeconds += {{ $q->durationeachquestion ?? 60 }};
        @endforeach
        
        // Create unique storage keys for this event and participant
        const storageKey = 'quiz_timer_{{ $eventCode }}_{{ session("participant_email", "guest") }}';
        const activeTabKey = 'quiz_active_tab_{{ $eventCode }}_{{ session("participant_email", "guest") }}';
        const answersKey = 'quiz_answers_{{ $eventCode }}_{{ session("participant_email", "guest") }}';
        const sessionKey = 'quiz_session_{{ $eventCode }}_{{ session("participant_email", "guest") }}';
        const timeDisplay = document.getElementById('time-remaining');
        
        // Generate unique tab ID with more entropy
        const currentTabId = 'tab_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        
        // Global variables
        let timerInterval;
        let heartbeatInterval;
        let tabCheckInterval;
        let isQuizActive = false;
        let isSubmitting = false;

        // Initialize quiz with proper session management
        async function initializeQuiz() {
            try {
                // First, check server-side session
                const sessionCheck = await fetch("{{ route('quiz.checkActiveSession', $eventCode) }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ tabId: currentTabId })
                });
                
                const sessionData = await sessionCheck.json();
                
                if (!sessionData.allowed) {
                    alert('Quiz is already active in another browser or device. Please close other sessions and try again.');
                    window.location.href = '/';
                    return;
                }
                
                // Check local storage for multiple tabs in same browser
                const existingTabId = localStorage.getItem(activeTabKey);
                if (existingTabId && existingTabId !== currentTabId) {
                    // Check if the other tab is still alive by using a timestamp
                    const tabTimestamp = localStorage.getItem(activeTabKey + '_timestamp');
                    const now = Date.now();
                    
                    if (tabTimestamp && (now - parseInt(tabTimestamp)) < 10000) { // 10 seconds
                        alert('Quiz is already open in another tab. Please use the existing tab or close it to continue.');
                        setTimeout(() => window.close(), 2000);
                        return;
                    }
                }
                
                // Mark this tab as active
                localStorage.setItem(activeTabKey, currentTabId);
                localStorage.setItem(activeTabKey + '_timestamp', Date.now());
                
                // Check if this is a fresh start or page refresh
                const quizSession = localStorage.getItem(sessionKey);
                const savedTime = localStorage.getItem(storageKey);
                const savedTimestamp = localStorage.getItem(storageKey + '_timestamp');
                
                // Determine if this is a new session or refresh
                const isNewSession = !quizSession || 
                                   ({{ session('new_session') ? 'true' : 'false' }}) ||
                                   (new URLSearchParams(window.location.search).has('new'));
                
                if (isNewSession && !quizSession) {
                    console.log('🆕 Starting fresh quiz session');
                    
                    // Clear any existing data
                    clearQuizData();
                    
                    // Start fresh timer
                    localStorage.setItem(storageKey, totalSeconds);
                    localStorage.setItem(storageKey + '_timestamp', Date.now());
                    localStorage.setItem(sessionKey, 'active');
                    
                    // Clear server-side answers for fresh start
                    await fetch("{{ route('quiz.clearAnswers', $eventCode) }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json"
                        }
                    }).catch(() => {});
                    
                } else if (savedTime && savedTimestamp) {
                    console.log('🔄 Restoring quiz session from refresh');
                    
                    // Calculate elapsed time during refresh
                    const currentTime = Date.now();
                    const elapsedSeconds = Math.floor((currentTime - parseInt(savedTimestamp)) / 1000);
                    const calculatedTime = parseInt(savedTime) - elapsedSeconds;
                    
                    if (calculatedTime > 0 && calculatedTime <= totalSeconds) {
                        totalSeconds = calculatedTime;
                        console.log(`⏰ Timer restored: ${totalSeconds} seconds remaining`);
                    } else {
                        console.log('⏰ Timer expired during refresh');
                        await handleTimeUp();
                        return;
                    }
                    
                    // Restore saved answers from localStorage
                    restoreAnswers();
                } else {
                    console.log('🆘 Invalid session state - starting fresh');
                    clearQuizData();
                    totalSeconds = {{ array_sum(array_column($questions->toArray(), 'durationeachquestion')) ?: ($questions->count() * 60) }};
                    localStorage.setItem(storageKey, totalSeconds);
                    localStorage.setItem(storageKey + '_timestamp', Date.now());
                    localStorage.setItem(sessionKey, 'active');
                }
                
                // Start the quiz
                startQuizTimer();
                setupEventHandlers();
                startHeartbeat();
                startTabMonitoring();
                isQuizActive = true;
                
                console.log('✅ Quiz initialized successfully');
                
            } catch (error) {
                console.error('❌ Error initializing quiz:', error);
                alert('Error starting quiz. Please refresh the page.');
            }
        }
        
        // Clear all quiz-related data
        function clearQuizData() {
            localStorage.removeItem(storageKey);
            localStorage.removeItem(storageKey + '_timestamp');
            localStorage.removeItem(answersKey);
            localStorage.removeItem(sessionKey);
        }
        
        // Start the quiz timer
        function startQuizTimer() {
            function updateTimer() {
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;
                timeDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                if (totalSeconds <= 0) {
                    clearInterval(timerInterval);
                    handleTimeUp();
                    return;
                }
                
                totalSeconds--;
                
                // Save timer state every 5 seconds (reduce localStorage writes)
                if (totalSeconds % 5 === 0) {
                    localStorage.setItem(storageKey, totalSeconds);
                    localStorage.setItem(storageKey + '_timestamp', Date.now());
                }
            }
            
            updateTimer(); // Initial call
            timerInterval = setInterval(updateTimer, 1000);
        }
        
        // Handle time up
        async function handleTimeUp() {
            if (isSubmitting) return;
            isSubmitting = true;
            
            console.log('⏰ Time expired - auto-submitting');
            
            clearQuizData();
            clearIntervals();
            
            alert('Time is up! Your answers will be submitted automatically.');
            
            // Auto-submit the form
            try {
                await submitQuiz(true);
            } catch (error) {
                console.error('Error auto-submitting:', error);
                form.submit(); // Fallback to regular form submission
            }
        }
        
        // Setup event handlers
        function setupEventHandlers() {
            // Auto-save answers
            document.querySelectorAll('input[type=radio]').forEach(radio => {
                radio.addEventListener('change', function () {
                    const questionId = this.name.match(/\d+/)[0];
                    const value = this.value;
                    
                    // Save to localStorage immediately
                    saveAnswerLocally(questionId, value);
                    
                    // Save to server
                    fetch("{{ route('quiz.saveAnswer', $eventCode) }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ questionId, value })
                    }).catch(() => console.log('Failed to save answer to server'));
                });
            });
            
            // Form submission
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                validateAndSubmit();
            });
            
            // Page unload handler
            window.addEventListener('beforeunload', function (e) {
                if (!isSubmitting && isQuizActive) {
                    // Clear active tab marker
                    localStorage.removeItem(activeTabKey);
                    localStorage.removeItem(activeTabKey + '_timestamp');
                    
                    // Clear server session
                    navigator.sendBeacon("{{ route('quiz.clearActiveSession', $eventCode) }}", 
                        new URLSearchParams({ '_token': "{{ csrf_token() }}" }));
                    
                    // Auto-submit if there are answers
                    const answeredQuestions = document.querySelectorAll('input[type=radio]:checked').length;
                    if (answeredQuestions > 0) {
                        const formData = new FormData(form);
                        const submitData = new URLSearchParams();
                        for (let [key, value] of formData.entries()) {
                            submitData.append(key, value);
                        }
                        
                        navigator.sendBeacon("{{ route('quiz.submit', ['eventCode' => $eventCode]) }}", submitData);
                    }
                }
            });
        }
        
        // Save answer to localStorage
        function saveAnswerLocally(questionId, value) {
            let answers = {};
            try {
                answers = JSON.parse(localStorage.getItem(answersKey) || '{}');
            } catch (e) {
                answers = {};
            }
            answers[questionId] = value;
            localStorage.setItem(answersKey, JSON.stringify(answers));
        }
        
        // Restore answers from localStorage
        function restoreAnswers() {
            try {
                const answers = JSON.parse(localStorage.getItem(answersKey) || '{}');
                Object.entries(answers).forEach(([questionId, value]) => {
                    const radio = document.querySelector(`input[name="answers[${questionId}]"][value="${value}"]`);
                    if (radio) {
                        radio.checked = true;
                    }
                });
                console.log(`📋 Restored ${Object.keys(answers).length} saved answers`);
            } catch (e) {
                console.log('Failed to restore answers from localStorage');
            }
        }
        
        // Validate and submit quiz
        async function validateAndSubmit() {
            if (isSubmitting) return;
            
            let valid = true;
            document.querySelectorAll('.error-star').forEach(star => star.style.display = 'none');
            document.querySelectorAll('.question-block').forEach(block => block.classList.remove('error-highlight'));

            const questionBlocks = document.querySelectorAll('.question-block');
            let firstUnanswered = null;

            questionBlocks.forEach(block => {
                const questionId = block.dataset.questionId;
                const selected = block.querySelector(`input[name="answers[${questionId}]"]:checked`);
                const star = block.querySelector('.error-star');

                if (!selected) {
                    valid = false;
                    star.style.display = 'inline';
                    block.classList.add('error-highlight');
                    if (!firstUnanswered) {
                        firstUnanswered = block;
                    }
                }
            });

            if (!valid) {
                firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
                alert('Please answer all questions before submitting.');
                return;
            }

            await submitQuiz(false);
        }
        
        // Submit quiz
        async function submitQuiz(isAutoSubmit) {
            if (isSubmitting) return;
            isSubmitting = true;
            
            try {
                clearQuizData();
                clearIntervals();
                
                // Clear server session
                await fetch("{{ route('quiz.clearActiveSession', $eventCode) }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    }
                }).catch(() => {});
                
                form.submit();
            } catch (error) {
                console.error('Error submitting quiz:', error);
                isSubmitting = false;
            }
        }
        
        // Start heartbeat to server
        function startHeartbeat() {
            heartbeatInterval = setInterval(async function() {
                if (!isQuizActive) return;
                
                try {
                    const response = await fetch("{{ route('quiz.heartbeat', $eventCode) }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ tabId: currentTabId })
                    });
                    
                    const data = await response.json();
                    if (!data.active) {
                        alert('Your quiz session has expired or been taken over by another device. Please restart.');
                        window.location.href = '/';
                    }
                } catch (error) {
                    console.log('Heartbeat failed:', error);
                }
            }, 15000); // Every 15 seconds
        }
        
        // Start tab monitoring
        function startTabMonitoring() {
            tabCheckInterval = setInterval(function() {
                if (!isQuizActive) return;
                
                // Update timestamp to show this tab is alive
                localStorage.setItem(activeTabKey + '_timestamp', Date.now());
                
                // Check if another tab took over
                const currentActiveTab = localStorage.getItem(activeTabKey);
                if (currentActiveTab && currentActiveTab !== currentTabId) {
                    const tabTimestamp = localStorage.getItem(activeTabKey + '_timestamp');
                    const now = Date.now();
                    
                    // If other tab is recent (within 10 seconds), close this one
                    if (tabTimestamp && (now - parseInt(tabTimestamp)) < 10000) {
                        clearIntervals();
                        alert('Quiz has been opened in another tab. This tab will be closed.');
                        window.close();
                    }
                }
            }, 3000); // Every 3 seconds
        }
        
        // Clear all intervals
        function clearIntervals() {
            if (timerInterval) clearInterval(timerInterval);
            if (heartbeatInterval) clearInterval(heartbeatInterval);
            if (tabCheckInterval) clearInterval(tabCheckInterval);
        }

        // Anti-cheating measures
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('copy', e => e.preventDefault());
        document.addEventListener('cut', e => e.preventDefault());
        document.addEventListener('paste', e => e.preventDefault());
        document.addEventListener('selectstart', e => e.preventDefault());
        
        // Detect tab switching and full-screen exit
        document.addEventListener('visibilitychange', function() {
            if (document.hidden && isQuizActive) {
                console.log('⚠️  Tab switched or minimized');
                // You can add additional anti-cheating measures here
            }
        });
        
        // Initialize the quiz
        initializeQuiz();
    });
</script>

</head>

<body class="bg-gray-100 font-sans">
    <div class="max-w-4xl mx-auto p-6">
        

<div id="timer" 
     class="fixed top-4 right-4 bg-red-600 text-white px-4 py-2 rounded shadow font-bold text-lg z-50">
    Time Remaining: <span id="time-remaining"></span>
</div>

        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900">
                Event {{ $eventCode }} – Assessment
            </h2>
            <p class="mt-2 text-gray-600">Please answer all the questions below.</p>
        </div>

        <form id="quiz-form" action="{{ route('quiz.submit', ['eventCode' => $eventCode]) }}" method="POST" class="space-y-6">


            @csrf
            @forelse($questions as $index => $q)
                <div class="question-block border rounded-lg p-6 bg-white shadow"
                    data-question-id="{{ $q->QuestionID }}">

                    @if (!empty($q->QuestionImage))
                        <div class="mb-2">
                            <img src="{{ asset($q->QuestionImage) }}" alt="Question Image" class="max-h-48 rounded shadow mx-auto mb-2">
                            <div class="text-xs text-gray-400 break-all">Path: {{ $q->QuestionImage }}</div>
                        </div>
                    @endif
                    <div class="mb-4 flex items-center">
                        <h3 class="font-semibold text-lg text-gray-800">
                            Q{{ $index + 1 }}. {{ $q->QuestionText }}
                        </h3>
                        <span class="error-star">*</span>
                    </div>

                    @php
                        $answers = \App\Models\AssessmentAnswer::where('QuestionID', $q->QuestionID)->get();
                    @endphp

                    @foreach ($answers as $key => $ans)
                        @php $optionLetter = chr(65+$key); @endphp
                        <label class="block mb-2 flex items-center gap-2">
                            <input type="radio" name="answers[{{ $q->QuestionID }}]" value="{{ $optionLetter }}"
                                @if (isset($savedAnswers[$q->QuestionID]) && $savedAnswers[$q->QuestionID] == $optionLetter) checked @endif>
                            @if (!empty($ans->AnswerImage))
                                <img src="{{ asset($ans->AnswerImage) }}" alt="Answer Image" class="max-h-20 ml-2 rounded border">
                            @endif
                            <span>{{ $optionLetter }}. {{ $ans->AnswerText }}</span>
                        </label>
                    @endforeach
                </div>
    
@empty
    <p class="text-gray-700">No questions found for this event.</p>
    @endforelse

    @if ($questions->count())
        <div class="text-right">
            <button type="submit"
                class="px-6 py-2 bg-violet-600 text-white font-semibold rounded hover:bg-violet-700 focus:ring-4 focus:ring-violet-300">
                Submit Answers
            </button>
        </div>
    @endif
    </form>
    </div>
    
</body>

</html>
