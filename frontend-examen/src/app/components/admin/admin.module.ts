import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';

// Rutas del módulo admin usando componentes standalone
const routes: Routes = [
  {
    path: '',
    loadComponent: () => import('./admin').then(m => m.Admin),
    children: [
      { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
      { 
        path: 'dashboard', 
        loadComponent: () => import('./dashboard/dashboard').then(m => m.Dashboard)
      },
      { 
        path: 'categorias', 
        loadComponent: () => import('./categorias/categorias').then(m => m.Categorias)
      },
      { 
        path: 'preguntas', 
        loadComponent: () => import('./preguntas/preguntas').then(m => m.Preguntas)
      },
      { 
        path: 'preguntas/crear', 
        loadComponent: () => import('./preguntas/pregunta-creator/pregunta-creator').then(m => m.PreguntaCreator)
      },
      { 
        path: 'examenes', 
        loadComponent: () => import('./examenes/examenes').then(m => m.Examenes)
      },
      { 
        path: 'oficinas', 
        loadComponent: () => import('./oficinas/oficinas').then(m => m.Oficinas)
      }
    ]
  }
];

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(routes)
  ]
})
export class AdminModule { }