import { Component, OnInit, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IconSvgComponent } from '../icon-svg/icon-svg.component';
import { WeatherService } from '../../../core/services/weather.service';

@Component({
  selector: 'app-weather',
  standalone: true,
  imports: [CommonModule, IconSvgComponent],
  templateUrl: './weather.component.html',
})
export class WeatherComponent implements OnInit {
  @Input() latitude?: string;
  @Input() longitude?: string;
  @Input() locationName?: string;
  
  weather: any = null;
  loading = true;
  error = false;

  constructor(private weatherService: WeatherService) {}

  ngOnInit() {
    if (this.latitude && this.longitude) {
      this.loadWeather();
    } else {
      this.loadDefaultWeather();
    }
  }

  loadWeather() {
    this.weatherService.getCurrentWeather(this.latitude!, this.longitude!).subscribe({
      next: (data) => {
        this.weather = data;
        this.loading = false;
      },
      error: () => {
        this.error = true;
        this.loading = false;
      }
    });
  }

  loadDefaultWeather() {
    // Coordenadas por defecto (Madrid)
    this.weatherService.getCurrentWeather('40.4165', '-3.7026').subscribe({
      next: (data) => {
        this.weather = data;
        this.loading = false;
      },
      error: () => {
        this.error = true;
        this.loading = false;
      }
    });
  }

  getWeatherIcon(code: number): string {
    if (code === 0) return 'sun';
    if (code <= 3) return 'cloud-sun';
    if (code <= 48) return 'cloud-fog';
    if (code <= 67) return 'cloud-rain';
    if (code <= 77) return 'cloud-snow';
    if (code >= 95) return 'cloud-lightning';
    return 'cloud';
  }
}