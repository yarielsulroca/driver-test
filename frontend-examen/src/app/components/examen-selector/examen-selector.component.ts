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
  estado: string;
}

export interface Conductor {
  conductor_id: number;
  nombre: string;
  apellido: string;
  dni: string;
}

@Component({
  selector: 'app-examen-selector',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="examen-selector-overlay" *ngIf="isOpen" (click)="closeModal()">
      <div class="examen-selector-modal" (click)="$event.stopPropagation()">
        <div class="modal-header">
          <h3>Seleccionar Examen para {{ conductor?.nombre }} {{ conductor?.apellido }}</h3>
          <button class="close-btn" (click)="closeModal()" title="Cerrar">
            <span>&times;</span>
          </button>
        </div>
        
        <div class="modal-content">
          <div *ngIf="loading" class="loading">
            <div class="spinner"></div>
            Cargando exámenes disponibles...
          </div>
          
          <div *ngIf="error" class="error">
            <strong>Error:</strong> {{ error }}
          </div>
          
          <div *ngIf="!loading && !error" class="examenes-list">
            <div *ngIf="examenesDisponibles.length === 0" class="no-examenes">
              No hay exámenes disponibles para este conductor.
            </div>
            
            <div *ngFor="let examen of examenesDisponibles" class="examen-card" (click)="seleccionarExamen(examen)">
              <div class="examen-header">
                <h4>{{ examen.titulo }}</h4>
                <span class="categoria-badge">{{ examen.categoria_codigo }}</span>
              </div>
              
              <div class="examen-info">
                <p class="descripcion">{{ examen.descripcion }}</p>
                
                <div class="examen-details">
                  <div class="detail-item">
                    <strong>Categoría:</strong> {{ examen.categoria_nombre }}
                  </div>
                  <div class="detail-item">
                    <strong>Dificultad:</strong> 
                    <span class="dificultad-badge" [class]="'dificultad-' + examen.dificultad.toLowerCase()">
                      {{ examen.dificultad }}
                    </span>
                  </div>
                  <div class="detail-item">
                    <strong>Puntaje mínimo:</strong> {{ examen.puntaje_minimo }}%
                  </div>
                  <div class="detail-item">
                    <strong>Duración:</strong> {{ examen.duracion_minutos }} minutos
                  </div>
                </div>
              </div>
              
              <div class="examen-actions">
                <button class="btn btn-primary" (click)="seleccionarExamen(examen)">
                  Asignar Examen
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .examen-selector-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.6);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      padding: 20px;
    }
    
    .examen-selector-modal {
      background: white;
      border-radius: 12px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      max-width: 800px;
      width: 100%;
      max-height: 80vh;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }
    
    .modal-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .modal-header h3 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 600;
    }
    
    .close-btn {
      background: none;
      border: none;
      color: white;
      font-size: 28px;
      cursor: pointer;
      padding: 0;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      transition: background-color 0.2s;
    }
    
    .close-btn:hover {
      background-color: rgba(255, 255, 255, 0.2);
    }
    
    .modal-content {
      padding: 24px;
      overflow-y: auto;
      flex: 1;
    }
    
    .loading {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
      color: #666;
    }
    
    .spinner {
      width: 24px;
      height: 24px;
      border: 3px solid #f3f3f3;
      border-top: 3px solid #667eea;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-right: 12px;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    .error {
      background-color: #f8d7da;
      color: #721c24;
      padding: 16px;
      border-radius: 8px;
      border: 1px solid #f5c6cb;
    }
    
    .no-examenes {
      text-align: center;
      padding: 40px;
      color: #666;
      font-style: italic;
    }
    
    .examenes-list {
      display: grid;
      gap: 16px;
    }
    
    .examen-card {
      border: 2px solid #e9ecef;
      border-radius: 12px;
      padding: 20px;
      transition: all 0.3s ease;
      cursor: pointer;
      background: #fafafa;
    }
    
    .examen-card:hover {
      border-color: #667eea;
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
      transform: translateY(-2px);
    }
    
    .examen-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 12px;
    }
    
    .examen-header h4 {
      margin: 0;
      color: #2c3e50;
      font-size: 1.25rem;
      font-weight: 600;
      flex: 1;
      margin-right: 12px;
    }
    
    .categoria-badge {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
      white-space: nowrap;
    }
    
    .examen-info {
      margin-bottom: 16px;
    }
    
    .descripcion {
      color: #666;
      margin: 0 0 16px 0;
      line-height: 1.5;
    }
    
    .examen-details {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 8px;
    }
    
    .detail-item {
      display: flex;
      align-items: center;
      font-size: 0.9rem;
      color: #555;
    }
    
    .detail-item strong {
      margin-right: 8px;
      color: #333;
      min-width: 100px;
    }
    
    .dificultad-badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
    }
    
    .dificultad-facil {
      background-color: #d4edda;
      color: #155724;
    }
    
    .dificultad-medio {
      background-color: #fff3cd;
      color: #856404;
    }
    
    .dificultad-dificil {
      background-color: #f8d7da;
      color: #721c24;
    }
    
    .examen-actions {
      display: flex;
      justify-content: flex-end;
    }
    
    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 0.9rem;
      font-weight: 600;
      transition: all 0.2s;
    }
    
    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }
    
    .btn-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    @media (max-width: 768px) {
      .examen-selector-modal {
        margin: 10px;
        max-height: 90vh;
      }
      
      .modal-header {
        padding: 16px 20px;
      }
      
      .modal-header h3 {
        font-size: 1.25rem;
      }
      
      .modal-content {
        padding: 16px;
      }
      
      .examen-details {
        grid-template-columns: 1fr;
      }
    }
  `]
})
export class ExamenSelectorComponent implements OnInit {
  @Input() isOpen = false;
  @Input() conductor: Conductor | null = null;
  @Output() close = new EventEmitter<void>();
  @Output() examenSeleccionado = new EventEmitter<Examen>();

  examenesDisponibles: Examen[] = [];
  loading = false;
  error = '';

  constructor(private apiService: ApiService) {}

  ngOnInit() {
    if (this.isOpen && this.conductor) {
      this.cargarExamenesDisponibles();
    }
  }

  async cargarExamenesDisponibles() {
    if (!this.conductor) return;

    this.loading = true;
    this.error = '';

    try {
      const response = await this.apiService.get('/examenes/disponibles', {
        conductor_id: this.conductor.conductor_id
      }).toPromise();

      if (response && response.status === 'success') {
        this.examenesDisponibles = (response.data as Examen[]) || [];
      } else {
        this.error = 'No se pudieron cargar los exámenes disponibles';
      }
    } catch (error: any) {
      console.error('Error al cargar exámenes:', error);
      this.error = error.message || 'Error al cargar exámenes disponibles';
    } finally {
      this.loading = false;
    }
  }

  seleccionarExamen(examen: Examen) {
    this.examenSeleccionado.emit(examen);
    this.closeModal();
  }

  closeModal() {
    this.close.emit();
  }
}
