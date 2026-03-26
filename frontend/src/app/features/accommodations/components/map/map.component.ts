import { Component, Input, AfterViewInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LeafletModule } from '@bluehalo/ngx-leaflet';
import * as L from 'leaflet';

@Component({
  selector: 'app-map',
  standalone: true,
  imports: [CommonModule, LeafletModule],
  template: `
    <div style="height: 400px; width: 100%; z-index: 1;"
         leaflet
         [leafletOptions]="options"
         (leafletMapReady)="onMapReady($event)"></div>
  `,
})
export class MapComponent implements AfterViewInit {
  @Input() latitude: number = 40.4168;
  @Input() longitude: number = -3.7038;
  @Input() accommodationName: string = 'Ubicación';

  private map: L.Map | undefined;

  options: L.MapOptions = {
    layers: [
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      })
    ],
    zoom: 15,
    center: L.latLng(this.latitude, this.longitude)
  };

  onMapReady(map: L.Map) {
    this.map = map;
    this.addMarker();
  }

  private addMarker() {
  if (this.map) {
    // Crear un icono personalizado (gris, neutro)
    const customIcon = L.icon({
      iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
      shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowSize: [41, 41]
    });

    const marker = L.marker([this.latitude, this.longitude], { icon: customIcon }).addTo(this.map);
    marker.bindPopup(`<b>${this.accommodationName}</b>`).openPopup();
  }
}

  ngAfterViewInit(): void {}
}