import { Accommodation } from './accommodation.model';
import { DistributionChannel } from './distribution-channel.model';

export interface ApartmentChannel {
  id: number;
  accommodation_id: number;
  distribution_channel_id: number;
  external_listing_id?: string; // ID del alojamiento en la OTA
  external_url?: string; // URL del anuncio
  connection_status: 'connected' | 'disconnected' | 'error';
  sync_enabled: boolean;
  sync_price?: boolean; // sincronizar precios?
  sync_availability?: boolean; // sincronizar disponibilidad?
  sync_content?: boolean; // sincronizar fotos/descripciones?
  price_adjustment_type?: 'percentage' | 'fixed';
  price_adjustment_value?: number; // +10%, -5€, etc.
  min_stay_adjustment?: number; // días mínimos específicos para este canal
  last_sync_at?: string;
  last_sync_status?: string;
  last_sync_message?: string;
  channel_data?: any; // JSON con datos específicos del canal
  created_at: string;
  updated_at: string;
  deleted_at?: string;

  // Relaciones (opcionales, según la respuesta de la API)
  accommodation?: Accommodation;
  distribution_channel?: DistributionChannel;
}

export interface ApartmentChannelListResponse {
  data: ApartmentChannel[];
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

export interface ApartmentChannelResponse {
  data: ApartmentChannel;
}