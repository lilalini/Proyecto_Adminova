import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { User } from '../../../core/models/user.model';

@Component({
  selector: 'app-greeting',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="bg-white rounded-xl shadow-sm p-4">
      <h1 class="text-xl font-semibold text-gray-800">
        {{ getGreeting() }}, {{ user?.first_name + ' ' + user?.last_name || 'Usuario' }}
      </h1>
      <p class="text-sm text-gray-500 mt-0.5">
        {{ today | date:'EEEE, d MMMM y' }}
      </p>
    </div>
  `
})
export class GreetingComponent {
  @Input() user: User | null = null;
  today = new Date();

  getGreeting(): string {
    const hour = new Date().getHours();
    if (hour >= 6 && hour < 12) return 'Buenos días';
    if (hour >= 12 && hour < 20) return 'Buenas tardes';
    return 'Buenas noches';
  }
}