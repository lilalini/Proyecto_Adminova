import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms'; 
import { ActivatedRoute, RouterModule, Router } from '@angular/router';
import { BookingService } from '../../../core/services/booking.service';
import { Booking } from '../../../core/models/booking.model';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-payment',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, IconSvgComponent],
  templateUrl: './payment.component.html',
})
export class PaymentComponent implements OnInit {
  booking: Booking | null = null;
  bookingId!: number;
  loading = true;
  processing = false;
  errorMessage = '';

  // Simulación de métodos de pago
  paymentMethods = [
    { id: 'card', name: 'Tarjeta de crédito/débito', icon: 'credit-card' },
    { id: 'paypal', name: 'PayPal', icon: 'paypal' },
    { id: 'transfer', name: 'Transferencia bancaria', icon: 'bank' }
  ];
  
  selectedMethod = 'card';

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private bookingService: BookingService
  ) {}

  ngOnInit() {
    this.bookingId = Number(this.route.snapshot.paramMap.get('id'));
    if (!this.bookingId) {
      this.router.navigate(['/']);
      return;
    }
    this.loadBooking();
  }

  loadBooking() {
    this.bookingService.getOne(this.bookingId).subscribe({
      next: (response) => {
        this.booking = response.data;
        this.loading = false;
      },
      error: (error) => {
        console.error('Error cargando reserva:', error);
        this.errorMessage = 'No se pudo cargar la reserva';
        this.loading = false;
      }
    });
  }

  processPayment() {
    this.processing = true;
    
    // Simular proceso de pago (2 segundos)
    setTimeout(() => {
      // 90% de éxito para simular
      const success = Math.random() < 0.9;
      
      if (success) {
        // Confirmar reserva en backend
        this.bookingService.confirmPayment(this.bookingId).subscribe({
          next: () => {
            this.router.navigate(['/bookings', this.bookingId, 'confirmation']);
          },
          error: (error) => {
            console.error('Error confirmando pago:', error);
            this.errorMessage = 'Error al confirmar el pago';
            this.processing = false;
          }
        });
      } else {
        this.errorMessage = 'El pago ha fallado. Inténtalo de nuevo.';
        this.processing = false;
      }
    }, 2000);
  }
}