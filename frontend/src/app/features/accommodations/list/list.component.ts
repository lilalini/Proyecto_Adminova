import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AccommodationService } from '../../../core/services/accommodation.service';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { Router } from '@angular/router';
import { Accommodation } from '../../../core/models/accommodation.model';

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
  accommodations: Accommodation[] = [];
  loading = true;
  errorMessage = '';

  // Paginación
  pagination: any = null;
  currentPage = 1;

  // Modal de eliminación
  showDeleteModal = false;
  deleteModalMessage = '';
  accommodationToDelete: number | null = null;


 constructor(
    private accommodationService: AccommodationService,
    private router: Router
  ) {}

  ngOnInit() {
    this.loadAccommodations(this.currentPage);
  }

  loadAccommodations(page: number = 1) {
    this.loading = true;
    this.accommodationService.getAll(page).subscribe({
      next: (response) => {
        this.accommodations = response.data;
        this.pagination = response.meta;
        this.loading = false;
      },
      error: (error) => {
        this.errorMessage = 'Error al cargar los alojamientos';
        this.loading = false;
      }
    });
  }

  loadPage(page: number) {
    if (page >= 1 && page <= this.pagination?.last_page) {
      this.currentPage = page;
      this.loadAccommodations(page);
    }
  }

  confirmDelete(id: number, title: string) {
    this.accommodationToDelete = id;
    this.deleteModalMessage = `¿Estás seguro de que quieres eliminar el alojamiento "${title}"? Esta acción no se puede deshacer.`;
    this.showDeleteModal = true;
  }

  cancelDelete() {
    this.showDeleteModal = false;
    this.accommodationToDelete = null;
  }

  deleteAccommodation() {
    if (!this.accommodationToDelete) return;

    // Guardar la página actual antes de eliminar
    const currentPage = this.pagination?.current_page || 1;

    this.accommodationService.delete(this.accommodationToDelete).subscribe({
      next: () => {
        this.showDeleteModal = false;
        this.accommodationToDelete = null;
        
        // Recargar la misma página
        this.loadAccommodations(currentPage);
      },
      error: (error) => {
        this.showDeleteModal = false;
        console.error('Error al eliminar:', error);
      }
    });
  }
}
