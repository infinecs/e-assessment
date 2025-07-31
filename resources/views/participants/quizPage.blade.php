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
            }
        });

        // Disable right-click and copy
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('copy', e => e.preventDefault());
        document.addEventListener('cut', e => e.preventDefault());
    });
</script>

</head>

<body class="bg-gray-100 font-sans">
    <div class="max-w-4xl mx-auto p-6">
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
