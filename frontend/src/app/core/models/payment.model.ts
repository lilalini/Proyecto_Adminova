export interface Payment {
  id: number;
  payment_reference: string;
  booking_id: number;
  guest_id: number;
  user_id?: number; // quien registró el pago (staff)
  payment_type: 'deposit' | 'final' | 'full' | 'damage_deposit';
  method: 'credit_card' | 'transfer' | 'cash' | 'paypal' | 'stripe';
  transaction_id?: string;
  amount: number;
  currency: string;
  status: 'pending' | 'completed' | 'failed' | 'refunded';
  payment_date: string;
  due_date?: string;
  notes?: string;
  receipt_sent: boolean;
  receipt_sent_at?: string;
  refunded_at?: string;
  refund_reason?: string;
  created_at: string;
  updated_at: string;
}

export interface PaymentListResponse {
  data: Payment[];
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

export interface PaymentResponse {
  data: Payment;
}