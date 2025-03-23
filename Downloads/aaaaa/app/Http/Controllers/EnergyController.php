<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\EnergyData;

class EnergyController extends Controller
{
    public function index(Request $request)
    {
        // Default to Delhi's coordinates
        $latitude = $request->input('latitude', 28.6139);
        $longitude = $request->input('longitude', 77.2090);
        $locationName = $request->input('location_name', 'Delhi');
        
        // Fetch current and forecast data
        $response = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'hourly' => 'temperature_2m,apparent_temperature,shortwave_radiation,windspeed_10m,winddirection_10m',
            'daily' => 'temperature_2m_max,temperature_2m_min,shortwave_radiation_sum,windspeed_10m_max,winddirection_10m_dominant',
            'timezone' => 'auto'
        ]);

        $data = $response->json();

        // Process daily data for forecast
        $dailyData = [];
        if (isset($data['daily'])) {
            foreach ($data['daily']['time'] as $index => $date) {
                $dailyData[] = [
                    'date' => $date,
                    'max_temp' => $data['daily']['temperature_2m_max'][$index],
                    'min_temp' => $data['daily']['temperature_2m_min'][$index],
                    'radiation' => $data['daily']['shortwave_radiation_sum'][$index],
                    'wind_speed' => $data['daily']['windspeed_10m_max'][$index],
                    'wind_direction' => $data['daily']['winddirection_10m_dominant'][$index]
                ];
            }
        }

        // Generate historical data (last 30 days) - using a simple model
        $historicalData = [];
        $currentDate = new \DateTime();
        for ($i = 30; $i >= 0; $i--) {
            $date = clone $currentDate;
            $date->modify("-{$i} days");
            $historicalData[] = [
                'date' => $date->format('Y-m-d'),
                'temperature' => rand(15, 30), // Example data
                'radiation' => rand(2000, 8000), // Example data
                'wind_speed' => rand(5, 25), // Example wind speed data
                'wind_direction' => rand(0, 360) // Example wind direction data
            ];
        }
        
        return view('energy.index', compact('data', 'locationName', 'dailyData', 'historicalData'));
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'locationName' => 'required|string'
        ]);

        // Store location in session
        session([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'locationName' => $request->locationName
        ]);

        return response()->json(['success' => true]);
    }

    private function getWeatherData($latitude, $longitude)
    {
        // Fetch current and forecast data
        $response = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'hourly' => 'temperature_2m,apparent_temperature,shortwave_radiation,windspeed_10m,winddirection_10m',
            'daily' => 'temperature_2m_max,temperature_2m_min,shortwave_radiation_sum,windspeed_10m_max,winddirection_10m_dominant',
            'timezone' => 'auto'
        ]);

        return $response->json();
    }

    private function getDailyData($data)
    {
        // Process daily data for forecast
        $dailyData = [];
        if (isset($data['daily'])) {
            foreach ($data['daily']['time'] as $index => $date) {
                $dailyData[] = [
                    'date' => $date,
                    'max_temp' => $data['daily']['temperature_2m_max'][$index],
                    'min_temp' => $data['daily']['temperature_2m_min'][$index],
                    'radiation' => $data['daily']['shortwave_radiation_sum'][$index],
                    'wind_speed' => $data['daily']['windspeed_10m_max'][$index],
                    'wind_direction' => $data['daily']['winddirection_10m_dominant'][$index]
                ];
            }
        }
        return $dailyData;
    }

    private function getHistoricalData($latitude, $longitude)
    {
        // Generate historical data (last 30 days) - using a simple model
        $historicalData = [];
        $currentDate = new \DateTime();
        for ($i = 30; $i >= 0; $i--) {
            $date = clone $currentDate;
            $date->modify("-{$i} days");
            $historicalData[] = [
                'date' => $date->format('Y-m-d'),
                'temperature' => rand(15, 30), // Example data
                'radiation' => rand(2000, 8000), // Example data
                'wind_speed' => rand(5, 25), // Example wind speed data
                'wind_direction' => rand(0, 360) // Example wind direction data
            ];
        }
        return $historicalData;
    }
}