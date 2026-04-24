import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, ActivatedRoute, Router } from '@angular/router';
import { BookingService } from '../../../../core/services/booking.service';
import { Booking } from '../../../../core/models/booking.model';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-admin-show',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent],
  templateUrl: './admin-show.component.html',
})
export class AdminShowComponent implements OnInit {
  booking: Booking | null = null;
  loading = true;
  errorMessage = '';

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private bookingService: BookingService
  ) {}

  ngOnInit() {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    if (id) {
      this.loadBooking(id);
    } else {
      this.errorMessage = 'ID de reserva no válido';
      this.loading = false;
    }
  }

  loadBooking(id: number) {
    this.bookingService.getOne(id).subscribe({
      next: (response) => {
        this.booking = response.data;
        this.loading = false;
      },
      error: (error) => {
        this.errorMessage = 'Error al cargar la reserva';
        this.loading = false;
      }
    });
  }

  formatDate(date: string): string {
    return new Date(date).toLocaleDateString('es-ES');
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

  getAccommodationTitle(): string {
        return this.booking?.accommodation?.title || 'Alojamiento no disponible';
    }

    getAccommodationAddress(): string {
        return this.booking?.accommodation?.address || 'Dirección no disponible';
    }
}