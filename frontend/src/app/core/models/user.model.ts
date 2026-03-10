export interface User {
  id: number;
  first_name: string;    
  last_name: string;  
  email: string;
  role: 'admin' | 'owner' | 'guest' | 'staff';
  created_at: string;
  updated_at: string;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  first_name: string;
  last_name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

export interface AuthResponse {
  user: User;
  token: string;
}