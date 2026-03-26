import { Owner } from './owner.model';
import { Media } from './media.model';

export interface Accommodation {
  id: number;
  title: string;                    
  slug: string;
  description: string;
  property_type: string;
  bedrooms: number;
  bathrooms: number;
  max_guests: number;
  size_m2: number;
  address: string;
  city: string;
  postal_code: string;
  country: string;
  latitude?: string;
  longitude?: string;
  base_price: number;           
  cleaning_fee?: number;
  security_deposit?: number;
  minimum_stay: number;
  maximum_stay?: number;
  check_in_time: string;
  check_out_time: string;
  status: 'draft' | 'published' | 'maintenance' | 'inactive';
  amenities: string[];
  house_rules?: string[];
  cancellation_policy_id: number;
  owner?: Owner;                   
  images?: Media[];                   
  main_image?: string;
  created_at: string;
  updated_at: string;
}

export interface AccommodationListResponse {
  data: Accommodation[];
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

export interface AccommodationResponse {
  data: Accommodation;
}

export interface CreateAccommodationData {
  title: string;
  description: string;
  property_type: string;
  bedrooms: number;
  bathrooms: number;
  max_guests: number;
  size_m2: number;
  address: string;
  city: string;
  postal_code: string;
  country: string;
  base_price: number;
  cleaning_fee?: number;
  security_deposit?: number;
  minimum_stay: number;
  maximum_stay?: number;
  check_in_time: string;
  check_out_time: string;
  status?: 'draft' | 'published' | 'maintenance' | 'inactive';
  amenities: string[];
  house_rules?: string[];
  cancellation_policy_id: number;
}

export interface ApiResponse<T> {
  data: T;
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