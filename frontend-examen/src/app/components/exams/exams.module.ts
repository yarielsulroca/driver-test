import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';

// Rutas del módulo exams usando componentes standalone
const routes: Routes = [
  { 
    path: '', 
    loadComponent: () => import('./exam-list/exam-list').then(m => m.ExamList)
  },
  { 
    path: 'crear', 
    loadComponent: () => import('./exam-creator/exam-creator').then(m => m.ExamCreator)
  },
  { 
    path: 'tomar/:id', 
    loadComponent: () => import('./exam-taker/exam-taker').then(m => m.ExamTakerComponent)
  },
  { 
    path: 'resultados', 
    loadComponent: () => import('./exam-results-table/exam-results-table').then(m => m.ExamResultsTable)
  }
];

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(routes)
  ]
})
export class ExamsModule { }