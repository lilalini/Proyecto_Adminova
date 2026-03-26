import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { Chart, registerables } from 'chart.js';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { GreetingComponent } from '../../../shared/components/greeting/greeting.component';
import { AuthService } from '../../../core/services/auth.service';
import { BookingService } from '../../../core/services/booking.service';
import { PaymentService } from '../../../core/services/payment.service';

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
    const total = 2; // bookings y payments

    const checkComplete = () => {
      completed++;
      if (completed === total) {
        this.loading = false;
        setTimeout(() => this.createCharts(), 100);
      }
    };

    // Cargar reservas
    this.bookingService.getAll().subscribe({
      next: (res) => {
        const bookings = res.data;
        this.stats.totalBookings = bookings.length;
        this.recentBookings = bookings.slice(-5).reverse();
        
        this.bookingStatus.pending = bookings.filter(b => b.status === 'pending').length;
        this.bookingStatus.confirmed = bookings.filter(b => b.status === 'confirmed').length;
        this.bookingStatus.cancelled = bookings.filter(b => b.status === 'cancelled').length;
        this.bookingStatus.completed = bookings.filter(b => b.status === 'completed').length;
        
        // Datos mensuales (simulados, mejorar con datos reales)
        this.monthlyRevenue = [12500, 14200, 16800, 15400, 18900, 21200, 23500, 22400, 19800, 17600, 15800, 14300];
        checkComplete();
      }
    });

    // Cargar pagos
    this.paymentService.getAll().subscribe({
      next: (res) => {
        const total = res.data.reduce((sum, p) => sum + (p.amount || 0), 0);
        this.stats.totalRevenue = Math.round(total);
        this.stats.averageBooking = this.stats.totalBookings > 0 
          ? Math.round(this.stats.totalRevenue / this.stats.totalBookings) 
          : 0;
        checkComplete();
      }
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

}