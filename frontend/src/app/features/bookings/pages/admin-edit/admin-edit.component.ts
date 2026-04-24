import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { BookingService } from '../../../../core/services/booking.service';
import { Booking } from '../../../../core/models/booking.model';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-admin-edit',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, IconSvgComponent],
  templateUrl: './admin-edit.component.html',
})
export class AdminEditComponent implements OnInit {
  booking: Booking | null = null;
  loading = true;
  saving = false;
  errorMessage = '';
  successMessage = '';

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

  onSubmit() {
    if (!this.booking) return;
    this.saving = true;
    this.errorMessage = '';

    this.bookingService.update(this.booking.id, {
      status: this.booking.status,
      check_in: this.booking.check_in,
      check_out: this.booking.check_out,
      adults: this.booking.adults,
      children: this.booking.children,
      total_amount: this.booking.total_amount
    }).subscribe({
      next: () => {
        this.saving = false;
        this.successMessage = 'Reserva actualizada correctamente';
        setTimeout(() => this.router.navigate(['/admin/bookings']), 1500);
      },
      error: (error) => {
        this.errorMessage = error.error?.message || 'Error al actualizar';
        this.saving = false;
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