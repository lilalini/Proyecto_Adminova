export interface Media {
  id: number;
  model_type: string;
  model_id: number;
  collection_name: string;
  name: string;
  file_name: string;
  mime_type: string;
  disk: string;
  size: number;
  order: number;
  is_main?: boolean;
  url: string;
  thumbnail_url?: string;
  created_at: string;
  updated_at: string;
}

export interface MediaListResponse {
  data: Media[];
  meta?: {
    current_page: number;
    last_page: number;
    total: number;
  };
}

export interface MediaResponse {
  data: Media;
}