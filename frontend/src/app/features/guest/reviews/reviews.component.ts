import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { IconSvgComponent } from '../../../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-reviews',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, IconSvgComponent],
  templateUrl: './reviews.component.html',
})
export class ReviewsComponent implements OnInit {
  pendingReviews: any[] = [];
  completedReviews: any[] = [];
  loading = true;
  
  // Modal de reseña
  showReviewModal = false;
  selectedBooking: any = null;
  reviewRating = 5;
  reviewComment = '';

  ngOnInit() {
    this.loadReviews();
  }

  loadReviews() {
    // Aquí vendrá la llamada al backend
    setTimeout(() => {
      this.pendingReviews = [
        {
          id: 1,
          accommodation: {
            id: 1,
            title: 'Loft Moderno con Terraza',
            image: '/assets/images/placeholder.jpg'
          },
          check_out: '2026-03-15',
          stay_days: 3
        },
        {
          id: 2,
          accommodation: {
            id: 2,
            title: 'Ático de Lujo con Vistas',
            image: '/assets/images/placeholder.jpg'
          },
          check_out: '2026-03-10',
          stay_days: 2
        }
      ];

      this.completedReviews = [
        {
          id: 1,
          accommodation: {
            id: 3,
            title: 'Apartamento Centro',
            image: '/assets/images/placeholder.jpg'
          },
          rating: 5,
          comment: 'Excelente estancia, todo perfecto. Muy recomendable.',
          created_at: '2026-02-15'
        },
        {
          id: 2,
          accommodation: {
            id: 4,
            title: 'Villa con Piscina',
            image: '/assets/images/placeholder.jpg'
          },
          rating: 4,
          comment: 'Muy buena experiencia, solo faltaba un poco más de menaje.',
          created_at: '2026-01-20'
        }
      ];
      
      this.loading = false;
    }, 500);
  }

  openReviewModal(booking: any) {
    this.selectedBooking = booking;
    this.reviewRating = 5;
    this.reviewComment = '';
    this.showReviewModal = true;
  }

  closeReviewModal() {
    this.showReviewModal = false;
    this.selectedBooking = null;
  }

  submitReview() {
    if (!this.reviewComment.trim()) {
      alert('Por favor, escribe un comentario');
      return;
    }

    // Aquí irá la llamada al backend
    const newReview = {
      id: Date.now(),
      accommodation: this.selectedBooking.accommodation,
      rating: this.reviewRating,
      comment: this.reviewComment,
      created_at: new Date().toISOString()
    };
    
    this.completedReviews.unshift(newReview);
    this.pendingReviews = this.pendingReviews.filter(b => b.id !== this.selectedBooking.id);
    
    this.closeReviewModal();
    alert('¡Gracias por tu reseña!');
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