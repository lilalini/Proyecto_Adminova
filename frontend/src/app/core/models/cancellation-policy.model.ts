export interface CancellationPolicy {
  id: number;
  name: string;
  description: string;
  free_cancellation_days: number;
  penalty_percentage: number;
}

export interface CancellationPolicyListResponse {
  data: CancellationPolicy[];
}