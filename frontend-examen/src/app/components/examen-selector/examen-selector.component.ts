import { Component, Input, Output, EventEmitter, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../services/api.service';

export interface Examen {
  examen_id: number;
  titulo: string;
  descripcion: string;
  categoria_id: number;
  categoria_codigo: string;
  categoria_nombre: string;
  dificultad: string;
  puntaje_minimo: number;
  tiempo_limite: number;
  duracion_minutos: number;
  numero_preguntas?: number;
  estado: string;
}

export interface Conductor {
  conductor_id: number;
  usuario_id?: number;
  estado?: string;
  documentos_presentados?: string;
  created_at?: string;
  updated_at?: string;
  nombre: string;
  apellido: string;
  email?: string;
  dni: string;
  total_examenes_asignados?: number;
  examenes_aprobados?: number;
  examenes_pendientes?: number;
  categorias_asignadas?: any[];
  categorias_aprobadas?: any[];
  categorias_pendientes?: any[];
  resumen_categorias?: any;
}

@Component({
  selector: 'app-examen-selector',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="modal-overlay" *ngIf="isOpen" (click)="closeModal()">
      <div class="modal-content" (click)="$event.stopPropagation()">
        <!-- Header -->
        <div class="modal-header">
          <div class="header-content">
            <h1 class="modal-title">Seleccionar Examen</h1>
            <div class="conductor-info" *ngIf="conductor">
              <div class="conductor-name">{{ conductor.nombre }} {{ conductor.apellido }}</div>
              <div class="conductor-dni">DNI: {{ conductor.dni }}</div>
              <div class="conductor-status" *ngIf="getConductorStatus()">
                {{ getConductorStatus() }}
              </div>
            </div>
          </div>
          <button class="close-btn" (click)="closeModal()" aria-label="Cerrar">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <!-- Body -->
        <div class="modal-body">
          <!-- Loading State -->
          <div *ngIf="loading" class="loading-container">
            <div class="loading-spinner"></div>
            <p class="loading-text">Cargando exámenes disponibles...</p>
          </div>
          
          <!-- Error State -->
          <div *ngIf="error && !loading" class="error-container">
            <div class="error-icon">
              <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="error-title">Error al cargar exámenes</h3>
            <p class="error-message">{{ error }}</p>
            <button class="retry-btn" (click)="cargarExamenesDisponibles()">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              Reintentar
            </button>
          </div>
          
          <!-- Exámenes List -->
          <div *ngIf="!loading && !error && examenesDisponibles.length > 0" class="examenes-grid">
            <div *ngFor="let examen of examenesDisponibles" class="examen-card" (click)="seleccionarExamen(examen)">
              <div class="card-header">
                <h3 class="examen-title">{{ examen.titulo }}</h3>
                <span class="examen-categoria">{{ examen.categoria_nombre }}</span>
              </div>
              
              <div class="card-body">
                <p class="examen-descripcion">{{ examen.descripcion || 'Sin descripción' }}</p>
                
                <div class="examen-stats">
                  <div class="stat-item">
                    <div class="stat-icon">
                      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">{{ examen.duracion_minutos || examen.tiempo_limite || 0 }}</span>
                      <span class="stat-label">minutos</span>
                    </div>
                  </div>
                  
                  <div class="stat-item">
                    <div class="stat-icon">
                      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">{{ examen.numero_preguntas || 'N/A' }}</span>
                      <span class="stat-label">preguntas</span>
                    </div>
                  </div>
                  
                  <div class="stat-item">
                    <div class="stat-icon">
                      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">{{ examen.puntaje_minimo || 70 }}%</span>
                      <span class="stat-label">mínimo</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="card-footer">
                <button class="btn-comenzar">
                  <span>Comenzar</span>
                  <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
          
          <!-- Empty State -->
          <div *ngIf="!loading && !error && examenesDisponibles.length === 0" class="empty-container">
            <div class="empty-icon">
              <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <h3 class="empty-title">No hay exámenes disponibles</h3>
            <p class="empty-message">No se encontraron exámenes disponibles para este conductor en este momento.</p>
          </div>
        </div>
      </div>
    </div>
  `,
  styles: [`
    /* Modal Overlay */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.6);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      padding: 20px;
    }

    .modal-content {
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      max-width: 800px;
      width: 100%;
      max-height: 90vh;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    /* Header */
    .modal-header {
      padding: 24px 32px;
      border-bottom: 1px solid #f1f5f9;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .header-content {
      flex: 1;
    }

    .modal-title {
      margin: 0 0 12px 0;
      color: white;
      font-size: 24px;
      font-weight: 700;
      line-height: 1.2;
    }

    .conductor-info {
      background: rgba(255, 255, 255, 0.1);
      padding: 12px 16px;
      border-radius: 8px;
      backdrop-filter: blur(10px);
    }

    .conductor-name {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 4px;
    }

    .conductor-dni {
      font-size: 14px;
      opacity: 0.9;
      margin-bottom: 4px;
    }

    .conductor-status {
      font-size: 12px;
      opacity: 0.8;
      font-style: italic;
    }

    .close-btn {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s;
      color: #64748b;
    }

    .close-btn:hover {
      background: #f1f5f9;
      border-color: #cbd5e1;
      color: #475569;
    }

    /* Body */
    .modal-body {
      padding: 32px;
      flex: 1;
      overflow-y: auto;
    }

    /* Loading State */
    .loading-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 60px 20px;
      text-align: center;
    }

    .loading-spinner {
      width: 40px;
      height: 40px;
      border: 3px solid #f1f5f9;
      border-top: 3px solid #3b82f6;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 16px;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .loading-text {
      color: #64748b;
      font-size: 16px;
      margin: 0;
    }

    /* Error State */
    .error-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 60px 20px;
      text-align: center;
    }

    .error-icon {
      color: #ef4444;
      margin-bottom: 16px;
    }

    .error-title {
      color: #1e293b;
      font-size: 20px;
      font-weight: 600;
      margin: 0 0 8px 0;
    }

    .error-message {
      color: #64748b;
      font-size: 16px;
      margin: 0 0 24px 0;
      line-height: 1.5;
    }

    .retry-btn {
      background: #3b82f6;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 12px 24px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s;
    }

    .retry-btn:hover {
      background: #2563eb;
    }

    /* Exámenes Grid */
    .examenes-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 24px;
    }

    .examen-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 24px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .examen-card:hover {
      border-color: #3b82f6;
      box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.1), 0 4px 6px -2px rgba(59, 130, 246, 0.05);
      transform: translateY(-2px);
    }

    /* Card Header */
    .card-header {
      margin-bottom: 16px;
    }

    .examen-title {
      color: #1e293b;
      font-size: 18px;
      font-weight: 600;
      margin: 0 0 8px 0;
      line-height: 1.3;
    }

    .examen-categoria {
      background: #dbeafe;
      color: #1e40af;
      padding: 4px 12px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 500;
      display: inline-block;
    }

    /* Card Body */
    .card-body {
      flex: 1;
      margin-bottom: 20px;
    }

    .examen-descripcion {
      color: #64748b;
      font-size: 14px;
      line-height: 1.5;
      margin: 0 0 20px 0;
    }

    .examen-stats {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .stat-item {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .stat-icon {
      width: 32px;
      height: 32px;
      background: #f8fafc;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #64748b;
      flex-shrink: 0;
    }

    .stat-content {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .stat-value {
      color: #1e293b;
      font-size: 16px;
      font-weight: 600;
    }

    .stat-label {
      color: #64748b;
      font-size: 12px;
      font-weight: 500;
    }

    /* Card Footer */
    .card-footer {
      margin-top: auto;
    }

    .btn-comenzar {
      background: #3b82f6;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 12px 20px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      transition: all 0.2s;
    }

    .btn-comenzar:hover {
      background: #2563eb;
    }

    /* Empty State */
    .empty-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 60px 20px;
      text-align: center;
    }

    .empty-icon {
      color: #cbd5e1;
      margin-bottom: 16px;
    }

    .empty-title {
      color: #1e293b;
      font-size: 20px;
      font-weight: 600;
      margin: 0 0 8px 0;
    }

    .empty-message {
      color: #64748b;
      font-size: 16px;
      margin: 0;
      line-height: 1.5;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .modal-overlay {
        padding: 16px;
      }

      .modal-content {
        max-height: 95vh;
      }

      .modal-header {
        padding: 20px 24px;
      }

      .modal-title {
        font-size: 20px;
      }

      .modal-body {
        padding: 24px;
      }

      .examenes-grid {
        grid-template-columns: 1fr;
        gap: 16px;
      }

      .examen-card {
        padding: 20px;
      }
    }

    @media (max-width: 480px) {
      .modal-header {
        padding: 16px 20px;
      }

      .modal-body {
        padding: 20px;
      }

      .examen-card {
        padding: 16px;
      }
    }
  `]
})
export class ExamenSelectorComponent implements OnInit {
  @Input() isOpen = false;
  @Input() conductor: Conductor | null = null;
  @Output() close = new EventEmitter<void>();
  @Output() examenSeleccionado = new EventEmitter<Examen>();

  loading = false;
  error = '';
  examenesDisponibles: Examen[] = [];

  constructor(private apiService: ApiService) {}

  ngOnInit() {
    console.log('ExamenSelectorComponent ngOnInit - isOpen:', this.isOpen, 'conductor:', this.conductor);
    if (this.isOpen && this.conductor) {
      this.cargarExamenesDisponibles();
    }
  }

  async cargarExamenesDisponibles() {
    console.log('Cargando todos los exámenes disponibles...');
    this.loading = true;
    this.error = '';

    try {
      // Cargar todos los exámenes activos
      const response = await this.apiService.get('/examenes').toPromise();

      console.log('Respuesta del servidor:', response);

      if (response && response.status === 'success') {
        this.examenesDisponibles = (response.data as Examen[]) || [];
        console.log('Exámenes cargados:', this.examenesDisponibles);
        
        // Si no hay exámenes y hay un conductor seleccionado, verificar exámenes reprobados
        if (this.examenesDisponibles.length === 0 && this.conductor) {
          await this.verificarExamenesReprobados();
        }
      } else {
        this.error = 'No se pudieron cargar los exámenes disponibles';
        console.log('Error en respuesta:', response);
      }
    } catch (error: any) {
      console.error('Error al cargar exámenes:', error);
      this.error = error.message || 'Error al cargar exámenes disponibles';
    } finally {
      this.loading = false;
    }
  }

  async verificarExamenesReprobados() {
    if (!this.conductor) return;
    
    try {
      const response = await this.apiService.get('/examenes/reprobados', {
        conductor_id: this.conductor.conductor_id
      }).toPromise();
      
      if (response && response.status === 'success' && (response.data as any[]).length > 0) {
        this.error = 'Has reprobado todos los exámenes disponibles. Debes aprobar otro examen para continuar.';
      } else {
        this.error = 'No hay exámenes disponibles en este momento.';
      }
    } catch (error) {
      console.error('Error verificando exámenes reprobados:', error);
      this.error = 'No hay exámenes disponibles en este momento.';
    }
  }

  getConductorStatus(): string {
    if (!this.conductor) return '';
    
    const esNuevo = (this.conductor.total_examenes_asignados || 0) === 0;
    const tieneExamenIniciado = (this.conductor.categorias_pendientes || []).some(
      cat => cat.estado === 'Iniciado'
    );
    const tieneCategoriasReprobadas = (this.conductor.categorias_pendientes || []).some(
      cat => cat.estado === 'Reprobado' && (cat.intentos_maximos || 0) > (cat.intentos_realizados || 0)
    );
    
    if (esNuevo) {
      return 'Conductor nuevo - Sin exámenes asignados';
    } else if (tieneExamenIniciado) {
      const categoriaIniciada = (this.conductor.categorias_pendientes || []).find(
        cat => cat.estado === 'Iniciado'
      );
      return `Examen iniciado: ${categoriaIniciada?.categoria_codigo || 'Categoría actual'}`;
    } else if (tieneCategoriasReprobadas) {
      const categoriasReprobadas = (this.conductor.categorias_pendientes || []).filter(
        cat => cat.estado === 'Reprobado' && (cat.intentos_maximos || 0) > (cat.intentos_realizados || 0)
      );
      return `Categorías con intentos: ${categoriasReprobadas.map(cat => `${cat.categoria_codigo} (${cat.intentos_realizados}/${cat.intentos_maximos})`).join(', ')}`;
    }
    
    return 'Sin exámenes activos';
  }

  seleccionarExamen(examen: Examen) {
    this.examenSeleccionado.emit(examen);
    this.closeModal();
  }

  closeModal() {
    this.close.emit();
  }
}