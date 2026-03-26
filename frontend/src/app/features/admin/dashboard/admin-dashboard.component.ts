import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { AccommodationService } from '../../../core/services/accommodation.service';
import { BookingService } from '../../../core/services/booking.service';
import { GuestService } from '../../../core/services/guest.service';
import { PaymentService } from '../../../core/services/payment.service';
import { User } from '../../../core/models/user.model';
import { Accommodation } from '../../../core/models/accommodation.model';
import { Booking } from '../../../core/models/booking.model';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { GreetingComponent } from '../../../shared/components/greeting/greeting.component';
import { SkeletonComponent } from '../../../shared/components/skeleton/skeleton.component'; 

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [
    CommonModule, 
    RouterModule,
    IconSvgComponent,
    GreetingComponent,
    SkeletonComponent  
  ],
  templateUrl: './admin-dashboard.component.html',
})
export class AdminDashboardComponent implements OnInit {
  user: User | null = null;
  loading = true;  
  
  stats = {
    accommodations: 0,
    bookings: 0,
    guests: 0,
    revenue: 0
  };

  recentBookings: Booking[] = [];
  recentAccommodations: Accommodation[] = [];

  constructor(
    private auth: AuthService,
    private router: Router,
    private accommodationService: AccommodationService,
    private bookingService: BookingService,
    private guestService: GuestService,
    private paymentService: PaymentService
  ) {}

  today = new Date();

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      this.user = user;
    });
    this.loadRealData();
  }

 loadRealData() {
  let completedRequests = 0;
  const totalRequests = 4; // accommodations, bookings, guests, payments

  const checkComplete = () => {
    completedRequests++;
    if (completedRequests === totalRequests) {
      this.loading = false;
    }
  };

  // Alojamientos
  this.accommodationService.getAll().subscribe({
    next: (res) => {
      this.stats.accommodations = res.meta?.total || res.data.length;
      this.recentAccommodations = res.data.slice(-5);
      checkComplete();
    }
  });

  // Reservas
  this.bookingService.getAll().subscribe({
    next: (res) => {
      this.stats.bookings = res.meta?.total || res.data.length;
      this.recentBookings = res.data.slice(-5);
      checkComplete();
    }
  });

  // Huéspedes
  this.guestService.getAll().subscribe({
    next: (res) => {
      this.stats.guests = res.meta?.total || res.data.length;
      checkComplete();
    }
  });

  // Pagos (ingresos)
  this.paymentService.getAll().subscribe({
    next: (res) => {
      const total = res.data.reduce((sum, payment) => sum + (payment.amount || 0), 0);
      this.stats.revenue = Math.round(total);
      checkComplete();
    }
  });
}

// Mantén calculateRevenue si lo usas en otro lugar, si no, bórralo
  logout() {
    this.auth.logout().subscribe({
      next: () => this.router.navigate(['/login']),
      error: () => this.router.navigate(['/login'])
    });
  }
}