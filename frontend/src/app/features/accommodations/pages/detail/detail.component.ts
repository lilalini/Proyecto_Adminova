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
import { AvailabilityCalendar } from '../../../../core/models/availability-calendar.model';
import { User } from '../../../../core/models/user.model';
import { Media } from '../../../../core/models/media.model';
import { BookingResponse } from '../../../../core/models/booking.model';
import { WeatherService, CurrentWeather, DailyForecast } from '../../../../core/services/weather.service';
import { SkeletonComponent } from '../../../../shared/components/skeleton/skeleton.component';
import { ReviewService } from '../../../../core/services/review.service';
import { PaginatedResponse, Review } from '../../../../core/models/review.model';
import { ApiResponse } from '../../../../core/services/public.service';

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
  todayDate: string = new Date().toISOString().split('T')[0];
  availabilityError: string = '';
  canBook: boolean = false;
  isCheckingAvailability: boolean = false;

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
  weather: CurrentWeather | null = null;
  forecast: DailyForecast[] = [];
  weatherLoading = true;
  weatherError = false;
  showForecast = false;

  // Reviews
  canReview = false;
  showReviewForm = false;
  submittingReview = false;
  reviewData = {
    rating: 5,
    comment: '',
    booking_id: 0
  };
  reviewSuccess = false;
  reviewError = '';
  showReviews = false;

  constructor(
    private route: ActivatedRoute,
    private publicService: PublicService,
    private auth: AuthService,
    private bookingService: BookingService,
    private router: Router,
    private weatherService: WeatherService,
    private reviewService: ReviewService
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
      this.checkCanReview(parseInt(id));
      
      const today = new Date();
      const nextWeek = new Date();
      nextWeek.setDate(today.getDate() + 7);
      
      this.checkIn = today.toISOString().split('T')[0];
      this.checkOut = nextWeek.toISOString().split('T')[0];
      this.calculateNights();
      this.verifyAvailability();
    }
  }

  loadAccommodation(id: number) {
    this.publicService.getAccommodation(id).subscribe({
      next: (response) => {
        this.accommodation = response.data;
        this.loading = false;
        this.calculateNights();
        this.verifyAvailability();
        
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
      next: (response: ApiResponse<PaginatedResponse<Review>>) => {
        this.reviews = response.data.data;
      },
      error: (error) => {
        console.error('Error cargando reseñas:', error);
      }
    });
  }

  toggleReviews(): void {
    this.showReviews = !this.showReviews;
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
      
      this.totalPrice = basePriceTotal + extraGuestsTotal + (this.accommodation.cleaning_fee || 0);
    }
  }

  onGuestsChange() {
    this.calculateTotal();
  }

  onDateChange() {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const checkInDate = new Date(this.checkIn);
    const checkOutDate = new Date(this.checkOut);

    if (checkInDate < today) {
      this.availabilityError = 'La fecha de entrada no puede ser anterior a hoy';
      this.canBook = false;
      return;
    }

    if (checkOutDate <= checkInDate) {
      this.availabilityError = 'La fecha de salida debe ser posterior a la fecha de entrada';
      this.canBook = false;
      return;
    }

    this.availabilityError = '';
    this.verifyAvailability();
  }

  verifyAvailability() {
    if (!this.accommodation || !this.checkIn || !this.checkOut) {
      this.canBook = false;
      return;
    }

    this.isCheckingAvailability = true;
    this.canBook = false;

    this.publicService.checkAvailability(this.accommodation.id, this.checkIn, this.checkOut).subscribe({
      next: (response) => {
        this.isCheckingAvailability = false;
        this.canBook = response.available;
        this.availabilityError = response.available ? '' : 'El alojamiento no está disponible en las fechas seleccionadas';
        
        if (response.available) {
          this.calculateNights();
        }
      },
      error: (err) => {
        console.error('Error verificando disponibilidad:', err);
        this.isCheckingAvailability = false;
        this.canBook = false;
        this.availabilityError = 'Error verificando disponibilidad. Intenta de nuevo.';
      }
    });
  }

  onImageUploaded(image: Media) {
    if (this.accommodation?.id) {
      this.loadAccommodation(this.accommodation.id);
    }
  }

  goToCheckout() {
    if (!this.canBook) {
      alert(this.availabilityError || 'Las fechas seleccionadas no están disponibles');
      return;
    }

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
    
    return Array.isArray(this.accommodation.amenities) ? this.accommodation.amenities : [];
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

  checkCanReview(accommodationId: number) {
    if (!this.user || this.user.role !== 'guest') return;

    this.bookingService.getMyBookings().subscribe({
      next: (response) => {
        const completedBooking = response.data.find(b =>
          b.accommodation?.id === accommodationId &&
          b.status === 'checked_out'
        );
        if (completedBooking) {
          this.canReview = true;
          this.reviewData.booking_id = completedBooking.id;
        }
      },
      error: () => {}
    });
  }

  submitReview() {
    if (!this.reviewData.comment.trim()) return;
    this.submittingReview = true;
    this.reviewError = '';

    this.reviewService.create({
      booking_id: this.reviewData.booking_id,
      rating: this.reviewData.rating,
      comment: this.reviewData.comment
    }).subscribe({
      next: () => {
        this.reviewSuccess = true;
        this.showReviewForm = false;
        this.submittingReview = false;
        const id = this.route.snapshot.paramMap.get('id');
        if (id) this.loadReviews(parseInt(id));
      },
      error: (err) => {
        this.reviewError = err.error?.message || 'Error al enviar la reseña';
        this.submittingReview = false;
      }
    });
  }

  setRating(rating: number) {
    this.reviewData.rating = rating;
  }

  getGuestName(review: Review): string {
    if (review.guest) {
      return review.guest.first_name + ' ' + review.guest.last_name;
    }
    return 'Huésped verificado';
  }

  scrollToReviews(): void {
    const element = document.getElementById('reviews');
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
    }
    this.showReviews = true;
  }

    onImagesChanged(images: any[]) {
    // Actualizar las imágenes en el componente principal
    if (this.accommodation) {
      this.accommodation.images = images;
    }
  }
}