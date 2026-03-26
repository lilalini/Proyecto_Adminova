import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-notifications',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent],
  templateUrl: './notifications.component.html',
})
export class NotificationsComponent implements OnInit {
  notifications: any[] = [];
  loading = true;
  unreadCount = 0; // ← AÑADIR

  ngOnInit() {
    this.loadNotifications();
  }

  loadNotifications() {
    setTimeout(() => {
      this.notifications = [
        {
          id: 1,
          title: 'Reserva confirmada',
          message: 'Tu reserva en el apartamento de Madrid ha sido confirmada',
          date: '2026-03-20T10:30:00',
          read: false,
          icon: 'calendar-check'
        },
        {
          id: 2,
          title: 'Pago recibido',
          message: 'Hemos recibido el pago de tu reserva #BKG-12345',
          date: '2026-03-19T15:20:00',
          read: true,
          icon: 'wallet'
        },
        {
          id: 3,
          title: 'Recordatorio de check-in',
          message: 'Tu check-in en Barcelona es dentro de 2 días',
          date: '2026-03-18T09:00:00',
          read: false,
          icon: 'bell'
        }
      ];
      this.unreadCount = this.notifications.filter(n => !n.read).length; // ← CALCULAR
      this.loading = false;
    }, 500);
  }

  markAsRead(notificationId: number) {
    const notification = this.notifications.find(n => n.id === notificationId);
    if (notification && !notification.read) {
      notification.read = true;
      this.unreadCount = this.notifications.filter(n => !n.read).length; // ← RECALCULAR
    }
  }

  formatDate(date: string): string {
    const d = new Date(date);
    const now = new Date();
    const diff = now.getTime() - d.getTime();
    const hours = Math.floor(diff / (1000 * 60 * 60));
    
    if (hours < 24) {
      if (hours < 1) return 'Hace unos minutos';
      return `Hace ${hours} ${hours === 1 ? 'hora' : 'horas'}`;
    }
    
    return d.toLocaleDateString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  }
}