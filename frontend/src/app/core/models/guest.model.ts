export interface Guest {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone: string;
  document_type: string | null;
  document_number: string | null;
  nationality: string;
  birth_date?: string;
  gender?: string;
  address?: string;
  city?: string;
  postal_code?: string;
  country?: string;
  source: 'direct' | 'booking' | 'airbnb' | 'expedia';
  source_data?: any; // JSON con datos raw de la OTA
  external_id?: string;
  created_at: string;
  updated_at: string;
}

export interface GuestListResponse {
  data: Guest[];
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

export interface GuestResponse {
  data: Guest;
}