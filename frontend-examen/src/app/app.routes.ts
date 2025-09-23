import { Routes } from '@angular/router';
import { Inicio } from './components/inicio/inicio';
import { Login } from './components/auth/login/login';
import { ExamResultsTable } from './components/exams/exam-results-table/exam-results-table';
import { ExamList } from './components/exams/exam-list/exam-list';
import { ExamB2TakerComponent } from './components/exams/exam-b2-taker/exam-b2-taker';
import { ExamTakerComponent } from './components/exams/exam-taker/exam-taker';
import { ExamCreator } from './components/exams/exam-creator/exam-creator';
import { Conductores } from './components/conductores/conductores';
import { ConductoresSimpleComponent } from './components/conductores/conductores-simple';
import { ConductorExamenComponent } from './components/conductores/conductor-examen/conductor-examen';
import { ConductorEditor } from './components/conductores/conductor-editor/conductor-editor';
import { ConductorCrearComponent } from './components/conductores/conductor-crear/conductor-crear';
import { ConductorExamenesComponent } from './components/conductores/conductor-examenes/conductor-examenes';
import { Admin } from './components/admin/admin';
import { PreguntaCreator } from './components/admin/preguntas/pregunta-creator/pregunta-creator';
import { PreguntaEditor } from './components/admin/preguntas/pregunta-editor/pregunta-editor';
import { OficinaCreator } from './components/admin/oficinas/oficina-creator/oficina-creator';
import { OficinaEditor } from './components/admin/oficinas/oficina-editor/oficina-editor';
import { CategoriaCreator } from './components/admin/categorias/categoria-creator/categoria-creator';
import { CategoriaEditor } from './components/admin/categorias/categoria-editor/categoria-editor';
import { ExamCreator as AdminExamCreator } from './components/admin/exams/exam-creator/exam-creator';
import { ExamEditor } from './components/admin/exams/exam-editor/exam-editor';


export const routes: Routes = [
  { path: '', component: Inicio },
  { path: 'inicio', component: Inicio },
  { path: 'login', component: Login },
  { path: 'resultados', component: ExamResultsTable },
  { path: 'examenes', component: ExamList },
  { path: 'examen-b2', component: ExamB2TakerComponent }, // Ruta para el examen B2 específico
  { path: 'examen-b2/:id', component: ExamB2TakerComponent }, // Ruta para el examen B2 con ID específico
  { path: 'examen/:id', component: ExamTakerComponent }, // Ruta para tomar un examen específico
  { path: 'examen', component: ExamList }, // Ruta para lista de exámenes (debe ir después de la ruta con parámetro)
  { path: 'crear-examen', component: ExamCreator },
  { path: 'conductores', component: Conductores },
  { path: 'conductores-simple', component: ConductoresSimpleComponent },
  { path: 'conductores/crear', component: ConductorCrearComponent },
  { path: 'conductores/editar/:id', component: ConductorEditor },
  { path: 'conductores/examenes', component: ConductorExamenesComponent }, // Nueva ruta para gestión de exámenes por conductor
  { path: 'conductor-examen', component: ConductorExamenComponent }, // Nueva ruta para conductor-examen
  
  // IMPORTANTE: Rutas específicas de admin DEBEN ir ANTES que las rutas generales
  { path: 'admin/preguntas/crear', component: PreguntaCreator },
  { path: 'admin/preguntas/editar/:id', component: PreguntaEditor },
  { path: 'admin/oficinas/crear', component: OficinaCreator },
  { path: 'admin/oficinas/editar/:id', component: OficinaEditor },
  { path: 'admin/categorias/crear', component: CategoriaCreator },
  { path: 'admin/categorias/editar/:id', component: CategoriaEditor },
  { path: 'admin/examenes/crear', component: AdminExamCreator },
  { path: 'admin/examenes/editar/:id', component: ExamEditor },
  
  // Rutas generales de admin (deben ir DESPUÉS de las específicas)
  { path: 'admin', component: Admin },
  { path: 'admin/preguntas', component: Admin },
  { path: 'admin/oficinas', component: Admin },
  { path: 'admin/categorias', component: Admin },
  { path: 'admin/examenes', component: Admin },
  
  // Rutas de debug para verificar componentes
  { path: 'debug/oficina-editor/:id', component: OficinaEditor },
  { path: 'debug/categoria-editor/:id', component: CategoriaEditor },
  { path: '**', redirectTo: '' }
];
