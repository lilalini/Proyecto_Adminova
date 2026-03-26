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
    todayCheckIns: 0,
    todayCheckOuts: 0,
    pendingTasks: 0
  };
  todayBookings: any[] = [];
  pendingTasks: any[] = [];
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
    
    this.bookingService.getAll().subscribe({
      next: (res) => {
        this.todayBookings = res.data.filter(b => 
          b.check_in === today || b.check_out === today
        );
        this.stats.todayCheckIns = this.todayBookings.filter(b => b.check_in === today).length;
        this.stats.todayCheckOuts = this.todayBookings.filter(b => b.check_out === today).length;
        this.loading = false;
      },
      error: () => {
        this.loading = false;
      }
    });
    
    // Tareas de limpieza (independiente)
    this.cleaningTaskService.getTodayTasks().subscribe({
      next: (res) => {
        this.stats.pendingTasks = res.data.filter(t => t.status === 'pending').length;
        this.pendingTasks = res.data;
      },
      error: (err) => {
        console.error('Error cargando tareas:', err);
        this.stats.pendingTasks = 0;
      }
    });
  }

  formatDate(date: string): string {
    return new Date(date).toLocaleDateString('es-ES');
  }
}