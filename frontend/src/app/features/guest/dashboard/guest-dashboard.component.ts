import { Component, OnInit, AfterViewInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router, NavigationEnd } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { BookingService } from '../../../core/services/booking.service';
import { LoyaltyPointService } from '../../../core/services/loyalty-point.service';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { GreetingComponent } from '../../../shared/components/greeting/greeting.component';
import { first } from 'rxjs/operators';
import { NotificationService } from '../../../core/services/notification.service';
import { GuestService } from '../../../core/services/guest.service'; 

@Component({
  selector: 'app-guest-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent, GreetingComponent],
  templateUrl: './guest-dashboard.component.html',
})
export class GuestDashboardComponent implements OnInit, AfterViewInit {
  user: any = null;
  stats = {
    upcomingBookings: 0,
    pointsBalance: 0,
    pendingReviews: 0,
    unreadNotifications: 0
  };
  
  loading = true;
  unreadCount = 0;

  constructor(
    private auth: AuthService,
    private bookingService: BookingService,
    private loyaltyPointService: LoyaltyPointService,
    private guestService: GuestService,
    private notificationService: NotificationService,  
    private router: Router
  ) {
      this.router.events.subscribe(event => {
      if (event instanceof NavigationEnd && event.url === '/guest/dashboard') {
        this.loadUnreadCount();
      }
    });
  }

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      this.user = user;
    });
    this.loadDashboardData();
    this.loadUnreadCount();
  }

   ngAfterViewInit() {
    this.loadUnreadCount(); 
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


    loadUnreadCount() {
    this.notificationService.getAll().subscribe({
      next: (res: any) => {
        const notifications = res.data || [];
        this.unreadCount = notifications.filter((n: any) => !n.is_read).length;
      },
      error: () => {
        this.unreadCount = 0;
      }
    });
  }
}