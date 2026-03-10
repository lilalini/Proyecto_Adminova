import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AccommodationService } from '../../../core/services/accommodation.service';
import { IconSvgComponent } from '../../../shared/components/icon-svg.component';

@Component({
  selector: 'app-accommodations-list',
  standalone: true,
  imports: [
    CommonModule, 
    RouterModule,
    IconSvgComponent
  ],
  templateUrl: './list.component.html',
})
export class ListComponent implements OnInit {
  accommodations: any[] = [];
  loading = true;
  errorMessage = '';

  constructor(private accommodationService: AccommodationService) {}

  ngOnInit() {
    this.loadAccommodations();
  }

  loadAccommodations() {
    this.loading = true;
    this.accommodationService.getAll().subscribe({
      next: (response) => {
        this.accommodations = response.data;
        this.loading = false;
      },
      error: (error) => {
        this.errorMessage = 'Error al cargar los alojamientos';
        this.loading = false;
        console.error(error);
      }
    });
  }

  deleteAccommodation(id: number) {
    if (confirm('¿Estás seguro de eliminar este alojamiento?')) {
      this.accommodationService.delete(id).subscribe({
        next: () => {
          this.accommodations = this.accommodations.filter(acc => acc.id !== id);
        },
        error: (error) => {
          alert('Error al eliminar el alojamiento');
          console.error(error);
        }
      });
    }
  }
}