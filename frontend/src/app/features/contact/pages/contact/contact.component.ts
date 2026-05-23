import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IconSvgComponent } from '../../../../shared/components/icon-svg/icon-svg.component';

@Component({
  selector: 'app-contact',
  standalone: true,
  imports: [CommonModule, FormsModule, IconSvgComponent],
  templateUrl: './contact.component.html',
})
export class ContactComponent {
  formData = {
    name: '',
    email: '',
    subject: '',
    message: ''
  };
  sending = false;
  successMessage = '';
  errorMessage = '';

  onSubmit() {
    if (!this.formData.name || !this.formData.email || !this.formData.message) {
      this.errorMessage = 'Por favor rellena todos los campos obligatorios';
      return;
    }

    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(this.formData.email)) {
      this.errorMessage = 'El email no tiene un formato válido';
      return;
    }

    this.sending = true;
    this.errorMessage = '';

    // Simulamos envío
    setTimeout(() => {
      this.sending = false;
      this.successMessage = '¡Mensaje enviado! Te responderemos en breve.';
      this.formData = { name: '', email: '', subject: '', message: '' };
    }, 1500);
  }
}