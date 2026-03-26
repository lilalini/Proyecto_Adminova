import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, RouterModule, Router } from '@angular/router';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';
import { UploadComponent } from '../../components/upload/upload.component';
import { MapComponent } from '../../components/map/map.component';
import { PublicService } from '../../../../core/services/public.service';
import { AuthService } from '../../../../core/services/auth.service';
import { BookingService } from '../../../../core/services/booking.service';
import { Accommodation } from '../../../../core/models/accommodation.model';
import { Review } from '../../../../core/models/review.model';
import { AvailabilityCalendar } from '../../../../core/models/availability-calendar.model';
import { User } from '../../../../core/models/user.model';
import { Media } from '../../../../core/models/media.model';
import { BookingResponse } from '../../../../core/models/booking.model';
import { WeatherService } from '../../../../core/services/weather.service';
import { SkeletonComponent } from '../../../../shared/components/skeleton/skeleton.component';

@Component({
  selector: 'app-accommodation-detail',
  standalone: true,
  imports: [
    CommonModule, 
    RouterModule,
    FormsModule,
    IconSvgComponent,
    UploadComponent,
    MapComponent,
    SkeletonComponent,
  ],
  templateUrl: './detail.component.html',
})
export class AccommodationDetailComponent implements OnInit {
  accommodation: Accommodation | null = null;
  user: User | null = null;
  availability: AvailabilityCalendar[] = [];
  reviews: Review[] = [];
  loading = true;
  errorMessage = '';
  uploadedUrl: string = '';
  
  // Para la reserva
  checkIn: string = '';
  checkOut: string = '';
  guests: number = 1;

  // Para cálculo dinámico
  nights: number = 0;
  totalPrice: number = 0;
  baseGuests: number = 2;
  extraGuestFee: number = 15;

  // Para el modal del carrusel
  showModal = false;
  modalCurrentIndex = 0;
  modalCurrentImage = '';

  // Clima
  weather: any = null;
  forecast: any[] = [];
  weatherLoading = true;
  weatherError = false;
  showForecast = false;
  
  constructor(
    private route: ActivatedRoute,
    private publicService: PublicService,
    private auth: AuthService,
    private bookingService: BookingService,
    private router: Router,
    private weatherService: WeatherService
  ) {}

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      this.user = user;
    });

    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.loadAccommodation(parseInt(id));
      this.loadAvailability(parseInt(id));
      this.loadReviews(parseInt(id));
      
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
        
        // Cargar clima después de tener coordenadas
        if (this.accommodation?.latitude && this.accommodation?.longitude) {
          this.loadWeather(this.accommodation.latitude, this.accommodation.longitude);
        }
      },
      error: (error) => {
        if (error.status === 404) {
          this.router.navigate(['/404']);
        } else {
          this.errorMessage = 'Error al cargar la propiedad';
          this.loading = false;
        }
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

loadWeather(lat: string, lon: string) {
  this.weatherLoading = true;
  this.weatherService.getCurrentWeather(lat, lon).subscribe({
    next: (data) => {
      this.weather = data;
      this.weatherLoading = false;
    },
    error: (err) => {
      console.error('Error cargando clima:', err);
      this.weatherError = true;
      this.weatherLoading = false;
    }
  });
  
  // Cargar pronóstico para los próximos días
  this.weatherService.getForecast(lat, lon).subscribe({
    next: (data) => {
      this.forecast = data;
    },
    error: (err) => {
      console.error('Error cargando pronóstico:', err);
    }
  });
}
  getWeatherIcon(code: number): string {
    if (code === 0) return 'sun';
    if (code <= 3) return 'cloud-sun';
    if (code <= 48) return 'cloud-fog';
    if (code <= 67) return 'cloud-rain';
    if (code <= 77) return 'cloud-snow';
    if (code >= 95) return 'cloud-lightning';
    return 'cloud';
  }

  formatDay(date: string): string {
    const d = new Date(date);
    return d.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric' });
  }

  calculateAverageRating(): number {
    if (!this.reviews.length) return 0;
    const sum = this.reviews.reduce((acc, review) => acc + review.rating, 0);
    return Math.round((sum / this.reviews.length) * 10) / 10;
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
    this.calculateTotal();
  }

  onDateChange() {
    this.calculateNights();
  }

  onImageUploaded(image: Media) {
    if (this.accommodation?.id) {
      this.loadAccommodation(this.accommodation.id);
    }
  }

  checkAvailability() {
    console.log('Verificando disponibilidad:', this.checkIn, this.checkOut);
  }

  goToCheckout() {
    if (!this.checkIn || !this.checkOut) {
      alert('Selecciona fechas de check-in y check-out');
      return;
    }

    this.router.navigate(['/checkout'], {
      queryParams: {
        id: this.accommodation?.id,
        checkIn: this.checkIn,
        checkOut: this.checkOut,
        guests: this.guests,
        nights: this.nights,
        totalPrice: this.totalPrice
      }
    });
  }

  getAmenitiesList(): string[] {
    if (!this.accommodation?.amenities) return [];
    
    if (typeof this.accommodation.amenities === 'string') {
      try {
        return JSON.parse(this.accommodation.amenities);
      } catch {
        return [];
      }
    }
    
    return Array.isArray(this.accommodation.amenities) 
      ? this.accommodation.amenities 
      : [];
  }

  openModal(index: number) {
    if (!this.accommodation?.images?.length) return;
    this.modalCurrentIndex = index;
    this.modalCurrentImage = this.accommodation.images[index]?.url || '';
    this.showModal = true;
    document.body.style.overflow = 'hidden';
  }

  closeModal() {
    this.showModal = false;
    document.body.style.overflow = '';
  }

  previousModalImage() {
    if (!this.accommodation?.images?.length) return;
    if (this.modalCurrentIndex > 0) {
      this.modalCurrentIndex--;
    } else {
      this.modalCurrentIndex = this.accommodation.images.length - 1;
    }
    this.modalCurrentImage = this.accommodation.images[this.modalCurrentIndex]?.url || '';
  }

  nextModalImage() {
    if (!this.accommodation?.images?.length) return;
    if (this.modalCurrentIndex < this.accommodation.images.length - 1) {
      this.modalCurrentIndex++;
    } else {
      this.modalCurrentIndex = 0;
    }
    this.modalCurrentImage = this.accommodation.images[this.modalCurrentIndex]?.url || '';
  }

  bookNow() {
    if (!this.accommodation || !this.checkIn || !this.checkOut) {
      alert('Por favor, selecciona fechas de check-in y check-out');
      return;
    }

    if (!this.user) {
      alert('Debes iniciar sesión para hacer una reserva');
      this.router.navigate(['/login']);
      return;
    }

    const bookingData = {
      accommodation_id: this.accommodation.id,
      guest_id: this.user.id,
      check_in: this.checkIn,
      check_out: this.checkOut,
      guests: this.guests,
      total_price: this.totalPrice,
      status: 'pending' as 'pending'
    };

    this.bookingService.create(bookingData).subscribe({
      next: (response: BookingResponse) => {
        alert('Reserva creada correctamente');
        this.router.navigate(['/bookings', response.data.id]);
      },
      error: (error: any) => {
        console.error('Error al crear reserva:', error);
        alert('Error al crear la reserva. Inténtalo de nuevo.');
      }
    });
  }
}