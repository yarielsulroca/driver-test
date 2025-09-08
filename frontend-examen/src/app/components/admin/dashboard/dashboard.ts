import { Component, OnInit, ChangeDetectorRef, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';
import { firstValueFrom } from 'rxjs';
import { environment } from '../../../../environments/environment';

@Component({
  selector: 'app-dashboard',
  imports: [CommonModule, HttpClientModule],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.scss',
  standalone: true
})
export class Dashboard implements OnInit {
  @Output() cambiarSeccion = new EventEmitter<string>();

  // Estadísticas del dashboard
  dashboardStats = {
    oficinas: 0,
    categorias: 0,
    examenes: 0,
    preguntas: 0
  };
  
  loading = false;
  error = '';

  constructor(
    private http: HttpClient,
    private router: Router,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    console.log('🚀 Componente Dashboard inicializado');
    this.cargarEstadisticas();
  }

  async cargarEstadisticas() {
    this.loading = true;
    this.error = '';
    this.cdr.detectChanges();
    
    console.log('🔄 Iniciando carga de estadísticas...');
    console.log('📍 API URL:', environment.apiUrl);
    
    // Primero probar la conectividad
    const conectividad = await this.probarConexion();
    if (!conectividad) {
      this.error = 'No se puede conectar al servidor. Verifica que el backend esté corriendo.';
      this.loading = false;
      this.cdr.detectChanges();
      return;
    }
    
    try {
      // Usar el endpoint específico para estadísticas
      console.log('📡 Obteniendo estadísticas del dashboard...');
      
      const statsRes = await firstValueFrom(
        this.http.get<{status: string, data: any}>(`${environment.apiUrl}/dashboard/stats`)
      );
      
      console.log('📊 Estadísticas recibidas:', statsRes);
      
      if (statsRes?.status === 'success' && statsRes?.data) {
        this.dashboardStats = {
          oficinas: statsRes.data.oficinas || 0,
          categorias: statsRes.data.categorias || 0,
          examenes: statsRes.data.examenes || 0,
          preguntas: statsRes.data.preguntas || 0
        };
      } else {
        throw new Error('Respuesta del servidor no válida');
      }
      
      console.log('✅ Estadísticas del dashboard cargadas:', this.dashboardStats);
      this.cdr.detectChanges();
    } catch (error: any) {
      console.error('❌ Error cargando estadísticas:', error);
      
      if (error.status === 0) {
        this.error = 'Error de conectividad. Verifica que el backend esté corriendo en https://examen.test';
      } else if (error.status === 404) {
        this.error = 'Las rutas de la API no existen. Verifica que el backend tenga las rutas /escuelas, /categorias, /examenes, /preguntas';
      } else {
        this.error = `Error al cargar las estadísticas del dashboard: ${error.message}`;
      }
      this.cdr.detectChanges();
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
      console.log('🏁 Carga de estadísticas finalizada');
    }
  }

  // Método para probar la conectividad
  async probarConexion() {
    console.log('🧪 Probando conectividad...');
    try {
      const response = await firstValueFrom(this.http.get(`${environment.apiUrl}/preguntas?per_page=1`));
      console.log('✅ Conexión exitosa:', response);
      return true;
    } catch (error: any) {
      console.error('❌ Error de conectividad:', error);
      return false;
    }
  }

  // Métodos de conteo ya no son necesarios - se obtienen del backend

  // Método para forzar la recarga de estadísticas
  recargarEstadisticas() {
    console.log('🔄 Forzando recarga de estadísticas...');
    this.cargarEstadisticas();
  }

  // Métodos de navegación - Ahora emiten eventos para cambiar sección
  navegarAOficinas() {
    console.log('🔄 Navegando a Oficinas...');
    this.cambiarSeccion.emit('oficinas');
  }

  navegarACategorias() {
    console.log('🔄 Navegando a Categorías...');
    this.cambiarSeccion.emit('categorias');
  }

  navegarAExamenes() {
    console.log('🔄 Navegando a Exámenes...');
    this.cambiarSeccion.emit('examenes');
  }

  navegarAPreguntas() {
    console.log('🔄 Navegando a Preguntas...');
    this.cambiarSeccion.emit('preguntas');
  }
} 