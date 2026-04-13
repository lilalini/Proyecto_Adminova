import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { BookingService } from '../../../core/services/booking.service';
import { LoyaltyPointService } from '../../../core/services/loyalty-point.service';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { GreetingComponent } from '../../../shared/components/greeting/greeting.component';
import { first } from 'rxjs/operators';
import { GuestService } from '../../../core/services/guest.service'; 

@Component({
  selector: 'app-guest-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent, GreetingComponent],
  templateUrl: './guest-dashboard.component.html',
})
export class GuestDashboardComponent implements OnInit {
  user: any = null;
  stats = {
    upcomingBookings: 0,
    pointsBalance: 0,
    pendingReviews: 0,
    unreadNotifications: 0
  };
  
  loading = true;

  constructor(
    private auth: AuthService,
    private bookingService: BookingService,
    private loyaltyPointService: LoyaltyPointService,
    private guestService: GuestService 
  ) {}

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      this.user = user;
    });
    this.loadDashboardData();
  }

  loadDashboardData() {
    const today = new Date().toISOString().split('T')[0];
    
    // Cargar reservas
    this.bookingService.getMyBookings().subscribe({
      next: (res) => {
        this.stats.upcomingBookings = res.data.filter(b => 
          b.check_in >= today && b.status !== 'cancelled'
        ).length;
        this.loading = false;
      },
      error: () => {
        this.loading = false;
      }
    });

    // Cargar puntos usando guest
    this.auth.currentUser$.pipe(first()).subscribe(user => {
      if (user) {
        this.guestService.getByUserId(user.id).subscribe({
          next: (response: any) => {
            const guest = response.data;
            if (guest) {
              this.loyaltyPointService.getBalance(guest.id).subscribe({
                next: (balance) => {
                  this.stats.pointsBalance = balance.balance;
                },
                error: (err) => console.error('Error cargando puntos:', err)
              });
            }
          },
          error: (err) => console.error('Error buscando guest:', err)
        });
      }
    });
  }
}