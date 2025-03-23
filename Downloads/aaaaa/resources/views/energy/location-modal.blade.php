<!-- Location Modal -->
<div id="locationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-xl p-6 w-full max-w-2xl relative">
            <button onclick="toggleLocationModal()" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6 gradient-text">Change Location</h2>
            
            <!-- Tabs -->
            <div class="flex space-x-4 mb-6 border-b border-gray-700">
                <button onclick="showLocationTab('map')" id="mapTab" class="py-2 px-4 border-b-2 border-blue-500 font-medium transition-all duration-300 hover:text-blue-400">Map Pick</button>
                <button onclick="showLocationTab('manual')" id="manualTab" class="py-2 px-4 border-b-2 border-transparent hover:border-gray-400 font-medium transition-all duration-300 hover:text-blue-400">Manual Entry</button>
            </div>

            <!-- Map Tab Content -->
            <div id="mapTabContent">
                <div class="flex justify-between items-center mb-4">
                    <div class="text-sm text-gray-400">
                        <i class="fas fa-info-circle mr-2"></i>
                        Click on the map to select a location or drag the marker to adjust.
                    </div>
                    <button onclick="getCurrentLocation()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                        <i class="fas fa-location-dot mr-2"></i>Use Current Location
                    </button>
                </div>
                <div id="map" class="w-full h-[400px] rounded-lg mb-4"></div>
                <form id="mapLocationForm" class="space-y-4">
                    <input type="hidden" id="mapLatitude" value="{{ request('latitude', 28.6139) }}">
                    <input type="hidden" id="mapLongitude" value="{{ request('longitude', 77.2090) }}">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Selected Location</label>
                        <input type="text" id="mapLocationName" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500" readonly>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                        <i class="fas fa-check mr-2"></i>Confirm Location
                    </button>
                </form>
            </div>

            <!-- Manual Entry Tab Content -->
            <div id="manualTabContent" class="hidden">
                <form id="locationForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Search Address</label>
                        <div class="relative">
                            <input type="text" 
                                   id="addressSearch" 
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500" 
                                   placeholder="Enter address to search..."
                                   required>
                            <button type="button" 
                                    onclick="searchAddress()" 
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div id="searchResults" class="mt-2 space-y-2 max-h-40 overflow-y-auto hidden"></div>
                    </div>

                    <div class="text-sm text-gray-400">
                        <i class="fas fa-info-circle mr-2"></i>
                        Or enter coordinates manually (optional)
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Latitude</label>
                            <input type="number" 
                                   step="any" 
                                   id="latitude" 
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500" 
                                   placeholder="e.g., 28.6139">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Longitude</label>
                            <input type="number" 
                                   step="any" 
                                   id="longitude" 
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500" 
                                   placeholder="e.g., 77.2090">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Location Name</label>
                        <input type="text" 
                               id="locationName" 
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500" 
                               required>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                        <i class="fas fa-check mr-2"></i>Update Location
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let map;
    let marker;
    let searchTimeout;

    // Update location function
    function updateLocation(lat, lng, name) {
        // Update the location name in the navigation bar
        document.querySelector('.text-gray-400 span').textContent = name;
        // Redirect to the new location
        window.location.href = `/?latitude=${lat}&longitude=${lng}&location_name=${encodeURIComponent(name)}`;
    }

    // Handle map location form submission
    document.getElementById('mapLocationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const lat = document.getElementById('mapLatitude').value;
        const lng = document.getElementById('mapLongitude').value;
        const name = document.getElementById('mapLocationName').value;
        updateLocation(lat, lng, name);
    });

    // Handle manual entry form submission
    document.getElementById('locationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;
        const name = document.getElementById('locationName').value;
        updateLocation(lat, lng, name);
    });

    function initMap() {
        map = L.map('map').setView([28.6139, 77.2090], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        marker = L.marker([28.6139, 77.2090], {draggable: true}).addTo(map);

        marker.on('dragend', function(e) {
            const lat = e.target.getLatLng().lat;
            const lng = e.target.getLatLng().lng;
            updateLocationName(lat, lng);
        });

        // Handle map click
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            marker.setLatLng([lat, lng]);
            updateLocationName(lat, lng);
        });
    }

    function updateLocationName(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                const locationName = data.display_name;
                document.getElementById('mapLocationName').value = locationName;
                document.getElementById('mapLatitude').value = lat;
                document.getElementById('mapLongitude').value = lng;
            })
            .catch(error => console.error('Error:', error));
    }

    function searchAddress() {
        const searchInput = document.getElementById('addressSearch');
        const resultsDiv = document.getElementById('searchResults');
        const searchButton = document.querySelector('button[onclick="searchAddress()"]');
        
        // Show loading state
        searchButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        searchButton.disabled = true;
        
        // Clear previous timeout
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        // Add debounce to prevent too many API calls
        searchTimeout = setTimeout(() => {
            const query = searchInput.value;
            if (query.length < 3) {
                resultsDiv.innerHTML = '';
                searchButton.innerHTML = '<i class="fas fa-search"></i>';
                searchButton.disabled = false;
                return;
            }

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    data.slice(0, 5).forEach(result => {
                        const div = document.createElement('div');
                        div.className = 'p-2 hover:bg-gray-700 cursor-pointer';
                        div.textContent = result.display_name;
                        div.onclick = () => {
                            const lat = parseFloat(result.lat);
                            const lng = parseFloat(result.lon);
                            document.getElementById('addressSearch').value = result.display_name;
                            document.getElementById('locationName').value = result.display_name;
                            document.getElementById('latitude').value = lat;
                            document.getElementById('longitude').value = lng;
                            resultsDiv.innerHTML = '';
                        };
                        resultsDiv.appendChild(div);
                    });
                })
                .catch(error => console.error('Error:', error))
                .finally(() => {
                    searchButton.innerHTML = '<i class="fas fa-search"></i>';
                    searchButton.disabled = false;
                });
        }, 500);
    }

    function getCurrentLocation() {
        const button = document.querySelector('button[onclick="getCurrentLocation()"]');
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Getting Location...';
        button.disabled = true;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.setView([lat, lng], 13);
                    marker.setLatLng([lat, lng]);
                    updateLocationName(lat, lng);
                    button.innerHTML = '<i class="fas fa-location-dot mr-2"></i>Use Current Location';
                    button.disabled = false;
                },
                error => {
                    let errorMessage = 'Error getting location';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Location permission denied';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information unavailable';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Location request timed out';
                            break;
                        default:
                            errorMessage = 'An unknown error occurred';
                            break;
                    }
                    alert(errorMessage);
                    button.innerHTML = '<i class="fas fa-location-dot mr-2"></i>Use Current Location';
                    button.disabled = false;
                }
            );
        } else {
            alert('Geolocation is not supported by your browser');
            button.innerHTML = '<i class="fas fa-location-dot mr-2"></i>Use Current Location';
            button.disabled = false;
        }
    }

    function toggleLocationModal() {
        const modal = document.getElementById('locationModal');
        modal.classList.toggle('hidden');
        if (!modal.classList.contains('hidden')) {
            initMap();
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('locationModal');
        if (event.target == modal) {
            modal.classList.add('hidden');
        }
    }

    // Handle tab switching
    function showLocationTab(tabName) {
        // Get all tab contents and buttons
        const tabContents = ['map', 'manual'].map(tab => document.getElementById(tab + 'TabContent'));
        const tabButtons = ['map', 'manual'].map(tab => document.getElementById(tab + 'Tab'));
        
        // Hide all tab contents
        tabContents.forEach(content => content.classList.add('hidden'));
        
        // Remove active state from all tabs
        tabButtons.forEach(button => {
            button.classList.remove('border-blue-500');
            button.classList.add('border-transparent');
        });
        
        // Show selected tab content
        document.getElementById(tabName + 'TabContent').classList.remove('hidden');
        
        // Add active state to selected tab
        const selectedTab = document.getElementById(tabName + 'Tab');
        selectedTab.classList.remove('border-transparent');
        selectedTab.classList.add('border-blue-500');

        // If showing map tab, trigger a resize event to fix map display
        if (tabName === 'map') {
            setTimeout(() => {
                map.invalidateSize();
            }, 100);
        }
    }

    // Initialize with map tab active
    document.addEventListener('DOMContentLoaded', function() {
        showLocationTab('map');
    });
</script>