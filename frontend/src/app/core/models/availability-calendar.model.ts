import { Accommodation } from './accommodation.model';

export interface AvailabilityCalendar {
  id: number;
  accommodation_id: number;
  user_id?: number;
  date: string;
  status: 'available' | 'booked' | 'blocked' | 'maintenance';
  price?: number;
  min_nights?: number;
  max_nights?: number;
  closed_to_arrival?: boolean;
  closed_to_departure?: boolean;
  notes?: string;
  created_at: string;
  updated_at: string;
  deleted_at?: string;

  // Relaciones (opcionales)
  accommodation?: Accommodation;
}

export interface AvailabilityCalendarListResponse {
  data: AvailabilityCalendar[];
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

export interface AvailabilityCalendarResponse {
  data: AvailabilityCalendar;
}

export interface AvailabilityCheckRequest {
  accommodation_id: number;
  check_in: string;
  check_out: string;
}

export interface AvailabilityCheckResponse {
  available: boolean;
  price?: number;
  conflicts?: string[];
}