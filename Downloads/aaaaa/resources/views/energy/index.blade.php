<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy Monitor Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        .animate-pulse-slow {
            animation: pulse 2s ease-in-out infinite;
        }
        .animate-slide-in {
            animation: slideIn 0.5s ease-out forwards;
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .gradient-text {
            background: linear-gradient(45deg, #60A5FA, #34D399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        /* Mobile-specific styles */
        @media (max-width: 768px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
            .mobile-menu {
                display: none;
                position: fixed;
                top: 60px;
                left: 0;
                right: 0;
                background: rgba(31, 41, 55, 0.95);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(75, 85, 99, 0.5);
                padding: 1rem;
                z-index: 40;
            }
            .mobile-menu.active {
                display: block;
            }
            .mobile-menu-item {
                display: block;
                padding: 0.75rem 1rem;
                color: white;
                text-decoration: none;
                border-radius: 0.5rem;
                margin-bottom: 0.5rem;
                transition: all 0.3s ease;
            }
            .mobile-menu-item:hover {
                background: rgba(255, 255, 255, 0.1);
            }
            .mobile-menu-item.active {
                background: rgba(59, 130, 246, 0.2);
                color: #60A5FA;
            }
            .location-info {
                display: none;
            }
            .tab-buttons {
                display: none;
            }
            .chart-container {
                min-height: 300px;
            }
            .mobile-menu-button {
                display: block;
            }
        }
        @media (min-width: 769px) {
            .mobile-menu {
                display: none;
            }
            .mobile-menu-button {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 text-white min-h-screen">
    <!-- Navigation -->
    <nav class="bg-gray-800 bg-opacity-50 backdrop-blur-lg border-b border-gray-700 fixed w-full z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between nav-container">
                <div class="flex items-center">
                    <i class="fas fa-solar-panel text-yellow-400 text-xl md:text-2xl mr-2 md:mr-3 animate-float"></i>
                    <span class="text-lg md:text-xl font-bold gradient-text">EnergyMonitor Pro</span>
                </div>
                <button class="mobile-menu-button text-white p-2 hover:bg-gray-700 rounded-lg transition-colors" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center space-x-2 md:space-x-4 location-info">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-map-marker-alt text-blue-500"></i>
                        <span class="text-sm md:text-base text-gray-400">Location: <span class="text-white">{{ $locationName }}</span></span>
                    </div>
                    <button onclick="toggleLocationModal()" class="bg-blue-600 hover:bg-blue-700 px-3 py-1.5 md:px-4 md:py-2 rounded-lg transition duration-300 hover:shadow-lg hover:shadow-blue-500/20 text-sm md:text-base">
                        <i class="fas fa-location-dot mr-1 md:mr-2"></i>Change Location
                    </button>
                    <a href="/about" class="text-gray-400 hover:text-white transition-colors text-sm md:text-base">About Us</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <div class="flex items-center space-x-2 mb-4">
            <i class="fas fa-map-marker-alt text-blue-500"></i>
            <span class="text-gray-400">Location: <span class="text-white">{{ $locationName }}</span></span>
        </div>
        <button onclick="toggleLocationModal()" class="mobile-menu-item w-full text-left">
            <i class="fas fa-location-dot mr-2"></i>Change Location
        </button>
        <button onclick="showTab('current')" class="mobile-menu-item w-full text-left" id="mobileCurrentTab">
            <i class="fas fa-chart-line mr-2"></i>Current Data
        </button>
        <button onclick="showTab('forecast')" class="mobile-menu-item w-full text-left" id="mobileForecastTab">
            <i class="fas fa-calendar-alt mr-2"></i>7-Day Forecast
        </button>
        <button onclick="showTab('history')" class="mobile-menu-item w-full text-left" id="mobileHistoryTab">
            <i class="fas fa-history mr-2"></i>Historical Data
        </button>
        <a href="/about" class="mobile-menu-item w-full text-left">
            <i class="fas fa-info-circle mr-2"></i>About Us
        </a>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-6 pt-20 md:pt-24">
        <!-- Desktop Tabs -->
        <div class="mb-4 md:mb-6 border-b border-gray-700 tab-buttons">
            <div class="flex space-x-2 md:space-x-4">
                <button onclick="showTab('current')" id="currentTab" class="py-2 px-3 md:px-4 border-b-2 border-blue-500 font-medium transition-all duration-300 hover:text-blue-400 text-sm md:text-base tab-button">Current Data</button>
                <button onclick="showTab('forecast')" id="forecastTab" class="py-2 px-3 md:px-4 border-b-2 border-transparent hover:border-gray-400 font-medium transition-all duration-300 hover:text-blue-400 text-sm md:text-base tab-button">7-Day Forecast</button>
                <button onclick="showTab('history')" id="historyTab" class="py-2 px-3 md:px-4 border-b-2 border-transparent hover:border-gray-400 font-medium transition-all duration-300 hover:text-blue-400 text-sm md:text-base tab-button">Historical Data</button>
            </div>
        </div>

        <!-- Current Data Tab -->
        <div id="currentTabContent">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                <!-- Temperature Card -->
                <div class="bg-gray-800 bg-opacity-50 backdrop-blur-lg rounded-xl p-3 md:p-4 border border-gray-700 hover:border-blue-500 transition-all duration-300">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h3 class="text-sm md:text-base font-semibold text-gray-300">Temperature</h3>
                            <p class="text-xs md:text-sm text-gray-400">Current: <span id="currentTemp" class="text-white">--</span>°C</p>
                        </div>
                        <div class="text-xl md:text-2xl text-blue-400">
                            <i class="fas fa-temperature-high"></i>
                        </div>
                    </div>
                    <div class="h-24 md:h-32 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-lg md:text-xl font-bold text-blue-400 mb-1" id="currentTempValue">--</div>
                            <div class="text-xs text-gray-400">Temperature</div>
                        </div>
                    </div>
                </div>

                <!-- Solar Radiation Card -->
                <div class="bg-gray-800 bg-opacity-50 backdrop-blur-lg rounded-xl p-3 md:p-4 border border-gray-700 hover:border-yellow-500 transition-all duration-300">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h3 class="text-sm md:text-base font-semibold text-gray-300">Solar Radiation</h3>
                            <p class="text-xs md:text-sm text-gray-400">Current: <span id="currentRadiation" class="text-white">--</span> W/m²</p>
                        </div>
                        <div class="text-xl md:text-2xl text-yellow-400">
                            <i class="fas fa-sun"></i>
                        </div>
                    </div>
                    <div class="h-24 md:h-32 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-lg md:text-xl font-bold text-yellow-400 mb-1" id="currentRadiationValue">--</div>
                            <div class="text-xs text-gray-400">Solar Radiation</div>
                        </div>
                    </div>
                </div>

                <!-- Wind Card -->
                <div class="bg-gray-800 bg-opacity-50 backdrop-blur-lg rounded-xl p-3 md:p-4 border border-gray-700 hover:border-green-500 transition-all duration-300">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h3 class="text-sm md:text-base font-semibold text-gray-300">Wind</h3>
                            <p class="text-xs md:text-sm text-gray-400">Current: <span id="currentWind" class="text-white">--</span> km/h</p>
                        </div>
                        <div class="text-xl md:text-2xl text-green-400">
                            <i class="fas fa-wind"></i>
                        </div>
                    </div>
                    <div class="h-24 md:h-32 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-lg md:text-xl font-bold text-green-400 mb-1" id="currentWindSpeed">--</div>
                            <div class="text-xs text-gray-400">Wind Speed</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mt-4 md:mt-6">
                <div class="bg-gray-800 bg-opacity-50 backdrop-blur-lg rounded-xl p-4 md:p-6 border border-gray-700 card-hover animate-slide-in chart-container" style="animation-delay: 0.4s">
                    <h2 class="text-lg md:text-xl font-semibold mb-3 md:mb-4 gradient-text">Temperature Trends</h2>
                    <canvas id="temperatureChart" class="w-full"></canvas>
                </div>
                
                <div class="bg-gray-800 bg-opacity-50 backdrop-blur-lg rounded-xl p-4 md:p-6 border border-gray-700 card-hover animate-slide-in chart-container" style="animation-delay: 0.5s">
                    <h2 class="text-lg md:text-xl font-semibold mb-3 md:mb-4 gradient-text">Solar Radiation Analysis</h2>
                    <canvas id="radiationAnalysisChart" class="w-full"></canvas>
                </div>

                <div class="bg-gray-800 bg-opacity-50 backdrop-blur-lg rounded-xl p-4 md:p-6 border border-gray-700 card-hover animate-slide-in chart-container" style="animation-delay: 0.6s">
                    <h2 class="text-lg md:text-xl font-semibold mb-3 md:mb-4 gradient-text">Wind Analysis</h2>
                    <canvas id="windAnalysisChart" class="w-full"></canvas>
                </div>
            </div>
        </div>

        <!-- Forecast Tab -->
        <div id="forecastTabContent" class="hidden">
            <div class="bg-gray-800 bg-opacity-50 backdrop-blur-lg rounded-xl p-4 md:p-6 border border-gray-700 mb-4 md:mb-6 card-hover animate-slide-in">
                <h2 class="text-lg md:text-xl font-semibold mb-3 md:mb-4 gradient-text">7-Day Energy Forecast</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-7 gap-2 md:gap-4" id="forecastCards">
                    <!-- Cards will be populated dynamically -->
                </div>
            </div>
            
            <div class="bg-gray-800 bg-opacity-50 backdrop-blur-lg rounded-xl p-4 md:p-6 border border-gray-700 card-hover animate-slide-in chart-container" style="animation-delay: 0.2s">
                <h2 class="text-lg md:text-xl font-semibold mb-3 md:mb-4 gradient-text">Weekly Energy Production Estimate</h2>
                <canvas id="weeklyForecastChart" class="w-full"></canvas>
            </div>
        </div>

        <!-- Historical Data Tab -->
        <div id="historyTabContent" class="hidden">
            <div class="bg-gray-800 bg-opacity-50 backdrop-blur-lg rounded-xl p-4 md:p-6 border border-gray-700 card-hover animate-slide-in chart-container">
                <h2 class="text-lg md:text-xl font-semibold mb-3 md:mb-4 gradient-text">Historical Data</h2>
                <canvas id="historicalChart" class="w-full"></canvas>
            </div>
        </div>
    </div>

    @include('energy.location-modal')

    <!-- Footer -->
    <footer class="bg-gray-800 bg-opacity-50 backdrop-blur-lg border-t border-gray-700 mt-16">
        <div class="container mx-auto px-6 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-solar-panel text-yellow-400 text-2xl mr-3"></i>
                        <span class="text-xl font-bold gradient-text">EnergyMonitor Pro</span>
                    </div>
                    <p class="text-gray-400">Empowering sustainable energy monitoring and analysis.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="/about" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <ul class="space-y-2">
                        <li class="text-gray-400"><i class="fas fa-envelope mr-2"></i> info@energymonitorpro.com</li>
                        <li class="text-gray-400"><i class="fas fa-phone mr-2"></i> +1 234 567 890</li>
                        <li class="text-gray-400"><i class="fas fa-map-marker-alt mr-2"></i> 123 Energy Street, Tech City</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-twitter text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-linkedin text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-facebook text-xl"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 EnergyMonitor Pro. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.querySelector('.mobile-menu');
            mobileMenu.classList.toggle('active');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.querySelector('.mobile-menu');
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            
            if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                mobileMenu.classList.remove('active');
            }
        });

        // Update tab switching function to handle mobile menu
        function showTab(tabName) {
            // Get all tab contents and buttons
            const tabContents = ['current', 'forecast', 'history'].map(tab => document.getElementById(tab + 'TabContent'));
            const tabButtons = ['current', 'forecast', 'history'].map(tab => document.getElementById(tab + 'Tab'));
            const mobileTabButtons = ['current', 'forecast', 'history'].map(tab => document.getElementById('mobile' + tab.charAt(0).toUpperCase() + tab.slice(1) + 'Tab'));
            
            // Hide all tab contents
            tabContents.forEach(content => content.classList.add('hidden'));
            
            // Remove active state from all tabs
            tabButtons.forEach(button => {
                button.classList.remove('border-blue-500');
                button.classList.add('border-transparent');
            });
            
            // Remove active state from all mobile tabs
            mobileTabButtons.forEach(button => {
                button.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName + 'TabContent').classList.remove('hidden');
            
            // Add active state to selected tab
            const selectedTab = document.getElementById(tabName + 'Tab');
            selectedTab.classList.remove('border-transparent');
            selectedTab.classList.add('border-blue-500');
            
            // Add active state to selected mobile tab
            const selectedMobileTab = document.getElementById('mobile' + tabName.charAt(0).toUpperCase() + tabName.slice(1) + 'Tab');
            selectedMobileTab.classList.add('active');
            
            // Close mobile menu after selecting a tab
            document.querySelector('.mobile-menu').classList.remove('active');
        }

        const data = @json($data);
        const dailyData = @json($dailyData);
        const historicalData = @json($historicalData);
        const locationName = @json($locationName);

        // Update location name in navigation bar on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.text-gray-400 span').textContent = locationName;
            showTab('current');
        });

        // Update current values function
        function updateCurrentValues() {
            const currentHour = new Date().getHours();
            const currentTemp = data.hourly.temperature_2m[currentHour];
            const currentRadiation = data.hourly.shortwave_radiation[currentHour];
            const currentWind = data.hourly.windspeed_10m[currentHour];

            document.getElementById('currentTemp').textContent = currentTemp.toFixed(1);
            document.getElementById('currentRadiation').textContent = currentRadiation.toFixed(1);
            document.getElementById('currentWind').textContent = currentWind.toFixed(1);
            
            // Update the large value displays
            document.getElementById('currentTempValue').textContent = currentTemp.toFixed(1);
            document.getElementById('currentRadiationValue').textContent = currentRadiation.toFixed(1);
            document.getElementById('currentWindSpeed').textContent = currentWind.toFixed(1);
        }

        // Update charts function
        function updateCharts() {
            // Update temperature chart
            tempChart.data.labels = data.hourly.time.map(time => {
                const date = new Date(time);
                return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
            });
            tempChart.data.datasets[0].data = data.hourly.temperature_2m;
            tempChart.update();

            // Update radiation chart
            radiationChart.data.labels = data.hourly.time.map(time => {
                const date = new Date(time);
                return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
            });
            radiationChart.data.datasets[0].data = data.hourly.shortwave_radiation;
            radiationChart.update();

            // Update wind chart
            windChart.data.labels = data.hourly.time.map(time => {
                const date = new Date(time);
                return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
            });
            windChart.data.datasets[0].data = data.hourly.windspeed_10m;
            windChart.update();
        }

        // Update forecast cards with animation
        function updateForecastCards() {
            const forecastCards = document.getElementById('forecastCards');
            forecastCards.innerHTML = '';

            dailyData.forEach((day, index) => {
                const date = new Date(day.date);
                const dayName = date.toLocaleDateString('en-US', { weekday: 'short' });
                const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                
                const card = document.createElement('div');
                card.className = 'p-4 bg-gray-700 rounded-lg text-center card-hover animate-slide-in';
                card.style.animationDelay = `${index * 0.1}s`;
                card.innerHTML = `
                    <div class="text-sm text-gray-400 mb-2">${dayName}</div>
                    <div class="text-lg font-bold mb-2">${formattedDate}</div>
                    <div class="text-sm">
                        <div class="text-yellow-400">${day.max_temp.toFixed(1)}°C</div>
                        <div class="text-blue-400">${day.min_temp.toFixed(1)}°C</div>
                        <div class="text-green-400">${(day.radiation / 1000).toFixed(1)} kWh/m²</div>
                    </div>
                `;
                forecastCards.appendChild(card);
            });
        }

        // Initialize Weekly Forecast Chart
        function initWeeklyForecastChart() {
            const ctx = document.getElementById('weeklyForecastChart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dailyData.map(day => new Date(day.date).toLocaleDateString('en-US', { weekday: 'short' })),
                    datasets: [{
                        label: 'Daily Solar Radiation (kWh/m²)',
                        data: dailyData.map(day => day.radiation / 1000),
                        backgroundColor: 'rgba(250, 204, 21, 0.5)',
                        borderColor: 'rgb(250, 204, 21)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: 'white'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: 'white'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: 'white'
                            }
                        }
                    }
                }
            });
        }

        // Initialize Historical Chart
        function initHistoricalChart() {
            const ctx = document.getElementById('historicalChart');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: historicalData.map(day => new Date(day.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
                    datasets: [{
                        label: 'Temperature (°C)',
                        data: historicalData.map(day => day.temperature),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Solar Radiation (kWh/m²)',
                        data: historicalData.map(day => day.radiation / 1000),
                        borderColor: 'rgb(250, 204, 21)',
                        backgroundColor: 'rgba(250, 204, 21, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: 'white'
                            }
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: 'white'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: 'white'
                            }
                        }
                    }
                }
            });
        }

        // Initialize all components
        updateCurrentValues();
        updateForecastCards();
        initWeeklyForecastChart();
        initHistoricalChart();

        // Temperature Chart
        new Chart(document.getElementById('temperatureChart'), {
            type: 'line',
            data: {
                labels: data.hourly.time.slice(0, 24).map(time => {
                    const date = new Date(time);
                    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                }),
                datasets: [{
                    label: 'Temperature (°C)',
                    data: data.hourly.temperature_2m.slice(0, 24),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    },
                    title: {
                        display: true,
                        text: new Date(data.hourly.time[0]).toLocaleDateString('en-US', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        }),
                        color: 'white',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    }
                },
                scales: {
                    y: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: 'white'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: 'white',
                            maxRotation: 0
                        }
                    }
                }
            }
        });

        // Solar Radiation Analysis Chart
        new Chart(document.getElementById('radiationAnalysisChart'), {
            type: 'line',
            data: {
                labels: data.hourly.time.slice(0, 24).map(time => {
                    const date = new Date(time);
                    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                }),
                datasets: [{
                    label: 'Solar Radiation (W/m²)',
                    data: data.hourly.shortwave_radiation.slice(0, 24),
                    borderColor: 'rgb(250, 204, 21)',
                    backgroundColor: 'rgba(250, 204, 21, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    },
                    title: {
                        display: true,
                        text: new Date(data.hourly.time[0]).toLocaleDateString('en-US', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        }),
                        color: 'white',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    }
                },
                scales: {
                    y: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: 'white'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: 'white',
                            maxRotation: 0
                        }
                    }
                }
            }
        });

        // Wind Analysis Chart
        new Chart(document.getElementById('windAnalysisChart'), {
            type: 'line',
            data: {
                labels: data.hourly.time.slice(0, 24).map(time => {
                    const date = new Date(time);
                    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                }),
                datasets: [{
                    label: 'Wind Speed (km/h)',
                    data: data.hourly.windspeed_10m.slice(0, 24),
                    borderColor: 'rgb(52, 211, 153)',
                    backgroundColor: 'rgba(52, 211, 153, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    },
                    title: {
                        display: true,
                        text: new Date(data.hourly.time[0]).toLocaleDateString('en-US', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        }),
                        color: 'white',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    }
                },
                scales: {
                    y: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: 'white'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: 'white',
                            maxRotation: 0
                        }
                    }
                }
            }
        });

        // Update location function
        function updateLocation(lat, lng, name) {
            // Update the location name in the navigation bar
            document.querySelector('.text-gray-400 span').textContent = name;
            // Redirect to the new location
            window.location.href = `/?latitude=${lat}&longitude=${lng}&location_name=${encodeURIComponent(name)}`;
        }

        // Update the location modal form submission
        document.getElementById('locationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const lat = document.getElementById('latitude').value;
            const lng = document.getElementById('longitude').value;
            const name = document.getElementById('locationName').value;
            updateLocation(lat, lng, name);
        });
    </script>
</body>
</html>