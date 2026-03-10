import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-icon-svg',
  standalone: true,
  template: `
    <img [src]="'/assets/icons/' + name + '.svg'" 
         [class]="className"
         [style.width]="size" 
         [style.height]="size"
         [alt]="name">
  `
})
export class IconSvgComponent {
  @Input() name: string = '';
  @Input() className: string = 'w-5 h-5';
  @Input() size: string = '20px';
}