import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

// --- Interfaces para la respuesta de Open-Meteo ---
interface OpenMeteoCurrentResponse {
  current_weather: {
    temperature: number;
    weathercode: number;
    windspeed: number;
  };
}

interface OpenMeteoDailyResponse {
  daily: {
    time: string[];
    temperature_2m_max: number[];
    temperature_2m_min: number[];
    weathercode: number[];
  };
}

// --- Interfaces de salida (lo que recibe el componente) ---
export interface CurrentWeather {
  temperature: number;
  weathercode: number;
  description: string;
  windspeed: number;
}

export interface DailyForecast {
  date: string;
  temperature_max: number;
  temperature_min: number;
  weathercode: number;
}

@Injectable({
  providedIn: 'root'
})
export class WeatherService {
  private apiUrl = 'https://api.open-meteo.com/v1/forecast';

  constructor(private http: HttpClient) {}

  getCurrentWeather(lat: string, lon: string): Observable<CurrentWeather> {
    return this.http.get<OpenMeteoCurrentResponse>(this.apiUrl, {
      params: {
        latitude: lat,
        longitude: lon,
        current_weather: 'true',
        timezone: 'auto'
      }
    }).pipe(
      map((response) => {
        const weather = response.current_weather;
        return {
          temperature: weather.temperature,
          weathercode: weather.weathercode,
          description: this.getWeatherDescription(weather.weathercode),
          windspeed: weather.windspeed
        };
      })
    );
  }

  getForecast(lat: string, lon: string): Observable<DailyForecast[]> {
    return this.http.get<OpenMeteoDailyResponse>(this.apiUrl, {
      params: {
        latitude: lat,
        longitude: lon,
        daily: 'temperature_2m_max,temperature_2m_min,weathercode',
        timezone: 'auto'
      }
    }).pipe(
      map((response) => {
        return response.daily?.time.map((date: string, i: number) => ({
          date,
          temperature_max: response.daily.temperature_2m_max[i],
          temperature_min: response.daily.temperature_2m_min[i],
          weathercode: response.daily.weathercode[i]
        })) || [];
      })
    );
  }

  private getWeatherDescription(code: number): string {
    const descriptions: Record<number, string> = {
      0: 'Despejado',
      1: 'Mayormente despejado',
      2: 'Parcialmente nublado',
      3: 'Nublado',
      45: 'Niebla',
      48: 'Niebla con escarcha',
      51: 'Llovizna ligera',
      53: 'Llovizna moderada',
      55: 'Llovizna densa',
      61: 'Lluvia ligera',
      63: 'Lluvia moderada',
      65: 'Lluvia intensa',
      71: 'Nevada ligera',
      73: 'Nevada moderada',
      75: 'Nevada intensa',
      80: 'Chubascos ligeros',
      81: 'Chubascos moderados',
      82: 'Chubascos violentos',
      95: 'Tormenta',
      96: 'Tormenta con granizo ligero',
      99: 'Tormenta con granizo intenso'
    };
    return descriptions[code] ?? 'Desconocido';
  }
}