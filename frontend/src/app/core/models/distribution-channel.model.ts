export interface DistributionChannel {
  id: number;
  channel_code: string; // booking, airbnb, expedia, direct
  name: string; // Booking.com, Airbnb, etc.
  channel_type: 'OTA' | 'direct' | 'corporate';
  commission_rate?: number; // % de comisión
  is_active: boolean;
  sync_enabled?: boolean;
  last_sync_at?: string;
  created_at: string;
  updated_at: string;
  deleted_at?: string;
}

export interface DistributionChannelListResponse {
  data: DistributionChannel[];
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

export interface DistributionChannelResponse {
  data: DistributionChannel;
}