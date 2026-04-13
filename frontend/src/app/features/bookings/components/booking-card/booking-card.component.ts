import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { Booking } from '../../../../core/models/booking.model';
import { BookingStatusBadgeComponent } from '../booking-status-badge/booking-status-badge.component';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';
import { BookingService } from '../../../../core/services/booking.service'; 
import { FormsModule } from '@angular/forms'; 

@Component({
  selector: 'app-booking-card',
  standalone: true,
  imports: [
    CommonModule, 
    RouterModule, 
    BookingStatusBadgeComponent, 
    IconSvgComponent,
    FormsModule // ← PARA EL MODAL
  ],
  templateUrl: './booking-card.component.html',
})
export class BookingCardComponent {
  @Input({ required: true }) booking!: Booking;
  @Input() showActions: boolean = true;
  @Input() linkToDetail: boolean = true;
  
  @Output() bookingCancelled = new EventEmitter<number>(); // ← EMITIR EVENTO

  showCancelModal = false;
  cancelReason = '';
  selectedBookingId: number | null = null;

  constructor(private bookingService: BookingService) {} // ← INYECTAR

   downloadConfirmation(): void {
    this.bookingService.downloadConfirmation(this.booking.id).subscribe({
      next: (blob) => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `confirmacion-${this.booking.id}.pdf`;
        a.click();
        window.URL.revokeObjectURL(url);
      },
      error: (error) => {
        console.error('Error descargando confirmación:', error);
        alert('No se pudo descargar la confirmación');
      }
    });
  }

  generateInvoice(): void {
    this.bookingService.generateInvoice(this.booking.id).subscribe({
      next: () => {
        alert('Factura generada correctamente. Puedes verla en "Mis documentos"');
      },
      error: (error) => {
        if (error.status === 422) {
          alert('La factura solo se puede generar después del check-out');
        } else {
          alert('Error al generar la factura');
        }
      }
    });
  }

  formatDate(date: string): string {
    return new Date(date).toLocaleDateString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  }

  today = new Date();

  openCancelModal(bookingId: number) {
    this.selectedBookingId = bookingId;
    this.cancelReason = '';
    this.showCancelModal = true;
  }

  closeCancelModal() {
    this.showCancelModal = false;
    this.selectedBookingId = null;
    this.cancelReason = '';
  }

  confirmCancel() {
    if (!this.selectedBookingId) return;
    
    this.bookingService.cancelBooking(this.selectedBookingId, this.cancelReason).subscribe({
      next: () => {
        this.closeCancelModal();
        this.bookingCancelled.emit(this.selectedBookingId!); // ← EMITIR AL PADRE
      },
      error: (error: any) => {
        alert('Error al cancelar: ' + error.error?.message || 'Error desconocido');
      }
    });
  }

     canCancel(): boolean {
      const today = new Date().toISOString().split('T')[0];
      const checkIn = this.booking.check_in.split('T')[0];
      const statusOk = this.booking.status === 'pending' || this.booking.status === 'confirmed';
      const dateOk = checkIn >= today;
      
      console.log('Reserva ID:', this.booking.id);
      console.log('Status:', this.booking.status, '| Status OK:', statusOk);
      console.log('Check-in:', checkIn, '| Hoy:', today, '| Date OK:', dateOk);
      console.log('Resultado:', statusOk && dateOk);
      
      return statusOk && dateOk;
    }
}