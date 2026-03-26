import { Accommodation } from './accommodation.model';
import { Booking } from './booking.model';
import { Guest } from './guest.model';

export interface Review {
  id: number;
  accommodation_id: number;
  booking_id?: number;
  guest_id: number;
  user_id?: number; // quien respondió (staff)
  rating: number; // 1-5
  cleanliness_rating?: number; // 1-5
  communication_rating?: number; // 1-5
  location_rating?: number; // 1-5
  value_rating?: number; // 1-5
  title?: string;
  comment: string;
  host_response?: string;
  host_responded_at?: string;
  status: 'pending' | 'published' | 'rejected' | 'archived';
  source: 'direct' | 'booking' | 'airbnb' | 'google';
  external_review_id?: string; // ID en la OTA
  is_verified: boolean;
  helpful_votes?: number;
  published_at?: string;
  created_at: string;
  updated_at: string;
  deleted_at?: string;

  // Relaciones (opcionales, según la respuesta de la API)
  accommodation?: Accommodation;
  booking?: Booking;
  guest?: Guest;
  guest_name?: string; // A veces viene directamente
}

export interface ReviewListResponse {
  data: Review[];
  links?: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
  meta?: {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
  };
}

export interface ReviewResponse {
  data: Review;
}

export interface CreateReviewData {
  accommodation_id: number;
  booking_id?: number;
  rating: number;
  cleanliness_rating?: number;
  communication_rating?: number;
  location_rating?: number;
  value_rating?: number;
  title?: string;
  comment: string;
}

export interface RespondToReviewData {
  response: string;
}