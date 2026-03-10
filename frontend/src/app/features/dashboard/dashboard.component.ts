import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';
import { AccommodationService } from '../../core/services/accommodation.service';
import { BookingService } from '../../core/services/booking.service';
import { GuestService } from '../../core/services/guest.service';
import { PaymentService } from '../../core/services/payment.service';
import { User } from '../../core/models/user.model';
import { IconSvgComponent } from '../../shared/components/icon-svg.component'; 

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [
    CommonModule, 
    RouterModule,
    IconSvgComponent  
  ],
  templateUrl: './dashboard.component.html',
})
export class DashboardComponent implements OnInit {
  user: User | null = null;
  
  stats = {
    accommodations: 0,
    bookings: 0,
    guests: 0,
    revenue: 0
  };

  recentBookings: any[] = [];
  recentAccommodations: any[] = [];

  constructor(
    private auth: AuthService,
    private router: Router,
    private accommodationService: AccommodationService,
    private bookingService: BookingService,
    private guestService: GuestService,
    private paymentService: PaymentService
  ) {}

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      this.user = user;
    });
    
    this.loadRealData();
  }

  loadRealData() {
    // Alojamientos
    this.accommodationService.getAll().subscribe({
      next: (res) => {
        this.stats.accommodations = res.data.length;
        this.recentAccommodations = res.data.slice(-5);
      }
    });

    // Reservas
    this.bookingService.getAll().subscribe({
      next: (res) => {
        this.stats.bookings = res.data.length;
        this.recentBookings = res.data.slice(-5);
        this.calculateRevenue();
      }
    });

    // Huéspedes
    this.guestService.getAll().subscribe({
      next: (res) => this.stats.guests = res.data.length
    });
  }

/*
    loadRealData() {
  // Reservas
  this.bookingService.getAll().subscribe({
    next: (res) => {
      console.log('RESERVAS CRUDAS:', res.data); // ← MIRA ESTO
      this.stats.bookings = res.data.length;
      this.recentBookings = res.data.slice(-5);
      this.calculateRevenue();
    }
  });

  // Alojamientos
  this.accommodationService.getAll().subscribe({
    next: (res) => {
      console.log('ALOJAMIENTOS CRUDOS:', res.data); // ← Y ESTO
      this.stats.accommodations = res.data.length;
      this.recentAccommodations = res.data.slice(-5);
    }
  });
}

*/


  calculateRevenue() {
    this.paymentService.getAll().subscribe({
      next: (res) => {
        const total = res.data.reduce((sum: number, payment: any) => 
          sum + (payment.amount || 0), 0
        );
        this.stats.revenue = Math.round(total); 
      }
    });
  }

  logout() {
    this.auth.logout().subscribe({
      next: () => this.router.navigate(['/login']),
      error: () => this.router.navigate(['/login'])
    });
  }
}