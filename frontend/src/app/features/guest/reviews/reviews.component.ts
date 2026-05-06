import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';
import { BookingService } from '../../../core/services/booking.service';
import { ReviewService } from '../../../core/services/review.service';
import { AuthService } from '../../../core/services/auth.service';
import { GuestService } from '../../../core/services/guest.service';
import { forkJoin, of } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { BackButtonComponent } from '../../../shared/components/back-button/back-button.component';

@Component({
  selector: 'app-reviews',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, IconSvgComponent, BackButtonComponent],
  templateUrl: './reviews.component.html',
})
export class ReviewsComponent implements OnInit {
  pendingReviews: any[] = [];
  completedReviews: any[] = [];
  loading = true;
  submitting = false;
  error = '';

  // Modal
  showReviewModal = false;
  selectedBooking: any = null;
  reviewRating = 5;
  reviewComment = '';

  private guestId: number | null = null;

  constructor(
    private bookingService: BookingService,
    private reviewService: ReviewService,
    private auth: AuthService,
    private guestService: GuestService
  ) {}

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      if (user) {
        // Obtener el guest asociado al user
        this.guestService.getByUserId(user.id).subscribe({
          next: (res: any) => {
            this.guestId = res.data?.id ?? res.id ?? null;
            this.loadReviews();
          },
          error: () => this.loadReviews()
        });
      }
    });
  }

  loadReviews() {
    forkJoin({
      bookings: this.bookingService.getMyBookings().pipe(catchError(() => of({ data: [] }))),
      reviews: this.guestId
        ? this.reviewService.getAll(1).pipe(catchError(() => of({ data: [] })))
        : of({ data: [] })
    }).subscribe({
      next: ({ bookings, reviews }) => {
        // Reservas completadas sin reseña
        const reviewedBookingIds = new Set(
          (reviews as any).data.map((r: any) => r.booking_id)
        );

        this.pendingReviews = (bookings as any).data.filter((b: any) =>
          b.status === 'checked_out' && !reviewedBookingIds.has(b.id)
        );

        // Reseñas ya escritas
        this.completedReviews = (reviews as any).data.filter((r: any) =>
          r.guest_id === this.guestId
        );

        this.loading = false;
      },
      error: () => {
        this.loading = false;
      }
    });
  }

  openReviewModal(booking: any) {
    this.selectedBooking = booking;
    this.reviewRating = 5;
    this.reviewComment = '';
    this.error = '';
    this.showReviewModal = true;
  }

  closeReviewModal() {
    this.showReviewModal = false;
    this.selectedBooking = null;
  }

  submitReview() {
    if (!this.reviewComment.trim()) {
      this.error = 'Por favor, escribe un comentario';
      return;
    }

    this.submitting = true;
    this.error = '';

    this.reviewService.create({
      booking_id: this.selectedBooking.id,
      rating: this.reviewRating,
      comment: this.reviewComment
    }).subscribe({
      next: () => {
        this.submitting = false;
        this.closeReviewModal();
        this.loading = true;
        this.loadReviews();
      },
      error: (err) => {
        this.submitting = false;
        this.error = err.error?.message || 'Error al enviar la reseña';
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

  getStars(rating: number): number[] {
    return Array(rating).fill(0);
  }

  getEmptyStars(rating: number): number[] {
    return Array(5 - rating).fill(0);
  }
}