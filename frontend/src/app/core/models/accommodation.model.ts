export interface Accommodation {
  id: number;
  name: string;
  description: string;
  address: string;
  price: number;
  owner_id: number;
  created_at: string;
  updated_at: string;
}

export interface AccommodationResponse {
  data: Accommodation;
}

export interface AccommodationListResponse {
  data: Accommodation[];
}

export interface CreateAccommodationData {
  name: string;
  description: string;
  address: string;
  price: number;
}

export interface AccommodationResponse {
  data: Accommodation;
  message?: string;
}

export interface ErrorResponse {
  message: string;
  errors?: any;
}