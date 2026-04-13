import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { map, catchError, switchMap } from 'rxjs/operators';
import { GuestService } from '../services/guest.service';
import { AuthService } from '../services/auth.service';
import { ActivatedRouteSnapshot } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class ProfileCompleteGuard implements CanActivate {
  constructor(
    private guestService: GuestService,
    private auth: AuthService,
    private router: Router
  ) {}

canActivate(route: ActivatedRouteSnapshot): Observable<boolean> {
  return this.auth.currentUser$.pipe(
    switchMap(user => {
      if (!user) {
        this.router.navigate(['/login']);
        return of(false);
      }
      
      return this.guestService.isProfileComplete(user.id).pipe(
        map(response => {
          if (response.complete) {
            return true;
          } else {
            // Guardar la URL a la que intentaba acceder
           const returnUrl = route.url.map((segment: { path: string }) => segment.path).join('/');
            this.router.navigate(['/guest/profile'], {
              queryParams: { incomplete: true, fields: response.missing_fields?.join(',') },
              state: { returnUrl: `/${returnUrl}` }
            });
            return false;
          }
        }),
        catchError(() => {
          this.router.navigate(['/guest/profile']);
          return of(false);
        })
      );
    })
  );
}
}