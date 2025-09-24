import { Component, Input, Output, EventEmitter, OnInit, OnChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api.service';

export interface Conductor {
  conductor_id: number;
  usuario_id: number;
  estado: string;
  documentos_presentados?: string;
  created_at?: string;
  updated_at?: string;
  nombre: string;
  apellido: string;
  email: string;
  dni: string;
  total_examenes_asignados: number;
  examenes_aprobados: number;
  examenes_pendientes: number;
  categorias_asignadas: any[];
  categorias_aprobadas: any[];
  categorias_pendientes: any[];
  resumen_categorias: any;
}

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

@Component({
  selector: 'app-conductor-edit-modal',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <div class="modal-overlay" *ngIf="isOpen" (click)="closeModal()">
      <div class="modal-content" (click)="$event.stopPropagation()">
        <!-- Header -->
        <div class="modal-header">
          <div class="header-content">
            <h1 class="modal-title">Editar Conductor</h1>
            <div class="conductor-info" *ngIf="conductor">
              <div class="conductor-name">{{ conductor.nombre }} {{ conductor.apellido }}</div>
              <div class="conductor-dni">DNI: {{ conductor.dni }}</div>
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
            <p class="loading-text">Cargando datos...</p>
          </div>
          
          <!-- Form Content -->
          <div *ngIf="!loading" class="form-container">
            <!-- Información Personal -->
            <div class="form-section">
              <h3 class="section-title">Información Personal</h3>
              <div class="form-grid">
                <div class="form-group">
                  <label for="nombre">Nombre *</label>
                  <input 
                    type="text" 
                    id="nombre" 
                    [(ngModel)]="conductorData.nombre" 
                    class="form-input"
                    required
                  >
                </div>
                
                <div class="form-group">
                  <label for="apellido">Apellido *</label>
                  <input 
                    type="text" 
                    id="apellido" 
                    [(ngModel)]="conductorData.apellido" 
                    class="form-input"
                    required
                  >
                </div>
                
                <div class="form-group">
                  <label for="dni">DNI *</label>
                  <input 
                    type="text" 
                    id="dni" 
                    [(ngModel)]="conductorData.dni" 
                    class="form-input"
                    required
                  >
                </div>
                
                <div class="form-group">
                  <label for="email">Email</label>
                  <input 
                    type="email" 
                    id="email" 
                    [(ngModel)]="conductorData.email" 
                    class="form-input"
                  >
                </div>
                
                <div class="form-group">
                  <label for="estado">Estado</label>
                  <select id="estado" [(ngModel)]="conductorData.estado" class="form-select">
                    <option value="p">Pendiente</option>
                    <option value="b">Bloqueado</option>
                    <option value="a">Activo</option>
                  </select>
                </div>
                
                <div class="form-group full-width">
                  <label for="documentos">Documentos Presentados</label>
                  <textarea 
                    id="documentos" 
                    [(ngModel)]="conductorData.documentos_presentados" 
                    class="form-textarea"
                    rows="3"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Gestión de Exámenes -->
            <div class="form-section">
              <h3 class="section-title">Gestión de Exámenes</h3>
              
              <!-- Estado Actual -->
              <div class="current-status" *ngIf="conductor">
                <h4>Estado Actual</h4>
                <div class="status-grid">
                  <div class="status-item">
                    <span class="status-label">Total Exámenes:</span>
                    <span class="status-value">{{ conductor.total_examenes_asignados || 0 }}</span>
                  </div>
                  <div class="status-item">
                    <span class="status-label">Aprobados:</span>
                    <span class="status-value">{{ conductor.examenes_aprobados || 0 }}</span>
                  </div>
                  <div class="status-item">
                    <span class="status-label">Pendientes:</span>
                    <span class="status-value">{{ conductor.examenes_pendientes || 0 }}</span>
                  </div>
                </div>
              </div>

              <!-- Categorías Pendientes -->
              <div class="pending-categories" *ngIf="conductor?.categorias_pendientes?.length">
                <h4>Categorías Pendientes</h4>
                <div class="category-list">
                  <div *ngFor="let categoria of conductor?.categorias_pendientes" class="category-item">
                    <div class="category-info">
                      <span class="category-code">{{ categoria.categoria_codigo }}</span>
                      <span class="category-name">{{ categoria.categoria_nombre }}</span>
                    </div>
                    <div class="category-status">
                      <span class="status-badge" [class]="getStatusClass(categoria.estado)">
                        {{ categoria.estado }}
                      </span>
                      <span class="attempts">{{ categoria.intentos_realizados }}/{{ categoria.intentos_maximos }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Asignar Nuevo Examen -->
              <div class="assign-exam-section">
                <h4>Asignar Nuevo Examen</h4>
                
                <!-- Loading Exámenes -->
                <div *ngIf="loadingExams" class="loading-exams">
                  <div class="loading-spinner small"></div>
                  <span>Cargando exámenes disponibles...</span>
                </div>
                
                <!-- Lista de Exámenes -->
                <div *ngIf="!loadingExams && examenesDisponibles.length > 0" class="exams-list">
                  <div *ngFor="let examen of examenesDisponibles" class="exam-item" (click)="selectExam(examen)">
                    <div class="exam-header">
                      <h5 class="exam-title">{{ examen.titulo }}</h5>
                      <span class="exam-category">{{ examen.categoria_codigo }}</span>
                    </div>
                    <div class="exam-details">
                      <span class="exam-duration">{{ examen.duracion_minutos }} min</span>
                      <span class="exam-questions">{{ examen.numero_preguntas || 'N/A' }} preguntas</span>
                      <span class="exam-difficulty">{{ examen.dificultad }}</span>
                    </div>
                    <div class="exam-description">{{ examen.descripcion || 'Sin descripción' }}</div>
                  </div>
                </div>
                
                <!-- Sin Exámenes -->
                <div *ngIf="!loadingExams && examenesDisponibles.length === 0" class="no-exams">
                  <p>No hay exámenes disponibles para asignar</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" (click)="closeModal()">
            Cancelar
          </button>
          <button type="button" class="btn btn-primary" (click)="saveConductor()" [disabled]="loading">
            <span *ngIf="loading" class="loading-spinner small"></span>
            Guardar Cambios
          </button>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      padding: 20px;
    }

    .modal-content {
      background: white;
      border-radius: 12px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      max-width: 800px;
      width: 100%;
      max-height: 90vh;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

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

    .modal-body {
      padding: 32px;
      flex: 1;
      overflow-y: auto;
    }

    .form-container {
      display: flex;
      flex-direction: column;
      gap: 32px;
    }

    .form-section {
      background: #f8fafc;
      padding: 24px;
      border-radius: 8px;
      border: 1px solid #e2e8f0;
    }

    .section-title {
      margin: 0 0 20px 0;
      color: #1e293b;
      font-size: 18px;
      font-weight: 600;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    .form-group.full-width {
      grid-column: 1 / -1;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    .form-group label {
      margin-bottom: 8px;
      color: #374151;
      font-weight: 500;
      font-size: 14px;
    }

    .form-input, .form-select, .form-textarea {
      padding: 12px 16px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 14px;
      transition: all 0.2s;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-textarea {
      resize: vertical;
      min-height: 80px;
    }

    .current-status {
      margin-bottom: 24px;
    }

    .current-status h4 {
      margin: 0 0 16px 0;
      color: #1e293b;
      font-size: 16px;
      font-weight: 600;
    }

    .status-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
    }

    .status-item {
      background: white;
      padding: 16px;
      border-radius: 6px;
      border: 1px solid #e2e8f0;
      text-align: center;
    }

    .status-label {
      display: block;
      color: #64748b;
      font-size: 12px;
      font-weight: 500;
      margin-bottom: 4px;
    }

    .status-value {
      display: block;
      color: #1e293b;
      font-size: 20px;
      font-weight: 700;
    }

    .pending-categories {
      margin-bottom: 24px;
    }

    .pending-categories h4 {
      margin: 0 0 16px 0;
      color: #1e293b;
      font-size: 16px;
      font-weight: 600;
    }

    .category-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .category-item {
      background: white;
      padding: 16px;
      border-radius: 6px;
      border: 1px solid #e2e8f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .category-info {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .category-code {
      font-weight: 600;
      color: #1e293b;
      font-size: 14px;
    }

    .category-name {
      color: #64748b;
      font-size: 12px;
    }

    .category-status {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .status-badge {
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: 500;
    }

    .status-badge.Iniciado {
      background: #dbeafe;
      color: #1e40af;
    }

    .status-badge.Reprobado {
      background: #fee2e2;
      color: #dc2626;
    }

    .status-badge.Aprobada {
      background: #dcfce7;
      color: #16a34a;
    }

    .attempts {
      color: #64748b;
      font-size: 12px;
    }

    .assign-exam-section h4 {
      margin: 0 0 16px 0;
      color: #1e293b;
      font-size: 16px;
      font-weight: 600;
    }

    .loading-exams {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 20px;
      color: #64748b;
    }

    .exams-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
      max-height: 300px;
      overflow-y: auto;
    }

    .exam-item {
      background: white;
      padding: 16px;
      border-radius: 6px;
      border: 1px solid #e2e8f0;
      cursor: pointer;
      transition: all 0.2s;
    }

    .exam-item:hover {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .exam-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
    }

    .exam-title {
      margin: 0;
      color: #1e293b;
      font-size: 16px;
      font-weight: 600;
    }

    .exam-category {
      background: #e0e7ff;
      color: #3730a3;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: 500;
    }

    .exam-details {
      display: flex;
      gap: 16px;
      margin-bottom: 8px;
    }

    .exam-details span {
      color: #64748b;
      font-size: 12px;
    }

    .exam-description {
      color: #64748b;
      font-size: 12px;
      line-height: 1.4;
    }

    .no-exams {
      text-align: center;
      padding: 40px 20px;
      color: #64748b;
    }

    .modal-footer {
      padding: 24px 32px;
      border-top: 1px solid #f1f5f9;
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      background: #f8fafc;
    }

    .btn {
      padding: 12px 24px;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      border: none;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-secondary {
      background: #f1f5f9;
      color: #64748b;
      border: 1px solid #e2e8f0;
    }

    .btn-secondary:hover {
      background: #e2e8f0;
      color: #475569;
    }

    .btn-primary {
      background: #3b82f6;
      color: white;
    }

    .btn-primary:hover:not(:disabled) {
      background: #2563eb;
    }

    .btn-primary:disabled {
      background: #94a3b8;
      cursor: not-allowed;
    }

    .loading-spinner {
      width: 20px;
      height: 20px;
      border: 2px solid #f1f5f9;
      border-top: 2px solid #3b82f6;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    .loading-spinner.small {
      width: 16px;
      height: 16px;
      border-width: 2px;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .loading-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 60px 20px;
      text-align: center;
    }

    .loading-text {
      color: #64748b;
      font-size: 16px;
      margin: 16px 0 0 0;
    }

    @media (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
      }
      
      .status-grid {
        grid-template-columns: 1fr;
      }
      
      .modal-content {
        margin: 10px;
        max-height: 95vh;
      }
      
      .modal-header, .modal-body, .modal-footer {
        padding: 20px;
      }
    }
  `]
})
export class ConductorEditModalComponent implements OnInit, OnChanges {
  @Input() isOpen: boolean = false;
  @Input() conductor: Conductor | null = null;
  @Output() close = new EventEmitter<void>();
  @Output() save = new EventEmitter<Conductor>();

  conductorData: Conductor = {
    conductor_id: 0,
    usuario_id: 0,
    estado: 'p',
    documentos_presentados: '',
    nombre: '',
    apellido: '',
    email: '',
    dni: '',
    total_examenes_asignados: 0,
    examenes_aprobados: 0,
    examenes_pendientes: 0,
    categorias_asignadas: [],
    categorias_aprobadas: [],
    categorias_pendientes: [],
    resumen_categorias: {}
  };

  examenesDisponibles: Examen[] = [];
  loading: boolean = false;
  loadingExams: boolean = false;
  error: string = '';

  ngOnInit() {
    if (this.conductor) {
      this.conductorData = { ...this.conductor };
    }
  }

  ngOnChanges() {
    if (this.conductor) {
      this.conductorData = { ...this.conductor };
      this.loadExamenesDisponibles();
    }
  }

  async loadExamenesDisponibles() {
    this.loadingExams = true;
    this.error = '';

    try {
      const response = await this.apiService.get('/examenes').toPromise();

      if (response && response.status === 'success') {
        this.examenesDisponibles = (response.data as Examen[]) || [];
      } else {
        this.error = 'No se pudieron cargar los exámenes disponibles';
      }
    } catch (error: any) {
      console.error('Error al cargar exámenes:', error);
      this.error = error.message || 'Error al cargar exámenes disponibles';
    } finally {
      this.loadingExams = false;
    }
  }

  selectExam(examen: Examen) {
    // Aquí se implementará la lógica para asignar el examen
    console.log('Examen seleccionado:', examen);
    // Por ahora solo mostramos un alert
    alert(`Examen "${examen.titulo}" seleccionado para asignar`);
  }

  getStatusClass(estado: string): string {
    return estado;
  }

  async saveConductor() {
    this.loading = true;
    
    try {
      // Aquí se implementará la lógica para guardar los cambios
      console.log('Guardando conductor:', this.conductorData);
      
      // Simular guardado
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      this.save.emit(this.conductorData);
      this.closeModal();
    } catch (error) {
      console.error('Error al guardar conductor:', error);
      alert('Error al guardar los cambios');
    } finally {
      this.loading = false;
    }
  }

  closeModal() {
    this.close.emit();
  }
}
