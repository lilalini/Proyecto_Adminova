import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { BookingService } from '../../../core/services/booking.service';
import { Booking } from '../../../core/models/booking.model';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { getBookingStatus, getStatusClass, getStatusText } from '../../../shared/utils/booking-status.util';

@Component({
  selector: 'app-owner-bookings',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent],
  templateUrl: './bookings.component.html',
})
export class OwnerBookingsComponent implements OnInit {
  bookings: Booking[] = [];
  loading = true;

  constructor(private bookingService: BookingService) {}

  ngOnInit() {
    this.bookingService.getAll().subscribe({
      next: (response) => {
        this.bookings = response.data;
        this.loading = false;
      },
      error: () => {
        this.loading = false;
      }
    });
  }

  formatDate(date: string): string {
    return new Date(date).toLocaleDateString('es-ES');
  }

    getBookingStatus = getBookingStatus;
    getStatusClass = getStatusClass;
    getStatusText = getStatusText;
}