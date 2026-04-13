import { Accommodation } from './accommodation.model';
import { Guest } from './guest.model';

export interface Booking {
  id: number;
  booking_reference: string;
  accommodation: Accommodation;
  guest: Guest;
  channel: Channel;
  check_in: string;
  check_out: string;
  nights: number;
  adults: number;
  children: number;
  infants: number;
  pets: number;
  status: 'pending' | 'confirmed' | 'checked_in' | 'checked_out' | 'cancelled' | 'completed';
  total_amount: number;
  paid_amount: number;
  balance_due: number;
  payment_status: 'pending' | 'paid' | 'refunded' | 'failed';
  guest_name: string;
  guest_email: string;
  guest_phone: string;
  created_at: string;
  updated_at: string;
}

export interface Channel {
  id: number;
  channel_code: string;
  name: string;
  channel_type: string;
  commission_rate: number;
  is_active: boolean;
}

export interface BookingListResponse {
  data: Booking[];
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

export interface BookingResponse {
  data: Booking;
}

export interface BookingResponseWithToken {
  data: Booking;
  token: string;
}