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
        const form = document.getElementById('quiz-form');

        // Auto-save answer to server when user selects
        document.querySelectorAll('input[type=radio]').forEach(radio => {
            radio.addEventListener('change', function () {
                const questionId = this.name.match(/\d+/)[0];
                const value = this.value;

                fetch("{{ route('quiz.saveAnswer', $eventCode) }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ questionId, value })
                }).catch(() => console.error('Failed to auto-save'));
            });
        });

        // Submit validation
        form.addEventListener('submit', function (e) {
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
                e.preventDefault();
                firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
                alert('Please answer all questions before submitting.');
            } else {
                // Clear timer data and session marker on successful submission
                localStorage.removeItem(storageKey);
                localStorage.removeItem(storageKey + '_timestamp');
                sessionStorage.removeItem('quiz_started_{{ $eventCode }}');
                localStorage.removeItem(activeTabKey);
                
                // Clear server-side active session
                fetch("{{ route('quiz.clearActiveSession', $eventCode) }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    }
                }).catch(() => {});
            }
        });

        // Timer functionality
        let totalSeconds = 0;
        @foreach($questions as $q)
            totalSeconds += {{ $q->durationeachquestion ?? 60 }};
        @endforeach
        
        // Create unique storage key for this event
        const storageKey = 'quiz_timer_{{ $eventCode }}';
        const activeTabKey = 'quiz_active_tab_{{ $eventCode }}';
        const timeDisplay = document.getElementById('time-remaining');
        
        // Generate unique tab ID
        const currentTabId = 'tab_' + Date.now() + '_' + Math.random();
        
        // Check server-side for active session first
        fetch("{{ route('quiz.checkActiveSession', $eventCode) }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ tabId: currentTabId })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.allowed) {
                alert('Quiz is already active in another browser or device. Please close other sessions and try again.');
                window.location.href = '/';
                return;
            }
            
            // Server allows this session, now check local storage for same browser tabs
            const activeTabId = localStorage.getItem(activeTabKey);
            if (activeTabId && activeTabId !== currentTabId) {
                // Another tab is active in same browser - show warning and close this tab
                alert('Quiz is already open in another tab. Please use the existing tab to continue your assessment.');
                window.close();
                return;
            }
            
            // Mark this tab as active locally
            localStorage.setItem(activeTabKey, currentTabId);
            
            // Continue with quiz initialization
            initializeQuiz();
        })
        .catch(error => {
            console.error('Error checking active session:', error);
            // Fallback to local check only if server check fails
            const activeTabId = localStorage.getItem(activeTabKey);
            if (activeTabId && activeTabId !== currentTabId) {
                alert('Quiz is already open in another tab. Please close this tab to continue your assessment.');
                window.close();
                return;
            }
            localStorage.setItem(activeTabKey, currentTabId);
            initializeQuiz();
        });

        function initializeQuiz() {
        // Check if this is coming from registration (new entry) or just a page refresh
        const urlParams = new URLSearchParams(window.location.search);
        const fromRegistration = urlParams.has('new') || 
                                @if(session('new_session')) true @else false @endif || 
                                sessionStorage.getItem('quiz_started_{{ $eventCode }}') === null;
        
        // Check if there's a saved timer state
        const savedTime = localStorage.getItem(storageKey);
        const savedTimestamp = localStorage.getItem(storageKey + '_timestamp');
        
        if (fromRegistration) {
            // Coming from registration - always start fresh
            console.log('New quiz session from registration - starting fresh');
            
            // Clear any previous timer data
            localStorage.removeItem(storageKey);
            localStorage.removeItem(storageKey + '_timestamp');
            
            // Clear any pre-selected answers
            document.querySelectorAll('input[type=radio]:checked').forEach(radio => {
                radio.checked = false;
            });
            
            // Clear server-side saved answers
            fetch("{{ route('quiz.clearAnswers', $eventCode) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                }
            }).catch(() => console.log('Could not clear server answers'));
            
            // Mark that quiz has started for this event
            sessionStorage.setItem('quiz_started_{{ $eventCode }}', 'true');
            
        } else if (savedTime && savedTimestamp) {
            // Page refresh - restore timer and keep answers
            console.log('Page refresh detected - restoring timer and answers');
            
            const currentTime = Date.now();
            const elapsedSeconds = Math.floor((currentTime - parseInt(savedTimestamp)) / 1000);
            const calculatedTime = parseInt(savedTime) - elapsedSeconds;
            
            // Only restore if the calculated time is positive and reasonable
            if (calculatedTime > 0 && calculatedTime <= totalSeconds) {
                totalSeconds = calculatedTime;
            } else {
                // Timer expired - clear data and auto-submit
                localStorage.removeItem(storageKey);
                localStorage.removeItem(storageKey + '_timestamp');
                localStorage.removeItem(activeTabKey);
                alert('Time has expired! Your answers will be submitted automatically.');
                form.submit();
                return;
            }
        }
        
        // Save initial timer state
        localStorage.setItem(storageKey, totalSeconds);
        localStorage.setItem(storageKey + '_timestamp', Date.now());

        function updateTimer() {
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            timeDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (totalSeconds <= 0) {
                clearInterval(timerInterval);
                // Clear timer data and session marker when finished
                localStorage.removeItem(storageKey);
                localStorage.removeItem(storageKey + '_timestamp');
                sessionStorage.removeItem('quiz_started_{{ $eventCode }}');
                localStorage.removeItem(activeTabKey);
                
                // Clear server-side active session
                fetch("{{ route('quiz.clearActiveSession', $eventCode) }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    }
                }).catch(() => {});
                
                alert('Time is up! Your answers will be submitted automatically.');
                form.submit();
                return;
            }
            totalSeconds--;
            
            // Update stored timer state every second
            localStorage.setItem(storageKey, totalSeconds);
            localStorage.setItem(storageKey + '_timestamp', Date.now());
        }

        updateTimer();
        const timerInterval = setInterval(updateTimer, 1000);

        // Send heartbeat to server every 30 seconds to maintain active session
        setInterval(function() {
            fetch("{{ route('quiz.heartbeat', $eventCode) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ tabId: currentTabId })
            }).catch(() => {});
        }, 30000);
        
        } // End of initializeQuiz function

        // Handle tab close/refresh - auto submit quiz
        window.addEventListener('beforeunload', function (e) {
            // Clear the active tab marker locally
            localStorage.removeItem(activeTabKey);
            
            // Clear server-side active session
            navigator.sendBeacon("{{ route('quiz.clearActiveSession', $eventCode) }}", 
                new URLSearchParams({ '_token': "{{ csrf_token() }}" }));
            
            // Auto-submit the quiz when tab is closed
            const answeredQuestions = document.querySelectorAll('input[type=radio]:checked').length;
            if (answeredQuestions > 0) {
                // Use sendBeacon for reliable submission when page is unloading
                const formData = new FormData(form);
                
                // Try to use sendBeacon first (more reliable for page unload)
                const submitData = new URLSearchParams();
                for (let [key, value] of formData.entries()) {
                    submitData.append(key, value);
                }
                
                // Send the data
                if (navigator.sendBeacon) {
                    navigator.sendBeacon("{{ route('quiz.submit', ['eventCode' => $eventCode]) }}", submitData);
                } else {
                    // Fallback for older browsers
                    fetch("{{ route('quiz.submit', ['eventCode' => $eventCode]) }}", {
                        method: "POST",
                        body: submitData,
                        keepalive: true
                    }).catch(() => {});
                }
            }
        });

        // Monitor if this tab is still the active one (in case user opens multiple tabs)
        setInterval(function() {
            const currentActiveTab = localStorage.getItem(activeTabKey);
            if (currentActiveTab !== currentTabId) {
                // This tab is no longer active - redirect or close
                alert('Quiz has been opened in another tab. This tab will be closed.');
                window.close();
            }
        }, 2000); // Check every 2 seconds

        // Disable right-click and copy
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('copy', e => e.preventDefault());
        document.addEventListener('cut', e => e.preventDefault());
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
                        <label class="block mb-2">
                            <input type="radio" name="answers[{{ $q->QuestionID }}]" value="{{ $optionLetter }}"
                                @if (isset($savedAnswers[$q->QuestionID]) && $savedAnswers[$q->QuestionID] == $optionLetter) checked @endif>
                            {{ $optionLetter }}. {{ $ans->AnswerText }}
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
