import { Component, Input, OnChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';

@Component({
  selector: 'app-icon-svg',
  standalone: true,
  imports: [CommonModule],
  template: `
    <svg [class]="className" 
         [style.width]="size" 
         [style.height]="size"
         [attr.viewBox]="viewBox"
         fill="none"
         stroke="currentColor"
         stroke-width="2"
         stroke-linecap="round"
         stroke-linejoin="round"
         [innerHTML]="svgContent">
    </svg>
  `
})
export class IconSvgComponent implements OnChanges {
  @Input() name: string = '';
  @Input() className: string = 'w-5 h-5';
  @Input() size: string = '20px';
  @Input() viewBox: string = '0 0 24 24';

  svgContent: SafeHtml = '';

  constructor(private http: HttpClient, private sanitizer: DomSanitizer) {}

  ngOnChanges() {
    if (this.name) {
      this.http.get(`/assets/icons/${this.name}.svg`, { responseType: 'text' })
        .subscribe({
          next: (svg) => {
            this.svgContent = this.sanitizer.bypassSecurityTrustHtml(svg);
          },
          error: () => {
            console.warn(`Icon not found: ${this.name}`);
          }
        });
    }
  }
}