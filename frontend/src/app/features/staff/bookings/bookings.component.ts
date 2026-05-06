import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { BookingService } from '../../../core/services/booking.service';
import { getBookingStatus, getStatusClass, getStatusText } from '../../../shared/utils/booking-status.util';

@Component({
  selector: 'app-staff-bookings',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './bookings.component.html'  // ← HTML aparte
})
export class StaffBookingsComponent implements OnInit {
  bookings: any[] = [];
  loading = true;

  constructor(private bookingService: BookingService) {}

  ngOnInit() {
    this.bookingService.getAll().subscribe({
      next: (res) => {
        this.bookings = res.data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error:', err);
        this.loading = false;
      }
    });
  }

  getBookingStatus = getBookingStatus;
  getStatusClass = getStatusClass;
  getStatusText = getStatusText;
}