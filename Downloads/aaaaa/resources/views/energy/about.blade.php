<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Energy Monitor Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        .gradient-text {
            background: linear-gradient(45deg, #60A5FA, #34D399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 text-white min-h-screen">
    <!-- Navigation -->
    <nav class="bg-gray-800 bg-opacity-50 backdrop-blur-lg border-b border-gray-700 fixed w-full z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-solar-panel text-yellow-400 text-2xl mr-3 animate-float"></i>
                    <span class="text-xl font-bold gradient-text">EnergyMonitor Pro</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-gray-400 hover:text-white transition-colors">Home</a>
                    <a href="/about" class="text-blue-400">About Us</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8 pt-24">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold mb-4 gradient-text">About EnergyMonitor Pro</h1>
            <p class="text-gray-400 text-lg">Empowering sustainable energy monitoring and analysis</p>
        </div>

        <!-- Mission Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
            <div class="bg-gray-800 bg-opacity-50 backdrop-blur-lg rounded-xl p-8 border border-gray-700">
                <h2 class="text-2xl font-semibold mb-4 gradient-text">Our Mission</h2>
                <p class="text-gray-300">To provide accurate, real-time energy monitoring solutions that help individuals and organizations make informed decisions about their energy consumption and solar potential.</p>
            </div>
            <div class="bg-gray-800 bg-opacity-50 backdrop-blur-lg rounded-xl p-8 border border-gray-700">
                <h2 class="text-2xl font-semibold mb-4 gradient-text">Our Vision</h2>
                <p class="text-gray-300">To become the leading platform for energy monitoring and analysis, contributing to a more sustainable and energy-efficient future.</p>
            </div>
        </div>

        <!-- Features Section -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold mb-8 gradient-text text-center">Key Features</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-800 bg-opacity-50 backdrop-blur-lg rounded-xl p-6 border border-gray-700">
                    <div class="text-blue-400 text-3xl mb-4">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Real-time Monitoring</h3>
                    <p class="text-gray-400">Track temperature and solar radiation data in real-time with our advanced monitoring system.</p>
                </div>
                <div class="bg-gray-800 bg-opacity-50 backdrop-blur-lg rounded-xl p-6 border border-gray-700">
                    <div class="text-yellow-400 text-3xl mb-4">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">7-Day Forecast</h3>
                    <p class="text-gray-400">Plan ahead with accurate weather and energy production forecasts for the next week.</p>
                </div>
                <div class="bg-gray-800 bg-opacity-50 backdrop-blur-lg rounded-xl p-6 border border-gray-700">
                    <div class="text-green-400 text-3xl mb-4">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Historical Analysis</h3>
                    <p class="text-gray-400">Access historical data to analyze trends and optimize your energy usage.</p>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold mb-8 gradient-text text-center">Our Team</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-32 h-32 mx-auto mb-4 rounded-full bg-gray-700 flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">John Doe</h3>
                    <p class="text-gray-400">Lead Developer</p>
                </div>
                <div class="text-center">
                    <div class="w-32 h-32 mx-auto mb-4 rounded-full bg-gray-700 flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Jane Smith</h3>
                    <p class="text-gray-400">Data Scientist</p>
                </div>
                <div class="text-center">
                    <div class="w-32 h-32 mx-auto mb-4 rounded-full bg-gray-700 flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Mike Johnson</h3>
                    <p class="text-gray-400">UI/UX Designer</p>
                </div>
            </div>
        </div>
    </div>

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
</body>
</html> 