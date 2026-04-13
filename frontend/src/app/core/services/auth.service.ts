import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { ApiService } from './api.service';
import { User, LoginCredentials, RegisterData, AuthResponse } from '../models/user.model';
import { environment } from '../../../environments/environment'; 

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private currentUserSubject = new BehaviorSubject<User | null>(null);
  public currentUser$ = this.currentUserSubject.asObservable();
  private tokenKey = 'auth_token';
  private roleKey = 'auth_role';
  private userKey = 'auth_user'; 
  private apiUrl = `${environment.apiUrl}/user`; 

  constructor(private api: ApiService, private router: Router, private http: HttpClient) {
    this.loadStoredUser();
  }

  login(credentials: LoginCredentials): Observable<AuthResponse> {
    return this.api.login(credentials).pipe(
      tap(response => {
        this.setToken(response.token);
        this.setRole(response.user.role);
        this.setUser(response.user);  // <--- AÑADIR
        this.currentUserSubject.next(response.user);
      })
    );
  }

  register(data: RegisterData): Observable<AuthResponse> {
    return this.api.register(data).pipe(
      tap(response => {
        this.setToken(response.token);
        this.setRole(response.user.role);
        this.setUser(response.user);  // <--- AÑADIR
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
  
  private setUser(user: User) { localStorage.setItem(this.userKey, JSON.stringify(user)); }  // <--- AÑADIR
  private getUser(): User | null { 
    const userStr = localStorage.getItem(this.userKey);
    return userStr ? JSON.parse(userStr) : null;
  }

  private clearAuth(): void {
    localStorage.removeItem(this.tokenKey);
    localStorage.removeItem(this.roleKey);
    localStorage.removeItem(this.userKey);  // <--- AÑADIR
    this.currentUserSubject.next(null);
    this.router.navigate(['/login']);
  }

  private loadStoredUser(): void {
    const user = this.getUser();
    if (user) {
      this.currentUserSubject.next(user);
    } else {
      // Si no hay usuario guardado, intentar con token
      const token = this.getToken();
      if (token) {
        this.api.getCurrentUser().subscribe({
          next: response => {
            this.setUser(response.user);
            this.currentUserSubject.next(response.user);
          },
          error: () => {
            console.warn('No se pudo recuperar el usuario, token puede ser inválido');
            this.clearAuth();
          }
        });
      }
    }
  }

  isAuthenticated(): boolean {
    return !!this.getToken() && !!this.getUser();
  }

  getTokenValue(): string | null { return this.getToken(); }

  updateCurrentUser(user: User) {
    this.setUser(user);
    this.currentUserSubject.next(user);
  }

    updateUser(data: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/user`, data);
  }
}
