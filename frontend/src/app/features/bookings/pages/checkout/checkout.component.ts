import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, RouterModule, Router } from '@angular/router';
import { BookingService } from '../../../../core/services/booking.service';
import { PublicService } from '../../../../core/services/public.service';
import { AuthService } from '../../../../core/services/auth.service';
import { Accommodation } from '../../../../core/models/accommodation.model';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';
import { BookingResponseWithToken } from '../../../../core/models/booking.model';
import { GuestService } from '../../../../core/services/guest.service';

@Component({
  selector: 'app-checkout',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, IconSvgComponent],
  templateUrl: './checkout.component.html',
})
export class CheckoutComponent implements OnInit {
  accommodation: Accommodation | null = null;
  accommodationId!: number;
  
  checkIn: string = '';
  checkOut: string = '';
  guests: number = 1;
  nights: number = 0;
  totalPrice: number = 0;
  
  guestData = {
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    special_requests: ''
  };
  
  loading = true;
  submitting = false;
  errorMessage = '';
  showAuthModal = false; 
  showTermsModal = false;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private publicService: PublicService,
    private bookingService: BookingService,
    public auth: AuthService,
    private guestService: GuestService
  ) {}

  ngOnInit() {
    this.accommodationId = Number(this.route.snapshot.queryParamMap.get('id'));
    this.checkIn = this.route.snapshot.queryParamMap.get('checkIn') || '';
    this.checkOut = this.route.snapshot.queryParamMap.get('checkOut') || '';
    this.guests = Number(this.route.snapshot.queryParamMap.get('guests')) || 1;
    this.nights = Number(this.route.snapshot.queryParamMap.get('nights')) || 0;
    this.totalPrice = Number(this.route.snapshot.queryParamMap.get('totalPrice')) || 0;

    if (!this.accommodationId || !this.checkIn || !this.checkOut) {
      this.router.navigate(['/']);
      return;
    }

    this.loadAccommodation();
    this.loadGuestData();
  }

  loadAccommodation() {
    this.publicService.getAccommodation(this.accommodationId).subscribe({
      next: (response) => {
        this.accommodation = response.data;
        this.loading = false;
      },
      error: () => {
        this.errorMessage = 'Error al cargar el alojamiento';
        this.loading = false;
      }
    });
  }

    loadGuestData() {
      this.auth.currentUser$.subscribe(user => {
        if (user && user.email) {
          // Buscar el guest por email
          this.guestService.getByEmail(user.email).subscribe({
            next: (response) => {
              const guest = response.data[0];
              if (guest) {
                this.guestData.first_name = guest.first_name;
                this.guestData.last_name = guest.last_name;
                this.guestData.email = guest.email;
                this.guestData.phone = guest.phone || '';
              }
            },
            error: () => {
              // Si no hay guest, usar datos del usuario (solo email)
              this.guestData.email = user.email;
            }
          });
        }
      });
    }

  onSubmit() {
    if (!this.auth.isAuthenticated()) {
      this.showAuthModal = true;
      return;
    }

    if (!this.guestData.first_name || !this.guestData.last_name || !this.guestData.email) {
      alert('Por favor, completa todos los campos obligatorios');
      return;
    }

    const bookingData = {
      accommodation_id: this.accommodationId,
      guest_name: `${this.guestData.first_name} ${this.guestData.last_name}`,
      guest_email: this.guestData.email,
      guest_phone: this.guestData.phone,
      check_in: this.checkIn,
      check_out: this.checkOut,
      adults: this.guests,
      children: 0,
      infants: 0,
      pets: 0,
      total_amount: this.totalPrice,
      status: 'pending' as 'pending',
      special_requests: this.guestData.special_requests,
      nights: this.nights,
      price_per_night: this.accommodation?.base_price || 0,
      base_price: (this.accommodation?.base_price || 0) * this.nights,
      balance_due: this.totalPrice
    };

    this.submitting = true;

    this.bookingService.create<BookingResponseWithToken>(bookingData).subscribe({
      next: (response: BookingResponseWithToken) => {
        localStorage.setItem('auth_token', response.token);
        this.router.navigate(['/payment', response.data.id]);
      },
      error: (error) => {
        console.error('Error al crear reserva:', error);
        this.errorMessage = 'Error al procesar la reserva';
        this.submitting = false;
      }
    });
  }

  goToLogin() {
    localStorage.setItem('redirectAfterLogin', this.router.url);
    this.router.navigate(['/login']);
  }

  goToRegister() {
    localStorage.setItem('redirectAfterLogin', this.router.url);
    this.router.navigate(['/register']);
  }

  closeAuthModal() {
    this.showAuthModal = false;
  }

  openTermsModal() {
    this.showTermsModal = true;
  }

  closeTermsModal() {
    this.showTermsModal = false;
  }
}