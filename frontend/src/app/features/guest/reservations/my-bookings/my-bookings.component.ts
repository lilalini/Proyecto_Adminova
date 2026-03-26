import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { BookingService } from '../../../../core/services/booking.service';
import { AuthService } from '../../../../core/services/auth.service';
import { Booking } from '../../../../core/models/booking.model';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';
import { BookingCardComponent } from '../../../bookings/components/booking-card/booking-card.component';

@Component({
  selector: 'app-my-bookings',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent, BookingCardComponent],
  templateUrl: './my-bookings.component.html',
})
export class MyBookingsComponent implements OnInit {
  bookings: Booking[] = [];
  loading = true;
  errorMessage = '';

  constructor(
    private bookingService: BookingService,
    private auth: AuthService
  ) {}

  ngOnInit() {
    this.loadMyBookings();
  }

  loadMyBookings() {
    this.loading = true;
    // Endpoint específico para guest
    this.bookingService.getMyBookings().subscribe({
      next: (response) => {
        this.bookings = response.data;
        this.loading = false;
      },
      error: (error) => {
        console.error('Error cargando reservas:', error);
        this.errorMessage = 'No se pudieron cargar tus reservas';
        this.loading = false;
      }
    });
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

  formatDate(date: string): string {
    return new Date(date).toLocaleDateString('es-ES');
  }
}