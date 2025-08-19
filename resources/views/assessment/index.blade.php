@extends('layout.appMain')
@section('content')
<div class="grid grid-cols-1 pb-6">
    <!-- Stat Widgets Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
        <div class="card border-2 border-violet-600 dark:border-violet-500 dark:bg-zinc-800 dark:border-zinc-600">
            <div class="card-body flex flex-col items-center justify-center">
                <div class="text-3xl font-bold text-violet-600 dark:text-violet-400">{{ isset($totalAssessments) ? $totalAssessments : '-' }}</div>
                <div class="text-gray-600 dark:text-gray-100 mt-2">Total Assessments</div>
            </div>
        </div>
        <div class="card border-2 border-violet-600 dark:border-violet-500 dark:bg-zinc-800 dark:border-zinc-600">
            <div class="card-body flex flex-col items-center justify-center">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ isset($totalParticipants) ? $totalParticipants : '-' }}</div>
                <div class="text-gray-600 dark:text-gray-100 mt-2">Total Participants</div>
            </div>
        </div>
        <div class="card border-2 border-violet-600 dark:border-violet-500 dark:bg-zinc-800 dark:border-zinc-600">
            <div class="card-body flex flex-col items-center justify-center">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ isset($passRate) ? $passRate : '-' }}%</div>
                <div class="text-gray-600 dark:text-gray-100 mt-2">Pass Rate</div>
            </div>
        </div>
        <div class="card border-2 border-violet-600 dark:border-violet-500 dark:bg-zinc-800 dark:border-zinc-600">
            <div class="card-body flex flex-col items-center justify-center">
                <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ isset($averageScore) ? $averageScore : '-' }}</div>
                <div class="text-gray-600 dark:text-gray-100 mt-2">Average Score</div>
            </div>
        </div>
    </div>
    <div class="md:flex items-center justify-between px-[2px]">
        <h4 class="text-[18px] font-medium text-gray-800 mb-sm-0 grow dark:text-gray-100 mb-2 md:mb-0">Assessment Dashboard</h4>
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 ltr:md:space-x-3 rtl:md:space-x-0">
                <li class="inline-flex items-center">
                    <a href="#"
                        class="inline-flex items-center text-sm text-gray-800 hover:text-gray-900 dark:text-zinc-100 dark:hover:text-white">
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center rtl:mr-2">
                        <i
                            class="font-semibold text-gray-600 align-middle far fa-angle-right text-13 rtl:rotate-180 dark:text-zinc-100"></i>
                        <a href="#"
                            class="text-sm font-medium text-gray-500 ltr:ml-2 rtl:mr-2 hover:text-gray-900 ltr:md:ml-2 rtl:md:mr-2 dark:text-gray-100 dark:hover:text-white">Assessment
                            Dashboard</a>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Charts Row: Donut and Bar Side by Side -->
<!-- Top Performers Table -->
<div class="card mb-6 border-2 border-violet-600 dark:border-violet-500 dark:bg-zinc-800 dark:border-zinc-600">
    <div class="card-body border-b border-gray-100 dark:border-zinc-600">
        <h6 class="mb-1 text-gray-600 text-15 dark:text-gray-100">Top Performers</h6>
    </div>
    <div class="card-body overflow-x-auto">
        <table class="min-w-full text-sm text-center text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-gray-100">
                <tr>
                    <th class="px-3 py-2">No.</th>
                    <th class="px-3 py-2">Name</th>
                    <th class="px-3 py-2">Email</th>
                    <th class="px-3 py-2">Assessment Name</th>
                    <th class="px-3 py-2">Score</th>
                    <th class="px-3 py-2">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sorted = collect($topPerformers ?? [])->filter(function($p) {
                        return isset($p->TotalScore) && isset($p->TotalQuestion) && $p->TotalQuestion > 0;
                    })->sortByDesc(function($p) {
                        return $p->TotalScore / $p->TotalQuestion;
                    })->values();
                @endphp
                @for($i = 0; $i < 5; $i++)
                    @php $performer = isset($sorted[$i]) ? $sorted[$i] : null; @endphp
                    @if($performer)
                    <tr>
                        <td class="px-3 py-2">{{ $i+1 }}</td>
                        <td class="px-3 py-2">{{ isset($performer->name) ? $performer->name : '-' }}</td>
                        <td class="px-3 py-2">{{ isset($performer->email) ? $performer->email : '-' }}</td>
                        <td class="px-3 py-2">{{ isset($performer->assessment_name) ? $performer->assessment_name : '-' }}</td>
                        <td class="px-3 py-2">{{ isset($performer->TotalScore) && isset($performer->TotalQuestion) ? $performer->TotalScore . ' / ' . $performer->TotalQuestion : '-' }}</td>
                        <td class="px-3 py-2">
                            @if(isset($performer->TotalQuestion) && $performer->TotalQuestion > 0 && isset($performer->TotalScore))
                                {{ number_format(($performer->TotalScore / $performer->TotalQuestion) * 100, 2) }}%
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endif
                @endfor
                @if($sorted->count() == 0)
                    <tr><td colspan="6" class="px-3 py-2 text-center">No data available</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
<div class="grid grid-cols-1 xl:grid-cols-2 gap-x-6 mt-6">
    <div>
        <div class="card dark:bg-zinc-800 dark:border-zinc-600">
            <div class="card-body border-b border-gray-100 dark:border-zinc-600">
                <h6 class="mb-1 text-gray-600 text-15 dark:text-gray-100">Results Distribution</h6>
            </div>
            <div class="flex flex-wrap gap-3 card-body">
                <div id="results-donut-chart" data-colors='["#5156be", "#ffbf53", "#fd625e", "#4ba6ef", "#2ab57d"]' class="e-charts" style="height:350px;"></div>
            </div>
        </div>
    </div>
    <div>
        <div class="card dark:bg-zinc-800 dark:border-zinc-600">
            <div class="card-body border-b border-gray-100 dark:border-zinc-600">
                <h6 class="mb-1 text-gray-600 text-15 dark:text-gray-100">Assessment Participation</h6>
            </div>
            <div class="flex flex-wrap gap-3 card-body">
                <div id="events-bar-chart" style="height:350px;"></div>
                @if(empty($eventChartLabels) || count($eventChartLabels) === 0)
                    <div class="w-full text-center text-gray-500 py-8">No assessment participation data available.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Echarts JS (already included in Minia) -->
<!-- Use Echarts CDN for reliability -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<style>
    #results-donut-chart {
        min-height: 350px;
        width: 100%;
        background: #fff;
        border-radius: 8px;
        border: 2px solid #5156be;
        box-shadow: 0 2px 8px rgba(81,86,190,0.08);
    }
    #events-bar-chart {
        min-height: 350px;
        width: 100%;
        background: #f8fafc;
        border-radius: 8px;
        border: 2px solid #5156be;
        box-shadow: 0 2px 8px rgba(81,86,190,0.08);
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var chartDom = document.getElementById('results-donut-chart');
    if (chartDom) {
        var myChart = echarts.init(chartDom);
        var option = {
            tooltip: {
                trigger: 'item'
            },
            legend: {
                top: '5%',
                left: 'center'
            },
            series: [
                {
                    name: 'Results',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    avoidLabelOverlap: false,
                    itemStyle: {
                        borderRadius: 10,
                        borderColor: '#fff',
                        borderWidth: 2
                    },
                    label: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: '18',
                            fontWeight: 'bold'
                        }
                    },
                    labelLine: {
                        show: false
                    },
                    color: ['#2ab57d', '#fd625e'], // Passed: green, Failed: red
                    data: [
                        { value: {{ $passed }}, name: 'Passed' },
                        { value: {{ $failed }}, name: 'Failed' }
                    ]
                }
            ]
        };
        myChart.setOption(option);
    }

    // Event Participation Pie Chart
    var chartDom = document.getElementById('events-bar-chart');
    if (chartDom && {!! json_encode($eventChartLabels ?? []) !!}.length > 0) {
        var myChart = echarts.init(chartDom);
        var pieColors = ['#5156be', '#ffbf53', '#fd625e', '#4ba6ef', '#2ab57d', '#8e44ad', '#e67e22', '#16a085'];
        var pieData = [];
        var labels = {!! json_encode($eventChartLabels ?? []) !!};
        var counts = {!! json_encode($eventChartCounts ?? []) !!};
        for (var i = 0; i < labels.length; i++) {
            pieData.push({ value: counts[i], name: labels[i] });
        }
        var option = {
            tooltip: {
                trigger: 'item'
            },
            legend: {
                top: '5%',
                left: 'center'
            },
            series: [
                {
                    name: 'Assessments',
                    type: 'pie',
                    radius: '70%',
                    avoidLabelOverlap: false,
                    itemStyle: {
                        borderRadius: 10,
                        borderColor: '#fff',
                        borderWidth: 2
                    },
                    label: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: '18',
                            fontWeight: 'bold'
                        }
                    },
                    labelLine: {
                        show: false
                    },
                    color: pieColors,
                    data: pieData
                }
            ]
        };
        myChart.setOption(option);
    }
});
</script>
@endsection
