import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AccommodationService } from '../../../core/services/accommodation.service';
import { Accommodation } from '../../../core/models/accommodation.model';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { SkeletonComponent } from '../../../shared/components/skeleton/skeleton.component';

@Component({
  selector: 'app-owner-accommodations',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent, SkeletonComponent],
  templateUrl: './accommodations.component.html',
})
export class OwnerAccommodationsComponent implements OnInit {
  accommodations: Accommodation[] = [];
  loading = true;
  total = 0;

  constructor(private accommodationService: AccommodationService) {}

  ngOnInit() {
    this.accommodationService.getAll().subscribe({
      next: (response) => {
        this.accommodations = response.data;
        this.total = response.meta?.total ?? response.data.length;
        this.loading = false;
      },
      error: () => {
        this.loading = false;
      }
    });
  }

  getStatusLabel(status: string): string {
    const labels: Record<string, string> = {
      published: 'Publicado',
      draft: 'Borrador',
      maintenance: 'Mantenimiento',
      inactive: 'Inactivo'
    };
    return labels[status] ?? status;
  }

  getStatusClass(status: string): string {
    const classes: Record<string, string> = {
      published: 'bg-emerald-100 text-emerald-700',
      draft: 'bg-gray-100 text-gray-600',
      maintenance: 'bg-amber-100 text-amber-700',
      inactive: 'bg-red-100 text-red-600'
    };
    return classes[status] ?? 'bg-gray-100 text-gray-600';
  }
}