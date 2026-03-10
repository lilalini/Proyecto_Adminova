import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
//import { AccommodationService } from '../../core/services/accommodation.service';
import { PublicService } from '../../core/services/public.service';
import { IconSvgComponent } from '../../shared/components/icon-svg.component';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent],
  templateUrl: './home.component.html',
})
export class HomeComponent implements OnInit {
  accommodations: any[] = [];
  featuredAccommodations: any[] = [];
  loading = true;

  constructor(private publicService: PublicService) {}

  ngOnInit() {
    this.loadAccommodations();
  }

  loadAccommodations() {
    this.publicService.getAccommodations().subscribe({
      next: (response) => {
        this.accommodations = response.data;
        // Tomamos los primeros 6 como destacados
        this.featuredAccommodations = this.accommodations.slice(0, 6);
        this.loading = false;
      },
      error: (error) => {
        console.error('Error cargando alojamientos:', error);
        this.loading = false;
      }
    });
  }
}