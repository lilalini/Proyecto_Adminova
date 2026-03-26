import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { BookingService } from '../../../../core/services/booking.service';
import { Booking } from '../../../../core/models/booking.model';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';
import { BookingCardComponent } from '../../components/booking-card/booking-card.component';

@Component({
  selector: 'app-bookings-list',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent, BookingCardComponent],
  templateUrl: './list.component.html',
})
export class ListComponent implements OnInit {
  bookings: Booking[] = [];
  loading = true;
  errorMessage = '';
  
  // Paginación
  pagination: any = null;
  currentPage = 1;

  constructor(private bookingService: BookingService) {}

  ngOnInit() {
    this.loadBookings();
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

  getStatusClass(status: string): string {
    const classes: Record<string, string> = {
      'pending': 'bg-yellow-100 text-yellow-800',
      'confirmed': 'bg-green-100 text-green-800',
      'cancelled': 'bg-red-100 text-red-800',
      'completed': 'bg-blue-100 text-blue-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
  }

  getStatusText(status: string): string {
    const texts: Record<string, string> = {
      'pending': 'Pendiente',
      'confirmed': 'Confirmada',
      'cancelled': 'Cancelada',
      'completed': 'Completada'
    };
    return texts[status] || status;
  }
}