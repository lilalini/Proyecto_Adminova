import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { BookingService } from '../../../../core/services/booking.service';
import { Booking } from '../../../../core/models/booking.model';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-admin-list',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent],
  templateUrl: './admin-list.component.html',
})
export class AdminListComponent implements OnInit {
  bookings: Booking[] = [];
  loading = true;
  errorMessage = '';
  pagination: any = null;
  currentPage = 1;

  showDeleteModal = false;
  deleteModalMessage = '';
  bookingToDelete: number | null = null;

  constructor(private bookingService: BookingService) {}

  ngOnInit() {
    this.loadBookings(this.currentPage);
  }

  loadBookings(page: number = 1) {
    this.loading = true;
    this.bookingService.getAll(page).subscribe({
      next: (response) => {
        this.bookings = response.data;
        this.pagination = response.meta;
        this.loading = false;
      },
      error: (error) => {
        this.errorMessage = 'Error al cargar las reservas';
        this.loading = false;
      }
    });
  }

  loadPage(page: number) {
    if (page >= 1 && page <= this.pagination?.last_page) {
      this.currentPage = page;
      this.loadBookings(page);
    }
  }

  confirmDelete(id: number, reference: string) {
    this.bookingToDelete = id;
    this.deleteModalMessage = `¿Estás seguro de que quieres eliminar la reserva "${reference}"? Esta acción no se puede deshacer.`;
    this.showDeleteModal = true;
  }

  cancelDelete() {
    this.showDeleteModal = false;
    this.bookingToDelete = null;
  }

  deleteBooking() {
    if (!this.bookingToDelete) return;
    const currentPage = this.pagination?.current_page || 1;

    this.bookingService.delete(this.bookingToDelete).subscribe({
      next: () => {
        this.showDeleteModal = false;
        this.bookingToDelete = null;
        this.loadBookings(currentPage);
      },
      error: (error) => {
        this.showDeleteModal = false;
        console.error('Error al eliminar:', error);
      }
    });
  }

  getStatusClass(status: string): string {
    const classes: Record<string, string> = {
      pending: 'bg-yellow-100 text-yellow-800',
      confirmed: 'bg-green-100 text-green-800',
      cancelled: 'bg-red-100 text-red-800',
      checked_in: 'bg-blue-100 text-blue-800',
      checked_out: 'bg-gray-100 text-gray-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
  }

  getStatusText(status: string): string {
    const texts: Record<string, string> = {
      pending: 'Pendiente',
      confirmed: 'Confirmada',
      cancelled: 'Cancelada',
      checked_in: 'Check-in',
      checked_out: 'Check-out'
    };
    return texts[status] || status;
  }
}