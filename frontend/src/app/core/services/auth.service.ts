import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { ApiService } from './api.service';
import { User, LoginCredentials, RegisterData, AuthResponse } from '../models/user.model';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private currentUserSubject = new BehaviorSubject<User | null>(null);
  public currentUser$ = this.currentUserSubject.asObservable();
  private tokenKey = 'auth_token';
  private roleKey = 'auth_role';

  constructor(private api: ApiService, private router: Router) {
    this.loadStoredUser();
  }

  login(credentials: LoginCredentials): Observable<AuthResponse> {
    return this.api.login(credentials).pipe(
      tap(response => {
        this.setToken(response.token);
        this.setRole(response.user.role);
        this.currentUserSubject.next(response.user);
      })
    );
  }

  register(data: RegisterData): Observable<AuthResponse> {
    return this.api.register(data).pipe(
      tap(response => {
        this.setToken(response.token);
        this.setRole(response.user.role);
        this.currentUserSubject.next(response.user);
      })
    );
  }

  logout() {
  return this.api.logout().pipe(
    tap({
      next: () => this.clearAuth(),
      error: () => this.clearAuth()
    })
  );
}

  private setToken(token: string) { localStorage.setItem(this.tokenKey, token); }
  private getToken(): string | null { return localStorage.getItem(this.tokenKey); }
  private setRole(role: string) { localStorage.setItem(this.roleKey, role); }
  getRole(): string | null { return localStorage.getItem(this.roleKey); }

  private clearAuth(): void {
    localStorage.removeItem(this.tokenKey);
    localStorage.removeItem(this.roleKey);
    this.currentUserSubject.next(null);
    this.router.navigate(['/login']);
  }

  private loadStoredUser(): void {
  const token = this.getToken();
  if (token) {
    this.api.getCurrentUser().subscribe({
      next: response => {
        this.currentUserSubject.next(response.user);
      },
      error: () => {
        console.warn('No se pudo recuperar el usuario, token sigue guardado');
        // NO limpiar localStorage automáticamente
      }
    });
  }
}

  isAuthenticated(): boolean {
    return !!this.getToken() && !!this.currentUserSubject.value;
  }

  getTokenValue(): string | null { return this.getToken(); }

  // En auth.service.ts
  updateCurrentUser(user: User) {
    this.currentUserSubject.next(user);
  }
}

