import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { AccommodationService } from '../../../core/services/accommodation.service';
import { BookingService } from '../../../core/services/booking.service';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { GreetingComponent } from '../../../shared/components/greeting/greeting.component';

@Component({
  selector: 'app-owner-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent, GreetingComponent],
  templateUrl: './owner-dashboard.component.html',
})
export class OwnerDashboardComponent implements OnInit {
  user: any = null;
  stats = {
    accommodations: 0,
    bookings: 0,
    revenue: 0
  };
  recentBookings: any[] = [];
  recentAccommodations: any[] = [];
  loading = true;

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
    // Alojamientos del owner
    this.accommodationService.getAll().subscribe({
      next: (res) => {
        this.stats.accommodations = res.data.length;
        this.recentAccommodations = res.data.slice(-5);
      }
    });

    // Reservas de sus alojamientos
    this.bookingService.getAll().subscribe({
      next: (res) => {
        this.stats.bookings = res.data.length;
        this.recentBookings = res.data.slice(-5);
        this.stats.revenue = res.data.reduce((sum, b) => sum + (b.total_amount || 0), 0);
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
}