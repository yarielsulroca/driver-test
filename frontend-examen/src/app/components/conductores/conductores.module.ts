import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';

// Rutas del módulo conductores usando componentes standalone
const routes: Routes = [
  { 
    path: '', 
    loadComponent: () => import('./conductores').then(m => m.Conductores)
  }
];

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(routes)
  ]
})
export class ConductoresModule { }