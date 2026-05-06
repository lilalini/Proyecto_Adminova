import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { forkJoin } from 'rxjs';
import { AuthService } from '../../../core/services/auth.service';
import { AccommodationService } from '../../../core/services/accommodation.service';
import { BookingService } from '../../../core/services/booking.service';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { SkeletonComponent } from '../../../shared/components/skeleton/skeleton.component';
import { GreetingComponent } from '../../../shared/components/greeting/greeting.component';

@Component({
  selector: 'app-owner-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent, SkeletonComponent, GreetingComponent],
  templateUrl: './owner-dashboard.component.html',
})
export class OwnerDashboardComponent implements OnInit {
  user: any = null;
  loading = true;

  stats = {
    accommodations: 0,
    bookings: 0,
    revenue: 0
  };

  recentBookings: any[] = [];
  recentAccommodations: any[] = [];

  constructor(
    private auth: AuthService,
    private accommodationService: AccommodationService,
    private bookingService: BookingService
  ) {}

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      this.user = user;
    });
    this.loadData();
  }

  loadData() {
    forkJoin({
      accommodations: this.accommodationService.getAll(),
      bookings: this.bookingService.getAll(),
    }).subscribe({
      next: ({ accommodations, bookings }) => {
        this.stats.accommodations = accommodations.meta?.total ?? accommodations.data.length;
        this.recentAccommodations = accommodations.data.slice(0, 5);

        this.stats.bookings = bookings.meta?.total ?? bookings.data.length;
        this.recentBookings = bookings.data.slice(0, 5);

        this.stats.revenue = bookings.data
          .filter(b => ['confirmed', 'checked_in', 'checked_out'].includes(b.status))
          .reduce((sum, b) => sum + (b.total_amount || 0), 0);

        this.loading = false;
      },
      error: () => {
        this.loading = false;
      }
    });
  }
}