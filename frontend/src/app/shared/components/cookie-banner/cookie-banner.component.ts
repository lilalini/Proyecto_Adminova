import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-cookie-banner',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './cookie-banner.component.html',
})
export class CookieBannerComponent implements OnInit {
  showBanner = false;

  ngOnInit() {
    const accepted = localStorage.getItem('cookies_accepted');
    if (!accepted) {
      this.showBanner = true;
    }
  }

  accept() {
    localStorage.setItem('cookies_accepted', 'true');
    this.showBanner = false;
  }

  reject() {
    localStorage.setItem('cookies_accepted', 'false');
    this.showBanner = false;
  }
}