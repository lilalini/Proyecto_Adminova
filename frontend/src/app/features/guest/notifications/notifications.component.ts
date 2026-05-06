import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { NotificationService } from '../../../core/services/notification.service';
import { getRelativeDate } from '../../../shared/utils/booking-status.util';
import { BackButtonComponent } from '../../../shared/components/back-button/back-button.component';

@Component({
  selector: 'app-notifications',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent, BackButtonComponent],
  templateUrl: './notifications.component.html',
})
export class NotificationsComponent implements OnInit {
  notifications: any[] = [];
  loading = true;
  unreadCount = 0;
  currentPage = 1;
  totalPages = 1;
  perPage = 5;

  constructor(private notificationService: NotificationService) {}

  ngOnInit() {
    this.loadNotifications();
  }

  loadNotifications() {
    this.notificationService.getAll(this.currentPage).subscribe({
      next: (res: any) => {

        console.log('Respuesta completa:', res);
        console.log('Meta:', res.meta);
        console.log('Total pages:', res.meta?.last_page);

        this.notifications = res.data || [];
        this.totalPages = res.meta?.last_page || 1;
        this.unreadCount = this.notifications.filter((n: any) => !n.is_read).length;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error cargando notificaciones:', err);
        this.loading = false;
      }
    });
  }

  goToPage(page: number) {
    if (page < 1 || page > this.totalPages) return;
    this.currentPage = page;
    this.loading = true;
    this.loadNotifications();
  }


  getIconForNotification(notification: any): string {
    const type = notification.type || '';
    if (type.includes('booking') || type.includes('reserva')) return 'calendar-check';
    if (type.includes('payment') || type.includes('pago')) return 'wallet';
    if (type.includes('reminder') || type.includes('recordatorio')) return 'bell';
    return 'bell';
  }

  markAllAsRead() {
    this.notificationService.markAllAsRead().subscribe({
      next: () => {

        this.notifications = this.notifications.map(n => ({
          ...n,
          is_read: 1
        }));

        this.unreadCount = 0;
      },
      error: (err) => console.error('Error:', err)
    });
  }

markAsRead(notificationId: number) {
  console.log('Click detectado, ID:', notificationId);
  this.notificationService.markAsRead(notificationId).subscribe({
    next: () => {
      console.log('Marcada como leída en backend');
      const notification = this.notifications.find(n => n.id === notificationId);
      if (notification) {
        notification.is_read = true;
        this.unreadCount = this.notifications.filter(n => !n.is_read).length;
        console.log('unreadCount actualizado:', this.unreadCount);
      }
    },
    error: (err) => console.error('Error:', err)
  });
}

  formatDate(date: string): string {
    return getRelativeDate(date);
  }
}