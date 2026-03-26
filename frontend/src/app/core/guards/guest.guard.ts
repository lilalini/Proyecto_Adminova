import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable({
  providedIn: 'root'
})
export class GuestGuard implements CanActivate {
  
  constructor(
    private auth: AuthService,
    private router: Router
  ) {}

  canActivate(): boolean {
    // Si NO está autenticado, puede acceder a login/register
    if (!this.auth.isAuthenticated()) {
      return true;
    }
    
    // Si está autenticado, redirigir según su rol
    const role = this.auth.getRole();
    const redirectUrl = localStorage.getItem('redirectAfterLogin');
    
    if (redirectUrl) {
      localStorage.removeItem('redirectAfterLogin');
      this.router.navigate([redirectUrl]);
    } else {
      this.router.navigate([this.getDashboardByRole(role)]);
    }
    
    return false;
  }

  private getDashboardByRole(role: string | null): string {
    switch(role) {
      case 'admin': return '/admin/dashboard';
      case 'owner': return '/owner/dashboard';
      case 'staff': return '/staff/dashboard';
      default: return '/dashboard';
    }
  }
}