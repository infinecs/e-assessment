<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="{{ asset('images/logos/Infinecs-Logo-Square.ico') }}">
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
            <p class="mt-2 text-gray-600">Please answer all the questions below. Right click disabled.</p>
        </div>

        <!-- Quiz Form -->
        <form id="quiz-form" action="{{ route('quiz.submit', ['eventCode' => $eventCode]) }}" method="POST" class="space-y-6">
            @csrf
            
           @forelse($questions as $index => $q)
    <div class="question-block border rounded-lg p-6 bg-white shadow" data-question-id="{{ $q->QuestionID }}">
        
        <!-- Debug info (remove this after testing) -->
        <div class="mb-2 text-xs text-gray-500" style="display: none;">
            DEBUG: QuestionImage = "{{ $q->QuestionImage }}" | Length: {{ strlen($q->QuestionImage ?? '') }}
        </div>
        
        <!-- Question Image - Improved condition -->
        @if (isset($q->QuestionImage) && !empty(trim($q->QuestionImage)))
            <div class="mb-4">
                <img src="{{ asset(trim($q->QuestionImage)) }}" 
                     alt="Question {{ $index + 1 }} Image" 
                     class="max-h-48 rounded shadow mx-auto block"
                     onerror="this.style.display='none'; console.log('Image failed to load: {{ trim($q->QuestionImage) }}');">
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
                
                <!-- Answer Image - Also improved -->
                @if (isset($ans->AnswerImage) && !empty(trim($ans->AnswerImage)))
                    <img src="{{ asset(trim($ans->AnswerImage)) }}" 
                         alt="Answer {{ $optionLetter }} Image" 
                         class="max-h-16 rounded border"
                         onerror="this.style.display='none'; console.log('Answer image failed: {{ trim($ans->AnswerImage) }}');">
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
    // ---------------- GLOBAL VARIABLES ----------------
    let isSubmitting = false;
    let isQuizActive = false;
    let timerInterval;
    let heartbeatInterval;
    let sessionCheckInterval;
    let totalSeconds = 0;

    // ---------------- QUIZ CONFIGURATION ----------------
    const QUIZ_CONFIG = {
        eventCode: '{{ $eventCode }}',
        totalQuestions: {{ $questions ? $questions->count() : 0 }},
        totalSeconds: {{ $questions->count() * (int)$assessment->DurationEachQuestion }},
        participantEmail: '{{ session("participant_email", "guest") }}',
        csrfToken: '{{ csrf_token() }}',
        isNewSession: {{ $isNewSession ? 'true' : 'false' }}
    };

    // ---------------- STORAGE KEYS ----------------
    const STORAGE_KEYS = {
        timer: `quiz_timer_${QUIZ_CONFIG.eventCode}_${QUIZ_CONFIG.participantEmail}`,
        answers: `quiz_answers_${QUIZ_CONFIG.eventCode}_${QUIZ_CONFIG.participantEmail}`,
        finished: `quiz_finished_${QUIZ_CONFIG.eventCode}_${QUIZ_CONFIG.participantEmail}`,
        tabId: `quiz_tab_${QUIZ_CONFIG.eventCode}_${QUIZ_CONFIG.participantEmail}`
    };

    // ---------------- TAB ID ----------------
    let currentTabId = sessionStorage.getItem(STORAGE_KEYS.tabId);
    if (!currentTabId) {
        currentTabId = 'tab_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        sessionStorage.setItem(STORAGE_KEYS.tabId, currentTabId);
    }

    // ---------------- PAGE LOAD ----------------
    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('quiz_auto_submitted')) {
            localStorage.removeItem('quiz_auto_submitted');
            clearQuizData();
            window.location.href = `/quiz/${QUIZ_CONFIG.eventCode}/results`;
            return;
        }
        initializeQuiz();
    });

    // ---------------- INITIALIZE QUIZ ----------------
    async function initializeQuiz() {
        try {
            updateSessionStatus('Connecting...', 'warning');

            if (localStorage.getItem(STORAGE_KEYS.finished) === '1') {
                window.location.href = `/quiz/${QUIZ_CONFIG.eventCode}/results`;
                return;
            }

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

            await initializeSession();
            setupEventHandlers();
            startHeartbeat();
            startSessionCheck();
            isQuizActive = true;
            updateSessionStatus('Quiz Active', 'success');
        } catch (error) {
            console.error('Error initializing quiz:', error);
            showErrorModal('Initialization Error', 'Failed to start quiz. Please refresh the page.');
        }
    }

    // ---------------- SERVER SESSION CHECK ----------------
    async function checkServerSession(forceNew = false) {
        try {
            const response = await fetch(`/quiz/${QUIZ_CONFIG.eventCode}/check-active-session`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': QUIZ_CONFIG.csrfToken
                },
                body: JSON.stringify({ tabId: currentTabId, force_new: forceNew })
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
            return { success: false, message: 'Unable to connect to server.', action: 'retry' };
        }
    }

    // ---------------- TAKEOVER ----------------
    async function showTakeoverDialog(message) {
        const result = await showConfirmModal('Session Conflict', message + '\n\nWhat would you like to do?', [
            { text: 'Take Over Session', value: 'takeover', class: 'btn-primary' },
            { text: 'Cancel', value: 'cancel', class: 'btn-danger' }
        ]);
        if (result === 'takeover') {
            await takeoverSession();
        } else {
            window.location.href = '/';
        }
    }

    async function takeoverSession() {
        try {
            updateSessionStatus('Taking over session...', 'warning');
            const response = await fetch(`/quiz/${QUIZ_CONFIG.eventCode}/takeover-session`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': QUIZ_CONFIG.csrfToken },
                body: JSON.stringify({ tabId: currentTabId })
            });
            const data = await response.json();
            if (data.success) {
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

    // ---------------- INITIALIZE SESSION & TIMER ----------------
    async function initializeSession() {
        const savedTime = localStorage.getItem(STORAGE_KEYS.timer);
        const savedTimestamp = localStorage.getItem(STORAGE_KEYS.timer + '_timestamp');

        if (QUIZ_CONFIG.isNewSession || !savedTime) {
            totalSeconds = QUIZ_CONFIG.totalSeconds;
            localStorage.setItem(STORAGE_KEYS.timer, totalSeconds);
            localStorage.setItem(STORAGE_KEYS.timer + '_timestamp', Date.now());
            await clearServerAnswers();
        } else if (savedTimestamp) {
            const elapsed = Math.floor((Date.now() - parseInt(savedTimestamp)) / 1000);
            const calculatedTime = parseInt(savedTime) - elapsed;
            totalSeconds = calculatedTime > 0 ? calculatedTime : 0;
            if (totalSeconds <= 0) {
                await handleTimeUp();
                return;
            }
            restoreAnswers();
        } else {
            totalSeconds = QUIZ_CONFIG.totalSeconds;
            localStorage.setItem(STORAGE_KEYS.timer, totalSeconds);
            localStorage.setItem(STORAGE_KEYS.timer + '_timestamp', Date.now());
        }

        localStorage.setItem(STORAGE_KEYS.tabId, currentTabId);
        startTimer();
    }

    function startTimer() {
        const timeDisplay = document.getElementById('time-remaining');
        if (!timeDisplay) return;

        function renderTimer() {
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            timeDisplay.textContent = `${minutes}:${seconds.toString().padStart(2,'0')}`;
        }

        renderTimer();
        timerInterval = setInterval(() => {
            if (totalSeconds <= 0) {
                clearInterval(timerInterval);
                handleTimeUp();
                return;
            }
            totalSeconds--;
            renderTimer();

            if (totalSeconds % 10 === 0) {
                localStorage.setItem(STORAGE_KEYS.timer, totalSeconds);
                localStorage.setItem(STORAGE_KEYS.timer + '_timestamp', Date.now());
            }
        }, 1000);
    }

    // ---------------- TIME UP & AUTO SUBMIT ----------------
    async function handleTimeUp() {
        if (isSubmitting || localStorage.getItem(STORAGE_KEYS.finished) === '1') return;
        isSubmitting = true;
        localStorage.setItem(STORAGE_KEYS.finished, '1');
        clearIntervals();
        isQuizActive = false;
        updateSessionStatus('Time Expired - Auto Submitting', 'error');

        showModal("Time's Up!", "Your time has expired. Submitting answers automatically...", [], false);

        try {
            const form = document.getElementById('quiz-form');
            const formData = new FormData(form);

            const response = await fetch(`/quiz/${QUIZ_CONFIG.eventCode}/auto-submit`, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': QUIZ_CONFIG.csrfToken }
            });

            const result = await response.json();
            if (result.status === 'submitted' || result.status === 'already_submitted') {
                clearQuizData();
                setTimeout(() => {
                    window.location.href = `/quiz/${QUIZ_CONFIG.eventCode}/results`;
                }, 2000);
            } else throw new Error(result.message || 'Auto-submit failed');

        } catch (error) {
            console.error('Auto-submit error:', error);
            setTimeout(() => {
                window.location.href = `/quiz/${QUIZ_CONFIG.eventCode}/results`;
            }, 2000);
        }
    }

    // ---------------- EVENT HANDLERS ----------------
    function setupEventHandlers() {
        const form = document.getElementById('quiz-form');
        document.querySelectorAll('input[type=radio]').forEach(radio => {
            radio.addEventListener('change', function() {
                const questionId = this.name.match(/\d+/)[0];
                const value = this.value;
                saveAnswerLocally(questionId, value);
                saveAnswerToServer(questionId, value);
            });
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            validateAndSubmit();
        });

        window.addEventListener('beforeunload', handlePageUnload);
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && isQuizActive && !isSubmitting) console.log('Tab hidden or minimized');
        });

        setupAntiCheating();
    }

    function setupAntiCheating() {
        ['contextmenu','copy','cut','paste','selectstart'].forEach(evt =>
            document.addEventListener(evt, e => e.preventDefault())
        );
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                (e.ctrlKey && e.key === 'u') || (e.ctrlKey && e.shiftKey && e.key === 'C')) e.preventDefault();
        });
    }

    async function validateAndSubmit() {
        if (isSubmitting) return;

        let valid = true;
        let firstUnanswered = null;
        document.querySelectorAll('.question-block').forEach(block => {
            const questionId = block.dataset.questionId;
            const selected = block.querySelector(`input[name="answers[${questionId}]"]:checked`);
            if (!selected) {
                valid = false;
                block.classList.add('error-highlight');
                if (!firstUnanswered) firstUnanswered = block;
            } else block.classList.remove('error-highlight');
        });

        if (!valid) {
            if (firstUnanswered) firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
            showErrorModal('Incomplete Quiz', 'Please answer all questions before submitting.');
            return;
        }

        await submitQuiz();
    }

    async function submitQuiz() {
        if (isSubmitting) return;
        isSubmitting = true;

        try {
            localStorage.setItem(STORAGE_KEYS.finished, '1');
            clearQuizData();
            clearIntervals();
            updateSessionStatus('Submitting...', 'warning');

            await clearServerSession();
            document.getElementById('quiz-form').submit();
        } catch (error) {
            console.error('Error submitting quiz:', error);
            isSubmitting = false;
        }
    }

    // ---------------- SAVE/RESTORE ANSWERS ----------------
    function saveAnswerLocally(questionId, value) {
        try {
            let answers = JSON.parse(localStorage.getItem(STORAGE_KEYS.answers) || '{}');
            answers[questionId] = value;
            localStorage.setItem(STORAGE_KEYS.answers, JSON.stringify(answers));
        } catch (error) { console.error(error); }
    }

    async function saveAnswerToServer(questionId, value) {
        try {
            await fetch(`/quiz/${QUIZ_CONFIG.eventCode}/save-answer`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': QUIZ_CONFIG.csrfToken },
                body: JSON.stringify({ questionId, value })
            });
        } catch (error) { console.log('Failed to save answer to server:', error); }
    }

    function restoreAnswers() {
        try {
            const answers = JSON.parse(localStorage.getItem(STORAGE_KEYS.answers) || '{}');
            Object.entries(answers).forEach(([q,v]) => {
                const radio = document.querySelector(`input[name="answers[${q}]"][value="${v}"]`);
                if (radio) radio.checked = true;
            });
        } catch (error) { console.error(error); }
    }

    // ---------------- HEARTBEAT & SESSION CHECK ----------------
    function startHeartbeat() {
        heartbeatInterval = setInterval(async () => {
            if (!isQuizActive || isSubmitting) return;
            try {
                const response = await fetch(`/quiz/${QUIZ_CONFIG.eventCode}/heartbeat`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': QUIZ_CONFIG.csrfToken },
                    body: JSON.stringify({ tabId: currentTabId })
                });
                const data = await response.json();
                if (!data.active) handleSessionLost(data.reason);
            } catch (error) { updateSessionStatus('Connection Issues', 'warning'); }
        }, 15000);
    }

    function startSessionCheck() {
        sessionCheckInterval = setInterval(() => {
            if (!isQuizActive || isSubmitting) return;
            const storedTabId = localStorage.getItem(STORAGE_KEYS.tabId);
            if (storedTabId && storedTabId !== currentTabId) handleMultipleTabsDetected();
        }, 5000);
    }

    function handleSessionLost(reason) {
        clearIntervals(); isQuizActive=false; updateSessionStatus('Session Lost','error');
        let msg = 'Your quiz session has been interrupted.';
        if (reason==='session_taken_over') msg='Your quiz session has been taken over by another tab/device.';
        else if (reason==='no_session') msg='Your login session has expired.';
        else if (reason==='server_error') msg='Server connection lost.';
        showErrorModal('Session Lost', msg, ()=>window.location.href='/');
    }

    function handleMultipleTabsDetected() {
        clearIntervals(); isQuizActive=false;
        showErrorModal('Multiple Tabs Detected','The quiz has been opened in another tab. Please use only one tab.',()=>window.close());
    }

    function handlePageUnload(e) {
        if (isSubmitting || !isQuizActive) return;
        const formData = new FormData(document.getElementById('quiz-form'));
        navigator.sendBeacon(`/quiz/${QUIZ_CONFIG.eventCode}/auto-submit`, formData);
        e.preventDefault(); e.returnValue='Your answers will be submitted automatically. Are you sure?'; return e.returnValue;
    }

    // ---------------- SERVER CLEAR FUNCTIONS ----------------
    async function clearServerSession() {
        try { await fetch(`/quiz/${QUIZ_CONFIG.eventCode}/clear-active-session`, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':QUIZ_CONFIG.csrfToken} }); }
        catch (error) { console.error(error); }
    }

    async function clearServerAnswers() {
        try { await fetch(`/quiz/${QUIZ_CONFIG.eventCode}/clear-answers`, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':QUIZ_CONFIG.csrfToken} }); }
        catch (error) { console.error(error); }
    }

    function clearQuizData() {
        Object.values(STORAGE_KEYS).forEach(key => { if(key!==STORAGE_KEYS.finished){ localStorage.removeItem(key); localStorage.removeItem(key+'_timestamp'); } });
    }

    function clearIntervals() {
        [timerInterval,heartbeatInterval,sessionCheckInterval].forEach(i=>{if(i) clearInterval(i);});
    }

    // ---------------- UI MODALS ----------------
    function updateSessionStatus(text,type='success'){
        const statusEl=document.getElementById('session-status');
        const statusText=document.getElementById('session-status-text');
        if(statusEl && statusText){
            statusText.textContent=text; statusEl.className=`session-status ${type}`; statusEl.style.display='block';
            if(type==='success'){setTimeout(()=>{statusEl.style.display='none';},3000);}
        }
    }

    function showErrorModal(title,message,callback=null){ showModal(title,message,[{text:'OK',value:'ok',class:'btn-primary'}],true,callback); }
    function showConfirmModal(title,message,buttons){ return showModal(title,message,buttons,true); }
    function showModal(title,message,buttons=[],closable=true,callback=null){
        return new Promise(resolve=>{
            const existing=document.getElementById('quiz-modal'); if(existing) existing.remove();
            const modal=document.createElement('div'); modal.id='quiz-modal'; modal.className='modal-overlay';
            modal.innerHTML=`
                <div class="modal-content">
                    <h2>${title}</h2>
                    <p style="white-space: pre-line;">${message}</p>
                    <div style="display:flex;gap:0.5rem;justify-content:center;flex-wrap:wrap;">
                        ${buttons.map(btn=>`<button onclick="resolveModal('${btn.value}')" class="${btn.class}">${btn.text}</button>`).join('')}
                    </div>
                </div>`;
            document.body.appendChild(modal);
            window.resolveModal=function(value){ modal.remove(); delete window.resolveModal; if(callback)callback(value); resolve(value); };
            if(closable){ modal.addEventListener('click',e=>{ if(e.target===modal) window.resolveModal(false); }); }
        });
    }
</script>

</body>
</html>
