import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { BookingService } from '../../../core/services/booking.service';
import { LoyaltyPointService } from '../../../core/services/loyalty-point.service';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { GreetingComponent } from '../../../shared/components/greeting/greeting.component';
import { first } from 'rxjs/operators';
import { NotificationService } from '../../../core/services/notification.service';
import { GuestService } from '../../../core/services/guest.service'; 
import { forkJoin } from 'rxjs';
import { ReviewService } from '../../../core/services/review.service';

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
  unreadCount = 0;

  constructor(
    private auth: AuthService,
    private bookingService: BookingService,
    private loyaltyPointService: LoyaltyPointService,
    private guestService: GuestService,
    private notificationService: NotificationService,  
    private reviewService: ReviewService
  ) {}

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      this.user = user;
      if (user) {
        this.loadDashboardData();
        this.loadUnreadCount();
      }
    });
  }

  loadDashboardData() {
    const today = new Date().toISOString().split('T')[0];
    
    // Obtener guest ID primero
    this.guestService.getByUserId(this.user.id).pipe(first()).subscribe({
      next: (response: any) => {
        const guest = response.data;
        if (guest) {
          // Cargar todo en paralelo
          forkJoin({
            bookings: this.bookingService.getMyBookings(),
            reviews: this.reviewService.getMyReviews(),
            points: this.loyaltyPointService.getBalance(guest.id)
          }).subscribe({
            next: ({ bookings, reviews, points }) => {
              const reviewedBookingIds = new Set(reviews.data.map((r: any) => r.booking_id));
              
              this.stats.upcomingBookings = bookings.data.filter((b: any) => 
                b.check_in >= today && b.status !== 'cancelled'
              ).length;
              
              this.stats.pendingReviews = bookings.data.filter((b: any) => 
                b.status === 'checked_out' && !reviewedBookingIds.has(b.id)
              ).length;
              
              this.stats.pointsBalance = points.balance;
              this.loading = false;
            },
            error: (err) => {
              console.error('Error cargando datos:', err);
              this.loading = false;
            }
          });
        } else {
          this.loading = false;
        }
      },
      error: (err) => {
        console.error('Error buscando guest:', err);
        this.loading = false;
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