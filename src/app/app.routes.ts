import { Routes } from '@angular/router';
import { LoginComponent } from './components/auth/login/login';
import { ExamListComponent } from './components/exams/exam-list/exam-list';
import { ExamResultsTableComponent } from './components/exams/exam-results-table/exam-results-table';
import { InicioComponent } from './components/inicio/inicio';

export const routes: Routes = [
  { path: '', component: InicioComponent, pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  { path: 'exams', component: ExamListComponent },
  { path: 'resultados', component: ExamResultsTableComponent },
  { path: '**', redirectTo: '' }
]; 