import { Routes } from '@angular/router';
import { AuthGuard } from './core/guards/auth.guard';
import { GuestGuard } from './core/guards/guest.guard';
import { RoleGuard } from './core/guards/role.guard';
import { ProfileCompleteGuard } from './core/guards/profile-complete.guard';

export const routes: Routes = [

  // ==================== RUTAS PÚBLICAS ====================
  {
    path: '',
    loadComponent: () =>
      import('./features/home/home.component').then(m => m.HomeComponent)
  },
  {
    path: 'login',
    loadComponent: () =>
      import('./features/auth/login/login.component').then(m => m.LoginComponent),
    canActivate: [GuestGuard]  // ← Solo invitados
  },
  {
    path: 'register',
    loadComponent: () =>
      import('./features/auth/register/register.component').then(m => m.RegisterComponent),
    canActivate: [GuestGuard]  // ← Solo invitados
  },

  {
  path: 'terms',
  loadComponent: () =>
    import('./features/terms/terms.component').then(m => m.TermsComponent)
},

  // ==================== RUTAS PROTEGIDAS (requieren autenticación) ====================
  {
    path: 'checkout',
    loadComponent: () =>
      import('./features/bookings/pages/checkout/checkout.component')
        .then(m => m.CheckoutComponent),
    canActivate: [AuthGuard, ProfileCompleteGuard]
  },

  {
    path: 'dashboard',
    loadComponent: () =>
      import('./features/dashboard/dashboard.component')
        .then(m => m.DashboardComponent),
    canActivate: [AuthGuard]
  },

  {
    path: 'accommodations/create',
    loadComponent: () =>
      import('./features/accommodations/pages/create/create.component')
        .then(m => m.CreateAccommodationComponent),
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['admin'] }
  },

  {
    path: 'accommodations',
    loadComponent: () =>
      import('./features/accommodations/list/list.component')
        .then(m => m.ListComponent),
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['admin'] }
  },

  // ==================== GUEST ====================
  {
    path: 'guest/dashboard',
    loadComponent: () =>
      import('./features/guest/dashboard/guest-dashboard.component')
        .then(m => m.GuestDashboardComponent),
    canActivate: [AuthGuard]
  },
  {
    path: 'guest/profile',
    loadComponent: () =>
      import('./features/guest/profile/profile.component')
        .then(m => m.ProfileComponent),
    canActivate: [AuthGuard]
  },
  {
    path: 'guest/points',
    loadComponent: () =>
      import('./features/guest/points/points.component')
        .then(m => m.PointsComponent),
    canActivate: [AuthGuard]
  },
  {
    path: 'guest/reviews',
    loadComponent: () =>
      import('./features/guest/reviews/reviews.component')
        .then(m => m.ReviewsComponent),
    canActivate: [AuthGuard]
  },
  {
    path: 'guest/notifications',
    loadComponent: () =>
      import('./features/guest/notifications/notifications.component')
        .then(m => m.NotificationsComponent),
    canActivate: [AuthGuard]
  },
  {
    path: 'my-bookings',
    loadComponent: () =>
      import('./features/guest/reservations/my-bookings/my-bookings.component')
        .then(m => m.MyBookingsComponent),
    canActivate: [AuthGuard]
  },

  {
  path: 'guest/documents',
  loadComponent: () =>
    import('./features/guest/documents/documents.component')
      .then(m => m.DocumentsComponent),
  canActivate: [AuthGuard]
},


  // ==================== ADMIN ====================

  {
    path: 'admin/dashboard',
    loadComponent: () => import('./features/admin/dashboard/admin-dashboard.component').then(m => m.AdminDashboardComponent),
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['admin'] }
  },

  {
  path: 'analytics',
  loadComponent: () => import('./features/admin/analytics/analytics.component').then(m => m.AnalyticsComponent),
  canActivate: [AuthGuard, RoleGuard],
  data: { roles: ['admin'] }
},


  // ==================== OWNER ====================
    {
    path: 'owner/dashboard',
    loadComponent: () => import('./features/owner/dashboard/owner-dashboard.component').then(m => m.OwnerDashboardComponent),
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['owner'] }
    },


  // ==================== STAFF ====================
  {
    path: 'staff/dashboard',
    loadComponent: () => import('./features/staff/dashboard/staff-dashboard.component').then(m => m.StaffDashboardComponent),
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['staff'] }
  },



  // ==================== RUTAS PARAMETRIZADAS ESPECÍFICAS ====================
  {
    path: 'admin/accommodations/:id/edit',
    loadComponent: () =>
      import('./features/accommodations/pages/edit/edit.component')
        .then(m => m.EditComponent),
    canActivate: [AuthGuard],
    data: { role: 'admin' }
  },

  {
    path: 'bookings/:id/confirmation',
    loadComponent: () =>
      import('./features/bookings/pages/confirmation/confirmation.component')
        .then(m => m.ConfirmationComponent)
  },

  // ==================== RUTAS CON PARÁMETRO SIMPLE ====================
  {
    path: 'accommodations/:id',
    loadComponent: () =>
      import('./features/accommodations/pages/detail/detail.component')
        .then(m => m.AccommodationDetailComponent)
  },

  {
    path: 'bookings/:id',
    loadComponent: () =>
      import('./features/bookings/pages/detail/detail.component')
        .then(m => m.DetailComponent)
  },

  {
    path: 'payment/:id',
    loadComponent: () =>
      import('./features/guest/payment/payment.component')
        .then(m => m.PaymentComponent)
  },

  {
    path: 'experiences',
    loadComponent: () => import('./features/experiences/experiences.component').then(m => m.ExperiencesComponent)
  },

  // ==================== WILDCARD ====================

  {
    path: '404',
    loadComponent: () => import('./features/not-found/not-found.component').then(m => m.NotFoundComponent)
  },

  {
    path: '**',
    redirectTo: '/404'
  }
];