import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule, Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './register.component.html',
})
export class RegisterComponent implements OnInit {
  userData = {
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    accepts_terms: false,        // <--- AÑADIDO
    accepts_newsletter: false    // <--- AÑADIDO
  };
  errorMessage = '';
  loading = false;
  submitted = false;              // <--- AÑADIDO (para mostrar error del checkbox)

  constructor(
    private auth: AuthService,
    private router: Router
  ) {}

  ngOnInit() { 
    if (this.auth.isAuthenticated()) {
      const redirectUrl = localStorage.getItem('redirectAfterLogin') || '/dashboard';
      localStorage.removeItem('redirectAfterLogin');
      this.router.navigate([redirectUrl]);
    }
  }

  onSubmit() {
    this.submitted = true;
    this.errorMessage = '';

    // Validar términos y condiciones
    if (!this.userData.accepts_terms) {
      this.errorMessage = 'Debes aceptar los términos y condiciones';
      return;
    }

    // Validar que las contraseñas coincidan
    if (this.userData.password !== this.userData.password_confirmation) {
      this.errorMessage = 'Las contraseñas no coinciden';
      return;
    }

    // Validar longitud mínima
    if (this.userData.password.length < 8) {
      this.errorMessage = 'La contraseña debe tener al menos 8 caracteres';
      return;
    }

    this.loading = true;

    this.auth.register(this.userData).subscribe({
      next: () => {
        const redirectUrl = localStorage.getItem('redirectAfterLogin') || '/dashboard';
        localStorage.removeItem('redirectAfterLogin');
        window.location.href = redirectUrl;
      },
      error: (error) => {
        this.errorMessage = error.error?.message || 'Error al registrarse';
        this.loading = false;
      }
    });
  }
}