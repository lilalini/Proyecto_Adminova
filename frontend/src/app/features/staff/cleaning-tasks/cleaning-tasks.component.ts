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
}