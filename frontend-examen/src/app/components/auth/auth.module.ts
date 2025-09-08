import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';

// Rutas del módulo auth usando componentes standalone
const routes: Routes = [
  { 
    path: 'login', 
    loadComponent: () => import('./login/login').then(m => m.Login)
  },
  { 
    path: '', 
    redirectTo: 'login', 
    pathMatch: 'full' 
  }
];

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(routes)
  ]
})
export class AuthModule { }