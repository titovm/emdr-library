<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-xl font-semibold mb-6">{{ __('Visitor Statistics Dashboard') }}</h2>
                    
                    <!-- Stats Overview Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Total Visitors Card -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-500 bg-opacity-20">
                                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Visitors</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalVisitors }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Unique Visitors Card -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-500 bg-opacity-20">
                                    <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unique Visitors</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $uniqueVisitors }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Access Methods Breakdown -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-purple-500 bg-opacity-20">
                                    <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Primary Access Method</p>
                                    @if($accessMethods->count() > 0)
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                            {{ ucfirst($accessMethods->first()->access_method) }}
                                        </p>
                                    @else
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">N/A</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Two Column Layout for Charts and Tables -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-8">
                            <!-- Access Methods Breakdown Table -->
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Access Methods</h3>
                                </div>
                                <div class="p-6">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                        <thead>
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Method</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Count</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                            @forelse($accessMethods as $method)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($method->access_method) }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $method->count }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                        {{ number_format(($method->count / $totalVisitors) * 100, 1) }}%
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">No data available</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Most Popular Pages Table -->
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Most Popular Pages</h3>
                                </div>
                                <div class="p-6">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                        <thead>
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Page</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Visits</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                            @forelse($popularPages as $page)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $page->formatted_page }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $page->visits }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                        {{ number_format(($page->visits / $totalVisitors) * 100, 1) }}%
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">No data available</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Top Visitors Table -->
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Top Visitors</h3>
                                </div>
                                <div class="p-6">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                        <thead>
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Visits</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                            @forelse($topVisitors as $visitor)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $visitor->name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $visitor->email }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $visitor->visits }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">No data available</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="space-y-8">
                            <!-- Visitors Per Day Chart -->
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Daily Visitors (Last 30 Days)</h3>
                                </div>
                                <div class="p-6 h-64">
                                    @if($visitorsPerDay->count() > 0)
                                        <div id="chart-container">
                                            <canvas id="visitors-chart"></canvas>
                                        </div>
                                        
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const ctx = document.getElementById('visitors-chart').getContext('2d');
                                                
                                                const chart = new Chart(ctx, {
                                                    type: 'line',
                                                    data: {
                                                        labels: @json($chartData['dates']),
                                                        datasets: [{
                                                            label: 'Daily Visitors',
                                                            data: @json($chartData['counts']),
                                                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                                            borderColor: 'rgba(59, 130, 246, 1)',
                                                            borderWidth: 2,
                                                            tension: 0.3,
                                                            fill: true,
                                                            pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                                                            pointBorderColor: '#fff',
                                                            pointRadius: 4,
                                                            pointHoverRadius: 6
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false,
                                                        plugins: {
                                                            legend: {
                                                                display: false
                                                            },
                                                            tooltip: {
                                                                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                                                titleColor: '#fff',
                                                                bodyColor: '#fff',
                                                                titleFont: {
                                                                    size: 14,
                                                                    weight: 'bold'
                                                                },
                                                                bodyFont: {
                                                                    size: 13
                                                                },
                                                                padding: 12,
                                                                cornerRadius: 6,
                                                                displayColors: false
                                                            }
                                                        },
                                                        scales: {
                                                            x: {
                                                                grid: {
                                                                    display: false
                                                                },
                                                                ticks: {
                                                                    color: 'rgb(156, 163, 175)'
                                                                }
                                                            },
                                                            y: {
                                                                beginAtZero: true,
                                                                grid: {
                                                                    color: 'rgba(156, 163, 175, 0.1)'
                                                                },
                                                                ticks: {
                                                                    color: 'rgb(156, 163, 175)',
                                                                    precision: 0
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            });
                                        </script>
                                    @else
                                        <div class="h-full flex items-center justify-center">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">No visitor data available for the last 30 days</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Recent Visitors Table -->
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Visitors</h3>
                                </div>
                                <div class="p-6">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                        <thead>
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Page</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                            @forelse($recentVisitors as $visitor)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $visitor->name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $visitor->formatted_page }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $visitor->visited_at->diffForHumans() }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">No data available</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-app-layout>