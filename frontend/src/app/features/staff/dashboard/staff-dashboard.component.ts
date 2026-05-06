import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { BookingService } from '../../../core/services/booking.service';
import { CleaningTaskService } from '../../../core/services/cleaning-task.service';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { GreetingComponent } from '../../../shared/components/greeting/greeting.component';

@Component({
  selector: 'app-staff-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent, GreetingComponent],
  templateUrl: './staff-dashboard.component.html',
})
export class StaffDashboardComponent implements OnInit {
  user: any = null;
  stats = {
    todayCheckOuts: 0,
    tomorrowCheckIns: 0,
    pendingTasks: 0
  };
  todayBookings: any[] = [];
  pendingTasks: any[] = [];
  checkOutsToday: any[] = [];
  checkInsTomorrow: any[] = [];
  loading = true;
  today = new Date().toISOString().split('T')[0];

  constructor(
    private auth: AuthService,
    private bookingService: BookingService,
    private cleaningTaskService: CleaningTaskService
  ) {}

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      this.user = user;
    });
    this.loadData();
  }

  loadData() {
    const today = new Date().toISOString().split('T')[0];
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    
    // Obtener reservas
    this.bookingService.getAll().subscribe({
      next: (res) => {
        // Check-outs de hoy (necesitan limpieza)
        this.checkOutsToday = res.data.filter((b: any) => b.check_out === today);
        
        // Check-ins de mañana (necesitan estar listos)
        this.checkInsTomorrow = res.data.filter((b: any) => b.check_in === tomorrowStr);
        
        this.stats.todayCheckOuts = this.checkOutsToday.length;
        this.stats.tomorrowCheckIns = this.checkInsTomorrow.length;
        this.loading = false;
      },
      error: () => {
        this.loading = false;
      }
    });
    
    // Tareas de limpieza pendientes
    this.cleaningTaskService.getTodayTasks().subscribe({
      next: (res) => {
        this.stats.pendingTasks = res.data.filter((t: any) => t.status === 'pending').length;
        this.pendingTasks = res.data;
      },
      error: (err) => {
        console.error('Error cargando tareas:', err);
      }
    });
  }

  formatDate(date: string): string {
    return new Date(date).toLocaleDateString('es-ES');
  }
}