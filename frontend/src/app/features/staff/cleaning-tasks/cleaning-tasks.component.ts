import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { CleaningTaskService, CleaningTask } from '../../../core/services/cleaning-task.service';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { SkeletonComponent } from '../../../shared/components/skeleton/skeleton.component';

@Component({
  selector: 'app-cleaning-tasks',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent, SkeletonComponent],
  templateUrl: './cleaning-tasks.component.html'
})
export class CleaningTasksComponent implements OnInit {
  tasks: CleaningTask[] = [];
  loading = true;

  constructor(private cleaningTaskService: CleaningTaskService) {}

  ngOnInit() {
    this.loadTasks();
  }

  loadTasks() {
    this.cleaningTaskService.getTodayTasks().subscribe({
      next: (res) => {
        this.tasks = res.data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error cargando tareas:', err);
        this.loading = false;
      }
    });
  }

  markAsDone(taskId: number) {
    this.cleaningTaskService.verify(taskId).subscribe({
      next: () => {
        this.loadTasks(); // Recargar la lista
      },
      error: (err) => {
        console.error('Error al completar tarea:', err);
      }
    });
  }

    getPriorityClass(priority: string): string {
    const classes: Record<string, string> = {
      low: 'bg-green-100 text-green-800',
      medium: 'bg-yellow-100 text-yellow-800',
      high: 'bg-orange-100 text-orange-800',
      urgent: 'bg-red-100 text-red-800'
    };
    return classes[priority] || 'bg-gray-100 text-gray-800';
  }
}