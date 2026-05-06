import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { Chart, registerables } from 'chart.js';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { GreetingComponent } from '../../../shared/components/greeting/greeting.component';
import { AuthService } from '../../../core/services/auth.service';
import { BookingService } from '../../../core/services/booking.service';
import { PaymentService } from '../../../core/services/payment.service';
import { getBookingStatus } from '../../../shared/utils/booking-status.util';

Chart.register(...registerables);

@Component({
  selector: 'app-analytics',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent, GreetingComponent],
  templateUrl: './analytics.component.html',
})
export class AnalyticsComponent implements OnInit {
  user: any = null;
  loading = true;

  revenuePercentage = 0;
  revenueTrend: 'up' | 'down' = 'up';
  bookingPercentage = 0;
  bookingTrend: 'up' | 'down' = 'up';
  averagePercentage = 0;
  averageTrend: 'up' | 'down' = 'up';
  revenueMessage = '';
  bookingMessage = '';
  averageMessage = '';
  
  stats = {
    totalRevenue: 0,
    totalBookings: 0,
    averageBooking: 0
  };
  
  bookingStatus = {
    pending: 0,
    confirmed: 0,
    cancelled: 0,
    completed: 0
  };

  recentBookings: any[] = [];
  monthlyRevenue: number[] = [];
  months: string[] = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

  constructor(
    private auth: AuthService,
    private bookingService: BookingService,
    private paymentService: PaymentService
  ) {}

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      this.user = user;
    });
    this.loadData();
  }

loadData() {
  let completed = 0;
  const total = 2;

  const checkComplete = () => {
    completed++;
    if (completed === total) {
      this.loadMonthlyRevenue();
      this.loadComparisons();
    }
  };

  // Cargar reservas
  this.bookingService.getAll().subscribe({
    next: (res: any) => {
      // Obtener el array de reservas
      const bookingsArray = res.data?.data || res.data || [];
      
      this.stats.totalBookings = bookingsArray.length;
      this.recentBookings = bookingsArray.slice(-5).reverse();
      
      this.bookingStatus.pending = bookingsArray.filter((b: any) => b.status === 'pending').length;
      this.bookingStatus.confirmed = bookingsArray.filter((b: any) => b.status === 'confirmed').length;
      this.bookingStatus.cancelled = bookingsArray.filter((b: any) => b.status === 'cancelled').length;
      this.bookingStatus.completed = bookingsArray.filter((b: any) => b.status === 'completed').length;
      
      console.log('Reservas reales:', this.stats.totalBookings);
      checkComplete();
    },
    error: (error) => {
      console.error('Error cargando reservas:', error);
      checkComplete();
    }
  });

  // Cargar ingresos totales
  this.paymentService.getTotalRevenue().subscribe({
    next: (res: any) => {
      this.stats.totalRevenue = res.total || 0;
      this.stats.averageBooking = this.stats.totalBookings > 0 
        ? Math.round(this.stats.totalRevenue / this.stats.totalBookings) 
        : 0;
      checkComplete();
    },
    error: (error) => {
      console.error('Error cargando ingresos:', error);
      checkComplete();
    }
  });
}

  loadMonthlyRevenue() {
    this.paymentService.getMonthlyRevenue().subscribe({
      next: (data) => {
        this.monthlyRevenue = data;
        this.loading = false;
        setTimeout(() => this.createCharts(), 100);
      },
      error: (error) => {
        console.error('Error cargando ingresos mensuales:', error);
        this.loading = false;
        // Si falla, usar datos simulados o mantener array vacío
        this.monthlyRevenue = [12500, 14200, 16800, 15400, 18900, 21200, 23500, 22400, 19800, 17600, 15800, 14300];
        setTimeout(() => this.createCharts(), 100);
      }
    });
  }


loadComparisons() {
  // Ingresos
  this.paymentService.getMonthlyComparison().subscribe({
    next: (res) => {
      if (res.previous_month === 0 && res.current_month > 0) {
        this.revenueMessage = 'Primer mes con ingresos';
        this.revenueTrend = 'up';
        this.revenuePercentage = 0;
      } else if (res.current_month === 0) {
        this.revenueMessage = 'Sin ingresos este mes';
        this.revenueTrend = 'down';
        this.revenuePercentage = 0;
      } else {
        this.revenueMessage = '';
        this.revenuePercentage = res.percentage;
        this.revenueTrend = res.trend as 'up' | 'down';
      }
    },
    error: (err) => console.error('Error cargando comparación ingresos:', err)
  });

  // Reservas
  this.bookingService.getBookingComparison().subscribe({
    next: (res) => {
      if (res.previous_month === 0 && res.current_month > 0) {
        this.bookingMessage = 'Primer mes con reservas';
        this.bookingTrend = 'up';
        this.bookingPercentage = 0;
      } else if (res.current_month === 0) {
        this.bookingMessage = 'Sin reservas este mes';
        this.bookingTrend = 'down';
        this.bookingPercentage = 0;
      } else {
        this.bookingMessage = '';
        this.bookingPercentage = res.percentage;
        this.bookingTrend = res.trend as 'up' | 'down';
      }
    },
    error: (err) => console.error('Error cargando comparación reservas:', err)
  });

  // Ticket medio
  this.bookingService.getAverageComparison().subscribe({
    next: (res) => {
      if (res.previous_month === 0 && res.current_month > 0) {
        this.averageMessage = 'Primer mes con ticket medio';
        this.averageTrend = 'up';
        this.averagePercentage = 0;
      } else if (res.current_month === 0) {
        this.averageMessage = 'Sin datos este mes';
        this.averageTrend = 'down';
        this.averagePercentage = 0;
      } else {
        this.averageMessage = '';
        this.averagePercentage = res.percentage;
        this.averageTrend = res.trend as 'up' | 'down';
      }
    },
    error: (err) => console.error('Error cargando comparación ticket:', err)
  });
}

  createCharts() {
    // Gráfico de ingresos mensuales
    new Chart('revenueChart', {
      type: 'line',
      data: {
        labels: this.months,
        datasets: [{
          label: 'Ingresos (€)',
          data: this.monthlyRevenue,
          borderColor: '#4f46e5',
          backgroundColor: 'rgba(79, 70, 229, 0.1)',
          tension: 0.3,
          fill: true,
          pointBackgroundColor: '#4f46e5',
          pointBorderColor: '#fff',
          pointRadius: 4,
          pointHoverRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'top' },
          tooltip: { 
            callbacks: { 
              label: (context: any) => `€ ${context.raw.toLocaleString()}` 
            } 
          }
        }
      }
    });

    // Gráfico de reservas por estado
    new Chart('statusChart', {
      type: 'doughnut',
      data: {
        labels: ['Pendientes', 'Confirmadas', 'Canceladas', 'Completadas'],
        datasets: [{
          data: [this.bookingStatus.pending, this.bookingStatus.confirmed, 
                  this.bookingStatus.cancelled, this.bookingStatus.completed],
          backgroundColor: ['#f59e0b', '#10b981', '#ef4444', '#3b82f6'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    });
  }


  getStatus(booking: any): string {
    return getBookingStatus(booking);
  }

  getStatusClass(status: string): string {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    checked_in: 'bg-purple-100 text-purple-800',
    checked_out: 'bg-gray-100 text-gray-800'
  };

  return classes[status] || 'bg-gray-100 text-gray-800';
}

getStatusText(status: string): string {
  const texts: Record<string, string> = {
    pending: 'Pendiente',
    confirmed: 'Confirmada',
    cancelled: 'Cancelada',
    checked_in: 'Check-in',
    checked_out: 'Check-out'
  };

  return texts[status] || status;
}


}