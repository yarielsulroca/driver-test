import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { Subject } from 'rxjs';
import { takeUntil } from 'rxjs/operators';

import { ApiService } from '../../../services/api.service';
import { NotificationService } from '../../../services/notification.service';
import { LoadingService } from '../../../services/loading.service';

interface Examen {
  examen_id: number;
  titulo: string;
  descripcion: string;
  tiempo_limite: number;
  duracion_minutos: number;
  puntaje_minimo: number;
  dificultad: string;
  estado: string;
}

interface ExamenAsignado {
  examen_conductor_id: number;
  examen_id: number;
  conductor_id: number;
  estado: string;
  fecha_inicio: string;
  fecha_fin: string;
  puntaje_obtenido: number;
  tiempo_utilizado: number;
  intentos_restantes: number;
  titulo: string;
  descripcion: string;
  tiempo_limite: number;
  duracion_minutos: number;
  puntaje_minimo: number;
  dificultad: string;
}

interface Conductor {
  conductor_id: number;
  usuario_id: number;
  estado: string;
  documentos_presentados: string;
  usuario?: {
    nombre: string;
    apellido: string;
    dni: string;
    email: string;
  };
}

@Component({
  selector: 'app-conductor-examenes',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './conductor-examenes.html',
  styleUrls: ['./conductor-examenes.scss']
})
export class ConductorExamenesComponent implements OnInit, OnDestroy {
  private destroy$ = new Subject<void>();

  conductores: Conductor[] = [];
  examenes: Examen[] = [];
  examenesAsignados: ExamenAsignado[] = [];
  
  conductorSeleccionado: Conductor | null = null;
  examenSeleccionado: Examen | null = null;
  
  intentosRestantes: number = 3;
  
  loading: boolean = false;
  showAsignarForm: boolean = false;

  constructor(
    private apiService: ApiService,
    private notificationService: NotificationService,
    private loadingService: LoadingService,
    private router: Router
  ) {}

  ngOnInit() {
    this.cargarDatos();
  }

  ngOnDestroy() {
    this.destroy$.next();
    this.destroy$.complete();
  }

  async cargarDatos() {
    this.loading = true;
    
    try {
      // Cargar conductores
      const conductoresResponse = await this.apiService.get('/conductores').toPromise();
      if (conductoresResponse?.status === 'success') {
        this.conductores = conductoresResponse.data as Conductor[];
      }

      // Cargar exámenes
      const examenesResponse = await this.apiService.get('/examenes').toPromise();
      if (examenesResponse?.status === 'success') {
        this.examenes = examenesResponse.data as Examen[];
      }

    } catch (error: any) {
      console.error('Error al cargar datos:', error);
      this.notificationService.error('❌ Error al cargar los datos');
    } finally {
      this.loading = false;
    }
  }

  seleccionarConductor(conductor: Conductor) {
    this.conductorSeleccionado = conductor;
    this.cargarExamenesConductor(conductor.conductor_id);
    this.showAsignarForm = false;
  }

  async cargarExamenesConductor(conductorId: number) {
    this.loading = true;
    
    try {
      const response = await this.apiService.get(`/conductores/${conductorId}/examenes`).toPromise();
      if (response?.status === 'success') {
        this.examenesAsignados = response.data as ExamenAsignado[];
      }
    } catch (error: any) {
      console.error('Error al cargar exámenes del conductor:', error);
      this.notificationService.error('❌ Error al cargar exámenes del conductor');
    } finally {
      this.loading = false;
    }
  }

  mostrarFormularioAsignar() {
    if (!this.conductorSeleccionado) {
      this.notificationService.warning('⚠️ Selecciona un conductor primero');
      return;
    }
    this.showAsignarForm = true;
  }

  async asignarExamen() {
    if (!this.conductorSeleccionado || !this.examenSeleccionado) {
      this.notificationService.warning('⚠️ Selecciona un conductor y un examen');
      return;
    }

    this.loading = true;
    
    try {
      const data = {
        conductor_id: this.conductorSeleccionado.conductor_id,
        examen_id: this.examenSeleccionado.examen_id,
        intentos_restantes: this.intentosRestantes
      };

      const response = await this.apiService.post('/examen-conductor/asignar', data).toPromise();
      
      if (response?.status === 'success') {
        this.notificationService.success('✅ Examen asignado exitosamente');
        this.showAsignarForm = false;
        this.examenSeleccionado = null;
        this.intentosRestantes = 3;
        
        // Recargar exámenes del conductor
        if (this.conductorSeleccionado) {
          this.cargarExamenesConductor(this.conductorSeleccionado.conductor_id);
        }
      } else {
        throw new Error(response?.message || 'Error al asignar examen');
      }
    } catch (error: any) {
      console.error('Error al asignar examen:', error);
      this.notificationService.error('❌ Error al asignar examen');
    } finally {
      this.loading = false;
    }
  }

  async eliminarAsignacion(examenConductorId: number) {
    if (!confirm('¿Estás seguro de que quieres eliminar esta asignación?')) {
      return;
    }

    this.loading = true;
    
    try {
      const response = await this.apiService.delete(`/examen-conductor/${examenConductorId}`).toPromise();
      
      if (response?.status === 'success') {
        this.notificationService.success('✅ Asignación eliminada exitosamente');
        
        // Recargar exámenes del conductor
        if (this.conductorSeleccionado) {
          this.cargarExamenesConductor(this.conductorSeleccionado.conductor_id);
        }
      } else {
        throw new Error(response?.message || 'Error al eliminar asignación');
      }
    } catch (error: any) {
      console.error('Error al eliminar asignación:', error);
      this.notificationService.error('❌ Error al eliminar asignación');
    } finally {
      this.loading = false;
    }
  }

  getEstadoTexto(estado: string): string {
    const estados: { [key: string]: string } = {
      'pendiente': 'Pendiente',
      'en_progreso': 'En Progreso',
      'completado': 'Completado',
      'aprobado': 'Aprobado',
      'reprobado': 'Reprobado'
    };
    return estados[estado] || estado;
  }

  getEstadoClass(estado: string): string {
    const classes: { [key: string]: string } = {
      'pendiente': 'estado-pendiente',
      'en_progreso': 'estado-progreso',
      'completado': 'estado-completado',
      'aprobado': 'estado-aprobado',
      'reprobado': 'estado-reprobado'
    };
    return classes[estado] || '';
  }

  getDificultadTexto(dificultad: string): string {
    const dificultades: { [key: string]: string } = {
      'facil': 'Fácil',
      'medio': 'Medio',
      'dificil': 'Difícil'
    };
    return dificultades[dificultad] || dificultad;
  }

  getDificultadClass(dificultad: string): string {
    const classes: { [key: string]: string } = {
      'facil': 'dificultad-facil',
      'medio': 'dificultad-medio',
      'dificultad': 'dificultad-dificil'
    };
    return classes[dificultad] || '';
  }

  getNombreCompleto(conductor: Conductor): string {
    if (conductor.usuario) {
      return `${conductor.usuario.nombre} ${conductor.usuario.apellido}`;
    }
    return 'Usuario no disponible';
  }

  cancelarAsignacion() {
    this.showAsignarForm = false;
    this.examenSeleccionado = null;
    this.intentosRestantes = 3;
  }

  volver() {
    this.router.navigate(['/conductores']);
  }
}
