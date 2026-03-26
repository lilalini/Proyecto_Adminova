import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-icon-svg',
  standalone: true,
  template: `
    <svg [class]="className" 
         [style.width]="size" 
         [style.height]="size"
         [attr.viewBox]="viewBox"
         fill="none"
         stroke="currentColor"
         stroke-width="2"
         stroke-linecap="round"
         stroke-linejoin="round">
      <use [attr.href]="iconPath" (error)="onError()"></use>
    </svg>
  `
})
export class IconSvgComponent {
  @Input() name: string = '';
  @Input() className: string = 'w-5 h-5';
  @Input() size: string = '20px';
  @Input() viewBox: string = '0 0 24 24';

  get iconPath(): string {
    return `/assets/icons/${this.name}.svg`;
  }

  onError() {
    console.warn(`Icon not found: ${this.name}`);
    // Opcional: mostrar un icono por defecto
    // this.name = 'default';
  }
}