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
import { DashboardUpdateService } from '../../../core/services/dashboard-update.service';

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

  revenuePercentage = 0;
  revenueTrend: 'up' | 'down' = 'up';
  
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
    private paymentService: PaymentService,
    private dashboardUpdate: DashboardUpdateService
  ) {}

  today = new Date();

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      this.user = user;
    });
    this.loadRealData();

      // Escuchar cambios para recargar datos
    this.dashboardUpdate.refresh$.subscribe(shouldRefresh => {
      if (shouldRefresh) {
        this.loadRealData();
      }
    });
  }

    loadRealData() {
      let completedRequests = 0;
      const totalRequests = 4;

      const checkComplete = () => {
        completedRequests++;
        if (completedRequests === totalRequests) {
          this.loading = false;
          this.loadComparisons();
        }
      };

      // Alojamientos (ordenados por fecha descendente)
      this.accommodationService.getAll().subscribe({
        next: (res) => {
          this.stats.accommodations = res.meta?.total || res.data.length;
          // Ordenar por created_at descendente (más recientes primero)
          const sorted = [...res.data].sort((a, b) => 
            new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
          );
          this.recentAccommodations = sorted.slice(0, 5);
          checkComplete();
        },
        error: () => checkComplete()
      });

      // Reservas (ordenadas por fecha descendente)
      this.bookingService.getAll().subscribe({
        next: (res) => {
          this.stats.bookings = res.meta?.total || res.data.length;
// Ordenar por id descendente (mayor ID = más reciente)
    const sorted = [...res.data].sort((a, b) => b.id - a.id);
    this.recentBookings = sorted.slice(0, 5);
    checkComplete();
        },
        error: () => checkComplete()
      });

      // Huéspedes
      this.guestService.getAll().subscribe({
        next: (res) => {
          this.stats.guests = res.meta?.total || res.data.length;
          checkComplete();
        },
        error: () => checkComplete()
      });

      // Pagos (ingresos)
      this.paymentService.getTotalRevenue().subscribe({
        next: (res) => {
          this.stats.revenue = res.total;
          checkComplete();
        },
        error: () => checkComplete()
      });
    }

    loadComparisons() {
      this.paymentService.getRevenueComparison().subscribe({
        next: (res) => {
          this.revenuePercentage = res.percentage;
          this.revenueTrend = res.trend as 'up' | 'down';
        },
        error: (err) => console.error('Error cargando comparación ingresos:', err)
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