import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { map, take } from 'rxjs/operators';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RoleGuard implements CanActivate {
  constructor(
    private auth: AuthService,
    private router: Router
  ) {}

  canActivate(route: ActivatedRouteSnapshot): Observable<boolean> {
    const requiredRoles = route.data['roles'] as Array<string>;

    return this.auth.currentUser$.pipe(
      take(1),
      map(user => {
        if (!user) {
          this.router.navigate(['/login']);
          return false;
        }

        if (requiredRoles && !requiredRoles.includes(user.role)) {
          // Redirigir según el rol
          if (user.role === 'owner') {
            this.router.navigate(['/my-accommodations']);
          } else if (user.role === 'guest') {
            this.router.navigate(['/my-bookings']);
          } else if (user.role === 'staff') {
            this.router.navigate(['/cleaning']);
          } else {
            this.router.navigate(['/dashboard']);
          }
          return false;
        }

        return true;
      })
    );
  }
}