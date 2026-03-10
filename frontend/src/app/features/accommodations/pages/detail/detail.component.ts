import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { AccommodationService } from '../../../../core/services/accommodation.service';
import { AvailabilityService } from '../../../../core/services/availability.service';
import { ReviewService } from '../../../../core/services/review.service';
import { IconSvgComponent } from '../../../../shared/components/icon-svg.component';
import { PublicService } from '../../../../core/services/public.service';

@Component({
  selector: 'app-accommodation-detail',
  standalone: true,
  imports: [
    CommonModule, 
    RouterModule,
    FormsModule,
    IconSvgComponent,
  ],
  templateUrl: './detail.component.html',
})
export class AccommodationDetailComponent implements OnInit {
  accommodation: any = null;
  availability: any[] = [];
  reviews: any[] = [];
  loading = true;
  errorMessage = '';
  
  // Para la reserva
  checkIn: string = '';
  checkOut: string = '';
  guests: number = 1;

  // Para cálculo dinámico
  nights: number = 0;
  totalPrice: number = 0;
  baseGuests: number = 2;
  extraGuestFee: number = 15;
  
  constructor(
    private route: ActivatedRoute,
    private accommodationService: AccommodationService,
    private publicService: PublicService,
    private reviewService: ReviewService,
    private availabilityService: AvailabilityService 
  ) {}

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.loadAccommodation(parseInt(id));
      this.loadAvailability(parseInt(id));
      this.loadReviews(parseInt(id));
      
      // Fechas por defecto
      const today = new Date();
      const nextWeek = new Date();
      nextWeek.setDate(today.getDate() + 7);
      
      this.checkIn = today.toISOString().split('T')[0];
      this.checkOut = nextWeek.toISOString().split('T')[0];
      this.calculateNights();
    }
  }

  loadAccommodation(id: number) {
    this.publicService.getAccommodation(id).subscribe({
      next: (response) => {
        this.accommodation = response.data;
        this.loading = false;
        this.calculateNights();
      },
      error: (error) => {
        this.errorMessage = 'Error al cargar la propiedad';
        this.loading = false;
      }
    });
  }

  loadAvailability(id: number) {
    this.publicService.getAvailability(id).subscribe({
      next: (response) => {
        this.availability = response.data;
      },
      error: (error) => {
        console.error('Error cargando disponibilidad:', error);
      }
    });
  }

  loadReviews(id: number) {
    this.publicService.getReviews(id).subscribe({
      next: (response) => {
        this.reviews = response.data;
      },
      error: (error) => {
        console.error('Error cargando reseñas:', error);
      }
    });
  }

  calculateAverageRating(): number {
    if (!this.reviews.length) return 0;
    const sum = this.reviews.reduce((acc, review) => acc + review.rating, 0);
    return Math.round((sum / this.reviews.length) * 10) / 10;
  }

  checkAvailability() {
    console.log('Verificando disponibilidad:', this.checkIn, this.checkOut);
  }

  bookNow() {
    console.log('Reservando:', this.accommodation?.id, this.checkIn, this.checkOut);
  }

  calculateNights() {
    if (this.checkIn && this.checkOut) {
      const start = new Date(this.checkIn);
      const end = new Date(this.checkOut);
      const diffTime = Math.abs(end.getTime() - start.getTime());
      this.nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      this.calculateTotal();
    }
  }

  calculateTotal() {
    if (this.accommodation && this.nights > 0) {
      const basePriceTotal = this.accommodation.base_price * this.nights;
      
      let extraGuestsTotal = 0;
      if (this.guests > this.baseGuests) {
        const extraGuests = this.guests - this.baseGuests;
        extraGuestsTotal = extraGuests * this.extraGuestFee * this.nights;
      }
      
      this.totalPrice = basePriceTotal + 
                        extraGuestsTotal + 
                        (this.accommodation.cleaning_fee || 0);
    }
  }

  onGuestsChange() {
    console.log('Huéspedes:', this.guests);
    this.calculateTotal();
  }

  onDateChange() {
    this.calculateNights();
  }
}