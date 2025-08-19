<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-md w-full text-center">
        <h1 class="text-3xl font-bold text-violet-700 mb-4">Assessment Completed</h1>
        <p class="text-lg text-gray-700 mb-6">
            Well done! You have completed <strong>{{ $eventCode }}</strong>.
        </p>
        <div class="text-4xl font-extrabold text-green-600 mb-6">
            {{ $result['score'] }} / {{ $result['total'] }}
        </div>
        @php
            $percentage = ($result['score'] / $result['total']) * 100;
        @endphp
        <p class="mb-6">
            @php
                $percentClass = 'text-gray-600';
                if ($percentage == 100) {
                    $percentClass = 'text-green-600 font-extrabold';
                } elseif ($percentage >= 75) {
                    $percentClass = 'text-blue-600 font-bold';
                } elseif ($percentage >= 50) {
                    $percentClass = 'text-yellow-600 font-bold';
                } else {
                    $percentClass = 'text-red-600 font-bold';
                }
            @endphp
            <span class="text-3xl {{ $percentClass }}">{{ number_format($percentage, 2) }}%</span>
        </p>
        <a href="{{ url('/') }}"
           class="inline-block px-6 py-2 bg-violet-600 text-white rounded hover:bg-violet-700">
           Finish
        </a>
    </div>
</body>
</html>
