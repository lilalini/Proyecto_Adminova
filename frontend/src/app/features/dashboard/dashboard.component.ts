import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  template: `<div class="flex items-center justify-center min-h-screen"><p class="text-gray-500">Redirigiendo...</p></div>`,
})
export class DashboardComponent implements OnInit {
  constructor(private auth: AuthService, private router: Router) {}

  ngOnInit() {
    this.auth.currentUser$.subscribe(user => {
      if (!user) {
        this.router.navigate(['/login']);
        return;
      }

      if (user.role === 'admin') {
        this.router.navigate(['/admin/dashboard']);
      } else if (user.role === 'owner') {
        this.router.navigate(['/owner/dashboard']);
      } else if (user.role === 'staff') {
        this.router.navigate(['/staff/dashboard']);
      } else if (user.role === 'guest') {
        this.router.navigate(['/guest/dashboard']);
      } else {
        this.router.navigate(['/']);
      }
    });
  }
}