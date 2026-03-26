import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { IconSvgComponent } from '../../shared/components/icon-svg/icon-svg.component';

interface Experience {
  id: number;
  title: string;
  description: string;
  location: string;
  duration: string;
  price: number;
  image: string;
  includes: string[];
  featured?: boolean;
}

@Component({
  selector: 'app-experiences',
  standalone: true,
  imports: [CommonModule, RouterModule, IconSvgComponent],
  templateUrl: './experiences.component.html',
})
export class ExperiencesComponent {
  showModal = false;
  selectedExperience: Experience | null = null;

  experiences: Experience[] = [
    {
      id: 1,
      title: 'Ruta de Senderismo por la Sierra',
      description: 'Descubre los paisajes más impresionantes de la sierra con guía experto. Ruta de dificultad baja, apta para todos los niveles. Incluye picnic con productos locales.',
      location: 'Sierra de Madrid',
      duration: '4 horas',
      price: 45,
      image: '/assets/images/hiking.jpg',
      includes: ['Guía profesional', 'Picnic', 'Seguro de accidentes', 'Fotos del recorrido'],
      featured: true
    },
    {
      id: 2,
      title: 'Visita Guiada Centro Histórico',
      description: 'Recorrido por los rincones más emblemáticos de la ciudad. Descubre la historia y los secretos mejor guardados. Incluye entrada a monumentos.',
      location: 'Centro Ciudad',
      duration: '2.5 horas',
      price: 25,
      image: '/assets/images/tour.jpg',
      includes: ['Guía oficial', 'Entrada a monumentos', 'Auriculares', 'Mapa de la ciudad']
    },
    {
      id: 3,
      title: 'Catamaran al Atardecer',
      description: 'Disfruta de una experiencia única navegando al atardecer. Incluye bebida de bienvenida y música en vivo. Ideal para parejas.',
      location: 'Puerto Deportivo',
      duration: '3 horas',
      price: 65,
      image: '/assets/images/boat.jpg',
      includes: ['Bebida de bienvenida', 'Música en vivo', 'Fotos profesionales', 'Avituallamiento']
    },
    {
      id: 4,
      title: 'Taller de Cocina Local',
      description: 'Aprende a cocinar los platos típicos de la región con chefs locales. Incluye degustación y recetario.',
      location: 'Casco Antiguo',
      duration: '3.5 horas',
      price: 55,
      image: '/assets/images/cooking.jpg',
      includes: ['Chef instructor', 'Ingredientes frescos', 'Degustación', 'Recetario digital']
    },
    {
      id: 5,
      title: 'Ruta en Kayak por la Costa',
      description: 'Explora las calas escondidas y acantilados desde el mar. Equipo completo y guía incluido.',
      location: 'Costa Norte',
      duration: '3 horas',
      price: 50,
      image: '/assets/images/kayak.jpg',
      includes: ['Equipo completo', 'Guía', 'Fotos', 'Avituallamiento']
    },
    {
      id: 6,
      title: 'Visita a Bodega con Cata',
      description: 'Descubre el proceso del vino y degusta las mejores variedades. Incluye visita a viñedos y cata de 3 vinos.',
      location: 'Valle del Vino',
      duration: '2 horas',
      price: 35,
      image: '/assets/images/wine.jpg',
      includes: ['Visita guiada', 'Cata de 3 vinos', 'Aperitivo local', 'Descuento en compras']
    }
  ];

  openModal(experience: Experience) {
    this.selectedExperience = experience;
    this.showModal = true;
    document.body.style.overflow = 'hidden';
  }

  closeModal() {
    this.showModal = false;
    this.selectedExperience = null;
    document.body.style.overflow = '';
  }

  contactUs() {
    alert('¡Gracias por tu interés! Pronto nos pondremos en contacto contigo para gestionar tu reserva.');
    this.closeModal();
  }
}