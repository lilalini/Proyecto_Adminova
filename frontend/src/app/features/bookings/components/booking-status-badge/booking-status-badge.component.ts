import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-booking-status-badge',
  standalone: true,
  imports: [CommonModule],
  template: `
    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ getClass() }}">
      {{ getText() }}
    </span>
  `
})
export class BookingStatusBadgeComponent {
  @Input() status: string = '';

  getClass(): string {
    const classes: Record<string, string> = {
      'pending': 'bg-yellow-100 text-yellow-800',
      'confirmed': 'bg-green-100 text-green-800',
      'cancelled': 'bg-red-100 text-red-800',
      'completed': 'bg-blue-100 text-blue-800'
    };
    return classes[this.status] || 'bg-gray-100 text-gray-800';
  }

  getText(): string {
    const texts: Record<string, string> = {
      'pending': 'Pendiente',
      'confirmed': 'Confirmada',
      'cancelled': 'Cancelada',
      'completed': 'Completada'
    };
    return texts[this.status] || this.status;
  }
}