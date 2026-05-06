import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { BookingService } from '../../../../core/services/booking.service';
import { Booking } from '../../../../core/models/booking.model';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';
import { getBookingStatus, getStatusClass, getStatusText } from '../../../../shared/utils/booking-status.util';

@Component({
  selector: 'app-booking-detail',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent],
  templateUrl: './detail.component.html',
})
export class DetailComponent implements OnInit {
  booking: Booking | null = null;
  loading = true;
  errorMessage = '';

  constructor(
    private route: ActivatedRoute,
    private bookingService: BookingService
  ) {}

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.loadBooking(parseInt(id));
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
        console.error('Error cargando reserva:', error);
        this.errorMessage = 'No se pudo cargar la reserva';
        this.loading = false;
      }
    });
  }

  formatDate(date: string): string {
    return new Date(date).toLocaleDateString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  }

/**
   * Descarga la confirmación de reserva (PDF)
   */
  downloadConfirmation(): void {
    if (!this.booking) {
      alert('No hay información de la reserva');
      return;
    }

    this.bookingService.downloadConfirmation(this.booking.id).subscribe({
      next: (blob: Blob) => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `confirmacion-${this.booking!.id}.pdf`;
        a.click();
        window.URL.revokeObjectURL(url);
      },
      error: (error) => {
        console.error('Error descargando confirmación:', error);
        alert('No se pudo descargar la confirmación');
      }
    });
  }

    getBookingStatus(booking: any): string {
    return getBookingStatus(booking);
  }

  getStatusClass(status: string): string {
    return getStatusClass(status);
  }

  getStatusText(status: string): string {
    return getStatusText(status);
  }

}