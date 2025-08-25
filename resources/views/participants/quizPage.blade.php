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
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
            100% { transform: translateX(0); }
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: #fff;
            padding: 2rem;
            border-radius: 1rem;
            max-width: 500px;
            width: 90vw;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            text-align: center;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .btn-primary {
            background: #7c3aed;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            margin: 0.25rem;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background: #6d28d9;
        }

        .btn-secondary {
            background: #64748b;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            margin: 0.25rem;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: #475569;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            margin: 0.25rem;
            transition: all 0.2s;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #7c3aed;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .session-status {
            position: fixed;
            top: 70px;
            right: 1rem;
            background: #10b981;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            z-index: 50;
            display: none;
        }

        .session-status.warning {
            background: #f59e0b;
        }

        .session-status.error {
            background: #dc2626;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div class="max-w-4xl mx-auto p-6">
        <!-- Timer -->
        <div id="timer" class="fixed top-4 right-4 bg-red-600 text-white px-4 py-2 rounded shadow font-bold text-lg z-50">
            Time Remaining: <span id="time-remaining">Loading...</span>
        </div>

        <!-- Session Status Indicator -->
        <div id="session-status" class="session-status">
            <span id="session-status-text">Session Active</span>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900">
                Event {{ $eventCode }} – Assessment
            </h2>
            <p class="mt-2 text-gray-600">Please answer all the questions below.</p>
        </div>

        <!-- Quiz Form -->
        <form id="quiz-form" action="{{ route('quiz.submit', ['eventCode' => $eventCode]) }}" method="POST" class="space-y-6">
            @csrf
            
            @forelse($questions as $index => $q)
                <div class="question-block border rounded-lg p-6 bg-white shadow" data-question-id="{{ $q->QuestionID }}">
                    <!-- Question Image -->
                    @if (!empty($q->QuestionImage))
                        <div class="mb-4">
                            <img src="{{ asset($q->QuestionImage) }}" alt="Question Image" class="max-h-48 rounded shadow mx-auto">
                        </div>
                    @endif
                    
                    <!-- Question Text -->
                    <div class="mb-4 flex items-center">
                        <h3 class="font-semibold text-lg text-gray-800">
                            Q{{ $index + 1 }}. {{ $q->QuestionText }}
                        </h3>
                        <span class="error-star">*</span>
                    </div>

                    <!-- Answer Options -->
                    @php
                        $answers = \App\Models\AssessmentAnswer::where('QuestionID', $q->QuestionID)->get();
                    @endphp

                    @foreach ($answers as $key => $ans)
                        @php $optionLetter = chr(65 + $key); @endphp
                        <label class="block mb-3 flex items-center gap-3 p-2 rounded hover:bg-gray-50 cursor-pointer">
                            <input type="radio" 
                                   name="answers[{{ $q->QuestionID }}]" 
                                   value="{{ $optionLetter }}"
                                   class="w-4 h-4"
                                   @if (isset($savedAnswers[$q->QuestionID]) && $savedAnswers[$q->QuestionID] == $optionLetter) checked @endif>
                            
                            @if (!empty($ans->AnswerImage))
                                <img src="{{ asset($ans->AnswerImage) }}" alt="Answer Image" class="max-h-16 rounded border">
                            @endif
                            
                            <span class="text-gray-700">{{ $optionLetter }}. {{ $ans->AnswerText }}</span>
                        </label>
                    @endforeach
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-gray-700 text-lg">No questions found for this event.</p>
                    <a href="/" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Go Back
                    </a>
                </div>
            @endforelse

            @if ($questions->count())
                <div class="text-right pt-6">
                    <button type="submit" 
                            id="submit-btn"
                            class="px-8 py-3 bg-violet-600 text-white font-semibold rounded-lg hover:bg-violet-700 focus:ring-4 focus:ring-violet-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Submit Answers
                    </button>
                </div>
            @endif
        </form>
    </div>

    <script>
        // Global variables
        let isSubmitting = false;
        let isQuizActive = false;
        let timerInterval;
        let heartbeatInterval;
        let sessionCheckInterval;
        
        // Quiz configuration from server
        const QUIZ_CONFIG = {
            eventCode: '{{ $eventCode }}',
            totalQuestions: {{ $questions->count() }},
            totalSeconds: {{ $questions->count() * (int)$assessment->DurationEachQuestion }},
            participantEmail: '{{ session("participant_email", "guest") }}',
            csrfToken: '{{ csrf_token() }}',
            isNewSession: {{ $isNewSession ? 'true' : 'false' }}
        };
        
        // Storage keys
        const STORAGE_KEYS = {
            timer: `quiz_timer_${QUIZ_CONFIG.eventCode}_${QUIZ_CONFIG.participantEmail}`,
            answers: `quiz_answers_${QUIZ_CONFIG.eventCode}_${QUIZ_CONFIG.participantEmail}`,
            finished: `quiz_finished_${QUIZ_CONFIG.eventCode}_${QUIZ_CONFIG.participantEmail}`,
            tabId: `quiz_tab_${QUIZ_CONFIG.eventCode}_${QUIZ_CONFIG.participantEmail}`
        };
        
        // Use or generate unique tab ID (persist for this tab)
        let currentTabId = sessionStorage.getItem(STORAGE_KEYS.tabId);
        if (!currentTabId) {
            currentTabId = 'tab_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem(STORAGE_KEYS.tabId, currentTabId);
        }
        let totalSeconds = QUIZ_CONFIG.totalSeconds;

        document.addEventListener('DOMContentLoaded', function() {
            initializeQuiz();
        });

        async function initializeQuiz() {
            try {
                console.log('🚀 Initializing quiz...');
                updateSessionStatus('Connecting...', 'warning');
                
                // Check if quiz is already finished
                if (localStorage.getItem(STORAGE_KEYS.finished) === '1') {
                    console.log('✅ Quiz already finished, redirecting to results');
                    window.location.href = `/quiz/${QUIZ_CONFIG.eventCode}/results`;
                    return;
                }

                // Check server-side session with better error handling
                const sessionResult = await checkServerSession();
                if (!sessionResult.success) {
                    if (sessionResult.action === 'show_takeover_option') {
                        await showTakeoverDialog(sessionResult.message);
                        return;
                    } else {
                        handleSessionError(sessionResult);
                        return;
                    }
                }

                // Initialize session and timer
                await initializeSession();

                // Setup event handlers
                setupEventHandlers();
                
                // Start monitoring
                startHeartbeat();
                startSessionCheck();
                
                isQuizActive = true;
                updateSessionStatus('Quiz Active', 'success');
                console.log('✅ Quiz initialized successfully');
                
            } catch (error) {
                console.error('❌ Error initializing quiz:', error);
                showErrorModal('Initialization Error', 'Failed to start quiz. Please refresh the page and try again.');
            }
        }

        async function checkServerSession(forceNew = false) {
            try {
                const response = await fetch(`/quiz/${QUIZ_CONFIG.eventCode}/check-active-session`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': QUIZ_CONFIG.csrfToken
                    },
                    body: JSON.stringify({ 
                        tabId: currentTabId,
                        force_new: forceNew
                    })
                });

                const data = await response.json();
                
                return {
                    success: data.allowed,
                    message: data.message,
                    action: data.action || 'none',
                    sessionAge: data.existing_session_age || 0
                };
            } catch (error) {
                console.error('Server session check failed:', error);
                return {
                    success: false,
                    message: 'Unable to connect to server. Please check your internet connection.',
                    action: 'retry'
                };
            }
        }

        async function showTakeoverDialog(message) {
            const result = await showConfirmModal(
                'Session Conflict',
                message + '\n\nWhat would you like to do?',
                [
                    { text: 'Take Over Session', value: 'takeover', class: 'btn-primary' },
                    { text: 'Cancel', value: 'cancel', class: 'btn-danger' }
                ]
            );

            switch (result) {
                case 'takeover':
                    await takeoverSession();
                    break;
                default:
                    window.location.href = '/';
                    break;
            }
        }

        async function takeoverSession() {
            try {
                updateSessionStatus('Taking over session...', 'warning');
                
                const response = await fetch(`/quiz/${QUIZ_CONFIG.eventCode}/takeover-session`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': QUIZ_CONFIG.csrfToken
                    },
                    body: JSON.stringify({ tabId: currentTabId })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Continue with current session
                    await initializeSession();
                    setupEventHandlers();
                    startHeartbeat();
                    startSessionCheck();
                    isQuizActive = true;
                    updateSessionStatus('Session Taken Over', 'success');
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Takeover failed:', error);
                showErrorModal('Takeover Failed', 'Could not take over the existing session. Please try again.');
            }
        }

        async function startNewSession() {
            try {
                updateSessionStatus('Starting new session...', 'warning');
                
                // Clear local storage
                Object.values(STORAGE_KEYS).forEach(key => localStorage.removeItem(key));
                
                // Request new session from server
                const sessionResult = await checkServerSession(true);
                if (sessionResult.success) {
                    window.location.reload();
                } else {
                    throw new Error(sessionResult.message);
                }
            } catch (error) {
                console.error('New session failed:', error);
                showErrorModal('New Session Failed', 'Could not start a new session. Please refresh the page.');
            }
        }

        function handleSessionError(result) {
            switch (result.action) {
                case 'redirect_login':
                    showErrorModal('Session Expired', 'Please log in again to continue.', () => {
                        window.location.href = '/login';
                    });
                    break;
                case 'refresh':
                    showErrorModal('Invalid Session', 'Please refresh the page to continue.', () => {
                        window.location.reload();
                    });
                    break;
                case 'retry':
                    showErrorModal('Connection Error', result.message + '\n\nPlease check your internet connection and try again.', () => {
                        window.location.reload();
                    });
                    break;
                default:
                    showErrorModal('Session Error', result.message);
                    break;
            }
        }

        async function initializeSession() {
            const savedTime = localStorage.getItem(STORAGE_KEYS.timer);
            const savedTimestamp = localStorage.getItem(STORAGE_KEYS.timer + '_timestamp');

            if (QUIZ_CONFIG.isNewSession || !savedTime) {
                console.log('🆕 Starting fresh quiz session');
                // Clear any existing data
                Object.values(STORAGE_KEYS).forEach(key => {
                    if (key !== STORAGE_KEYS.finished) {
                        localStorage.removeItem(key);
                        localStorage.removeItem(key + '_timestamp');
                    }
                });
                
                totalSeconds = QUIZ_CONFIG.totalSeconds;
                localStorage.setItem(STORAGE_KEYS.timer, totalSeconds);
                localStorage.setItem(STORAGE_KEYS.timer + '_timestamp', Date.now());
                
                // Clear server answers
                await clearServerAnswers();
                
            } else if (savedTimestamp) {
                console.log('🔄 Restoring quiz session from refresh');
                
                const elapsedSeconds = Math.floor((Date.now() - parseInt(savedTimestamp)) / 1000);
                const calculatedTime = parseInt(savedTime) - elapsedSeconds;

                if (calculatedTime > 0 && calculatedTime <= QUIZ_CONFIG.totalSeconds) {
                    totalSeconds = calculatedTime;
                    restoreAnswers();
                    console.log(`⏰ Timer restored: ${totalSeconds} seconds remaining`);
                } else {
                    console.log('⏰ Timer expired during refresh');
                    await handleTimeUp();
                    return;
                }
            } else {
                // Invalid state - start fresh
                console.log('🆘 Invalid session state - starting fresh');
                totalSeconds = QUIZ_CONFIG.totalSeconds;
                localStorage.setItem(STORAGE_KEYS.timer, totalSeconds);
                localStorage.setItem(STORAGE_KEYS.timer + '_timestamp', Date.now());
            }

            // Store tab ID
            localStorage.setItem(STORAGE_KEYS.tabId, currentTabId);
            
            startTimer();
        }

        function startTimer() {
            const timeDisplay = document.getElementById('time-remaining');
            
            function updateTimer() {
                if (totalSeconds <= 0) {
                    clearInterval(timerInterval);
                    handleTimeUp();
                    return;
                }

                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;
                timeDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                totalSeconds--;

                // Save every 10 seconds to reduce localStorage writes
                if (totalSeconds % 10 === 0) {
                    localStorage.setItem(STORAGE_KEYS.timer, totalSeconds);
                    localStorage.setItem(STORAGE_KEYS.timer + '_timestamp', Date.now());
                }
            }

            updateTimer();
            timerInterval = setInterval(updateTimer, 1000);
        }

        async function handleTimeUp() {
            if (isSubmitting) return;
            isSubmitting = true;
            
            console.log('⏰ Time expired - auto-submitting');
            localStorage.setItem(STORAGE_KEYS.finished, '1');
            clearQuizData();
            clearIntervals();
            updateSessionStatus('Time Expired', 'error');
            
            showModal('Time\'s Up!', 'Your time has expired. Your answers will be submitted automatically.', [], false);
            
            try {
                const form = document.getElementById('quiz-form');
                const formData = new FormData(form);
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    window.location.href = `/quiz/${QUIZ_CONFIG.eventCode}/results`;
                } else {
                    throw new Error('Server error');
                }
            } catch (error) {
                console.error('Error auto-submitting:', error);
                document.getElementById('quiz-form').submit();
            }
        }

        function setupEventHandlers() {
            const form = document.getElementById('quiz-form');
            
            // Auto-save answers when radio buttons change
            document.querySelectorAll('input[type=radio]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const questionId = this.name.match(/\d+/)[0];
                    const value = this.value;
                    
                    saveAnswerLocally(questionId, value);
                    saveAnswerToServer(questionId, value);
                });
            });

            // Form submission handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                validateAndSubmit();
            });

            // Page unload handler with better UX
            window.addEventListener('beforeunload', handlePageUnload);

            // Visibility change handler
            document.addEventListener('visibilitychange', function() {
                if (document.hidden && isQuizActive && !isSubmitting) {
                    console.log('⚠️ Tab switched or minimized');
                }
            });

            // Anti-cheating measures
            setupAntiCheating();
        }

        function setupAntiCheating() {
            document.addEventListener('contextmenu', e => e.preventDefault());
            document.addEventListener('copy', e => e.preventDefault());
            document.addEventListener('cut', e => e.preventDefault());
            document.addEventListener('paste', e => e.preventDefault());
            document.addEventListener('selectstart', e => e.preventDefault());
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'F12' || 
                    (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                    (e.ctrlKey && e.key === 'u') ||
                    (e.ctrlKey && e.shiftKey && e.key === 'C')) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        async function validateAndSubmit() {
            if (isSubmitting) return;

            document.querySelectorAll('.error-star').forEach(star => star.style.display = 'none');
            document.querySelectorAll('.question-block').forEach(block => block.classList.remove('error-highlight'));

            let valid = true;
            let firstUnanswered = null;

            document.querySelectorAll('.question-block').forEach(block => {
                const questionId = block.dataset.questionId;
                const selected = block.querySelector(`input[name="answers[${questionId}]"]:checked`);
                
                if (!selected) {
                    valid = false;
                    const star = block.querySelector('.error-star');
                    if (star) star.style.display = 'inline';
                    block.classList.add('error-highlight');
                    if (!firstUnanswered) firstUnanswered = block;
                }
            });

            if (!valid) {
                if (firstUnanswered) {
                    firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                showErrorModal('Incomplete Quiz', 'Please answer all questions before submitting.');
                return;
            }

            const confirmed = await showConfirmModal(
                'Submit Quiz',
                'Are you sure you want to submit your answers? This cannot be undone.',
                [
                    { text: 'Submit', value: true, class: 'btn-primary' },
                    { text: 'Cancel', value: false, class: 'btn-secondary' }
                ]
            );

            if (confirmed) {
                await submitQuiz();
            }
        }

        async function submitQuiz() {
            if (isSubmitting) return;
            isSubmitting = true;

            try {
                const submitBtn = document.getElementById('submit-btn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Submitting...';
                }

                localStorage.setItem(STORAGE_KEYS.finished, '1');
                clearQuizData();
                clearIntervals();
                updateSessionStatus('Submitting...', 'warning');

                await clearServerSession();
                document.getElementById('quiz-form').submit();
                
            } catch (error) {
                console.error('Error submitting quiz:', error);
                isSubmitting = false;
                
                const submitBtn = document.getElementById('submit-btn');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Answers';
                }
                
                showErrorModal('Submission Error', 'Error submitting quiz. Please try again.');
            }
        }

        function saveAnswerLocally(questionId, value) {
            try {
                let answers = JSON.parse(localStorage.getItem(STORAGE_KEYS.answers) || '{}');
                answers[questionId] = value;
                localStorage.setItem(STORAGE_KEYS.answers, JSON.stringify(answers));
            } catch (error) {
                console.error('Error saving answer locally:', error);
            }
        }

        async function saveAnswerToServer(questionId, value) {
            try {
                await fetch(`/quiz/${QUIZ_CONFIG.eventCode}/save-answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': QUIZ_CONFIG.csrfToken
                    },
                    body: JSON.stringify({ questionId, value })
                });
            } catch (error) {
                console.log('Failed to save answer to server:', error);
            }
        }

        function restoreAnswers() {
            try {
                const answers = JSON.parse(localStorage.getItem(STORAGE_KEYS.answers) || '{}');
                Object.entries(answers).forEach(([questionId, value]) => {
                    const radio = document.querySelector(`input[name="answers[${questionId}]"][value="${value}"]`);
                    if (radio) radio.checked = true;
                });
                console.log(`📋 Restored ${Object.keys(answers).length} saved answers`);
            } catch (error) {
                console.error('Error restoring answers:', error);
            }
        }

        function startHeartbeat() {
            heartbeatInterval = setInterval(async function() {
                if (!isQuizActive || isSubmitting) return;

                try {
                    const response = await fetch(`/quiz/${QUIZ_CONFIG.eventCode}/heartbeat`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': QUIZ_CONFIG.csrfToken
                        },
                        body: JSON.stringify({ tabId: currentTabId })
                    });

                    const data = await response.json();
                    
                    if (!data.active) {
                        handleSessionLost(data.reason);
                    } else {
                        updateSessionStatus('Quiz Active', 'success');
                    }
                } catch (error) {
                    console.log('Heartbeat failed:', error);
                    updateSessionStatus('Connection Issues', 'warning');
                }
            }, 15000);
        }

        function startSessionCheck() {
            sessionCheckInterval = setInterval(function() {
                if (!isQuizActive || isSubmitting) return;

                const storedTabId = localStorage.getItem(STORAGE_KEYS.tabId);
                if (storedTabId && storedTabId !== currentTabId) {
                    handleMultipleTabsDetected();
                }
            }, 5000);
        }

        function handleSessionLost(reason) {
            clearIntervals();
            isQuizActive = false;
            updateSessionStatus('Session Lost', 'error');
            
            let message = 'Your quiz session has been interrupted.';
            
            switch (reason) {
                case 'session_taken_over':
                    message = 'Your quiz session has been taken over by another tab or device.';
                    break;
                case 'no_session':
                    message = 'Your login session has expired.';
                    break;
                case 'server_error':
                    message = 'Server connection lost.';
                    break;
            }
            
            showErrorModal('Session Lost', message, () => {
                window.location.href = '/';
            });
        }

        function handleMultipleTabsDetected() {
            clearIntervals();
            isQuizActive = false;
            updateSessionStatus('Multiple Tabs Detected', 'error');
            
            showErrorModal(
                'Multiple Tabs Detected', 
                'The quiz has been opened in another tab. Please use only one tab for the quiz.',
                () => window.close()
            );
        }

        function handlePageUnload(e) {
            if (isSubmitting || !isQuizActive) return;

            const form = document.getElementById('quiz-form');
            const formData = new FormData(form);
            
            // Use sendBeacon for reliable background submission
            navigator.sendBeacon(`/quiz/${QUIZ_CONFIG.eventCode}/auto-submit`, formData);

            // This message is not guaranteed to be shown in modern browsers
            e.preventDefault();
            e.returnValue = 'Your answers will be submitted automatically. Are you sure you want to leave?';
            return e.returnValue;
        }

        async function clearServerSession() {
            try {
                await fetch(`/quiz/${QUIZ_CONFIG.eventCode}/clear-active-session`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': QUIZ_CONFIG.csrfToken
                    }
                });
            } catch (error) {
                console.error('Error clearing server session:', error);
            }
        }

        async function clearServerAnswers() {
            try {
                await fetch(`/quiz/${QUIZ_CONFIG.eventCode}/clear-answers`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': QUIZ_CONFIG.csrfToken
                    }
                });
            } catch (error) {
                console.error('Error clearing server answers:', error);
            }
        }

        function clearQuizData() {
            Object.values(STORAGE_KEYS).forEach(key => {
                if (key !== STORAGE_KEYS.finished) {
                    localStorage.removeItem(key);
                    localStorage.removeItem(key + '_timestamp');
                }
            });
        }

        function clearIntervals() {
            if (timerInterval) clearInterval(timerInterval);
            if (heartbeatInterval) clearInterval(heartbeatInterval);
            if (sessionCheckInterval) clearInterval(sessionCheckInterval);
        }

        function updateSessionStatus(text, type = 'success') {
            const statusEl = document.getElementById('session-status');
            const statusText = document.getElementById('session-status-text');
            
            if (statusEl && statusText) {
                statusText.textContent = text;
                statusEl.className = `session-status ${type}`;
                statusEl.style.display = 'block';
                
                // Auto-hide success messages after 3 seconds
                if (type === 'success') {
                    setTimeout(() => {
                        statusEl.style.display = 'none';
                    }, 3000);
                }
            }
        }

        // Modal functions
        function showErrorModal(title, message, callback = null) {
            showModal(title, message, [
                { text: 'OK', value: 'ok', class: 'btn-primary' }
            ], true, callback);
        }

        function showConfirmModal(title, message, buttons) {
            return showModal(title, message, buttons, true);
        }

        function showModal(title, message, buttons = [], closable = true, callback = null) {
            return new Promise((resolve) => {
                // Remove existing modal
                const existingModal = document.getElementById('quiz-modal');
                if (existingModal) existingModal.remove();

                const modal = document.createElement('div');
                modal.id = 'quiz-modal';
                modal.className = 'modal-overlay';
                
                const buttonsHtml = buttons.map(btn => 
                    `<button onclick="resolveModal('${btn.value}')" class="${btn.class}">${btn.text}</button>`
                ).join('');

                modal.innerHTML = `
                    <div class="modal-content">
                        <h2 style="font-size: 1.5rem; font-weight: bold; color: #374151; margin-bottom: 1rem;">
                            ${title}
                        </h2>
                        <p style="margin-bottom: 1.5rem; color: #6b7280; white-space: pre-line;">
                            ${message}
                        </p>
                        <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                            ${buttonsHtml}
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modal);

                // Global function to resolve modal
                window.resolveModal = function(value) {
                    modal.remove();
                    delete window.resolveModal;
                    if (callback && typeof callback === 'function') {
                        callback(value);
                    }
                    resolve(value);
                };

                // Close on overlay click if closable
                if (closable) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            window.resolveModal(false);
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
