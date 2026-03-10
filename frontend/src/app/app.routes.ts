import { Routes } from '@angular/router';
import { AuthGuard } from './core/guards/auth.guard';
import { RoleGuard } from './core/guards/role.guard';

export const routes: Routes = [
  // Rutas públicas

  {
    path: '',
    loadComponent: () => import('./features/home/home.component').then(m => m.HomeComponent)
  },
  {
    path: 'accommodations/:id',
    loadComponent: () => import('./features/accommodations/pages/detail/detail.component').then(m => m.AccommodationDetailComponent)
  },
  {
    path: 'login',
    loadComponent: () => import('./features/auth/login/login.component').then(m => m.LoginComponent)
  },
  {
    path: 'register',
    loadComponent: () => import('./features/auth/register/register.component').then(m => m.RegisterComponent)
  },

  // Dashboard (cada rol ve su propio dashboard)
  {
    path: 'dashboard',
    loadComponent: () => import('./features/dashboard/dashboard.component').then(m => m.DashboardComponent),
    canActivate: [AuthGuard]
  },

  // ALOJAMIENTOS - SOLO ADMIN (gestión completa)
  {
    path: 'accommodations',
    loadComponent: () => import('./features/accommodations/list/list.component').then(m => m.ListComponent),
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['admin'] } 
  },

  {
    path: 'accommodations/create',
    loadComponent: () => import('./features/accommodations/pages/create/create.component').then(m => m.CreateAccommodationComponent),
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['admin'] } // SOLO admin
  },
  /*{
    path: 'accommodations/:id/edit',
    loadComponent: () => import('./features/accommodations/pages/edit/edit.component').then(m => m.EditComponent),
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['admin'] } // SOLO admin
  },

  // MIS ALOJAMIENTOS (para owners) - SOLO VER LOS SUYOS
  {
    path: 'my-accommodations',
    loadComponent: () => import('./features/accommodations/pages/my-accommodations/my-accommodations.component').then(m => m.MyAccommodationsComponent),
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['owner'] } // SOLO owner, solo ve los suyos
  },

  // RESERVAS - SOLO ADMIN Y STAFF
  {
    path: 'bookings',
    loadComponent: () => import('./features/bookings/list/list.component').then(m => m.ListComponent),
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['admin', 'staff'] }
  },
  {
    path: 'bookings/create',
    loadComponent: () => import('./features/bookings/pages/create/create.component').then(m => m.CreateComponent),
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['admin', 'staff'] }
  },

  // TAREAS DE LIMPIEZA - SOLO STAFF
  {
    path: 'cleaning',
    loadComponent: () => import('./features/cleaning/tasks/tasks.component').then(m => m.TasksComponent),
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['staff'] } // SOLO personal de limpieza
  },

  // MIS RESERVAS (para guests)
  {
    path: 'my-bookings',
    loadComponent: () => import('./features/bookings/pages/my-bookings/my-bookings.component').then(m => m.MyBookingsComponent),
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['guest'] } // SOLO guests ven sus reservas
  },*/

  // Redirecciones
  {
    path: '',
    redirectTo: '/dashboard',
    pathMatch: 'full'
  },
  {
    path: '**',
    redirectTo: '/dashboard'
  }
];