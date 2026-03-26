export interface Owner {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone?: string;
  document_type?: string;
  document_number?: string;
  address?: string;
  city?: string;
  postal_code?: string;
  country?: string;
  iban?: string;
  contract_signed?: boolean;
  contract_date?: string;
  is_active: boolean;
  email_verified_at?: string;
  last_login_at?: string;
  last_login_ip?: string;
  created_at: string;
  updated_at: string;
}

export interface OwnerListResponse {
  data: Owner[];
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

export interface OwnerResponse {
  data: Owner;
}