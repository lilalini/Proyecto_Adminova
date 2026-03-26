import { Injectable } from '@angular/core';
import { CanActivate, Router, ActivatedRouteSnapshot } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { Observable } from 'rxjs';
import { take, map } from 'rxjs/operators';

@Injectable({ providedIn: 'root' })
export class AuthGuard implements CanActivate {
  constructor(private auth: AuthService, private router: Router) {}

  /*canActivate(route: ActivatedRouteSnapshot): boolean {
    const requiredRole = route.data['role']; // opcional
    if (!this.auth.isAuthenticated()) {
      this.router.navigate(['/login']);
      return false;
    }

    if (requiredRole && this.auth.getRole() !== requiredRole) {
      this.router.navigate(['/dashboard']); // o página de acceso denegado
      return false;
    }

    return true;
  }*/

 canActivate(): boolean {
    if (localStorage.getItem('auth_token')) {
      return true;
    }
    this.router.navigate(['/login']);
    return false;
  }
}