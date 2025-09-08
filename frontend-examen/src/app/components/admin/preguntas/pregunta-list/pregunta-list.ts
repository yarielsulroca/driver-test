import { Component, OnInit, OnDestroy, inject, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { Subject, takeUntil, debounceTime, distinctUntilChanged, firstValueFrom } from 'rxjs';
import { ApiService } from '../../../../services/api.service';
import { NotificationService } from '../../../../services/notification.service';
import { LoadingService } from '../../../../services/loading.service';
import { Pregunta, Respuesta, FiltrosPreguntas, PreguntasResponse, CategoriasResponse, ApiResponse } from '../../../../shared/interfaces';

@Component({
  selector: 'app-pregunta-list',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <div class="pregunta-list-container">
      <!-- Header -->
      <div class="header">
        <h2>Lista de Preguntas</h2>
        <p>Administra todas las preguntas del sistema</p>
      </div>

      <!-- Filtros -->
      <div class="filters-section">
        <div class="filters-row">
          <div class="filter-group">
            <label for="filtroTexto">Buscar texto:</label>
            <input 
              type="text" 
              id="filtroTexto"
              [(ngModel)]="filtros.texto" 
              (ngModelChange)="onFiltroTextoChange($event)"
              placeholder="Buscar en enunciado..."
              class="form-control"
            >
          </div>
          
          <div class="filter-group">
            <label for="filtroCategoria">Categoría:</label>
            <select 
              id="filtroCategoria"
              [(ngModel)]="filtros.categoria_id" 
              (ngModelChange)="onFiltroCategoriaChange($event)"
              class="form-control"
            >
              <option value="">Todas las categorías</option>
              <option *ngFor="let cat of categorias" [value]="cat.categoria_id">
                {{ cat.nombre }}
              </option>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="filtroDificultad">Dificultad:</label>
            <select 
              id="filtroDificultad"
              [(ngModel)]="filtros.dificultad" 
              (ngModelChange)="onFiltroDificultadChange($event)"
              class="form-control"
            >
              <option value="">Todas las dificultades</option>
              <option value="facil">Fácil</option>
              <option value="medio">Medio</option>
              <option value="dificil">Difícil</option>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="filtroTipo">Tipo:</label>
            <select 
              id="filtroTipo"
              [(ngModel)]="filtros.tipo" 
              (ngModelChange)="onFiltroTipoChange($event)"
              class="form-control"
            >
              <option value="">Todos los tipos</option>
              <option value="multiple">Opción Múltiple</option>
              <option value="unica">Opción Única</option>
              <option value="verdadero_falso">Verdadero/Falso</option>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="filtroCritica">Crítica:</label>
            <select 
              id="filtroCritica"
              [(ngModel)]="filtros.es_critica" 
              (ngModelChange)="onFiltroCriticaChange($event)"
              class="form-control"
            >
              <option value="">Todas</option>
              <option value="true">Sí</option>
              <option value="false">No</option>
            </select>
          </div>
        </div>
        
        <div class="filters-actions">
          <button class="btn btn-secondary" (click)="limpiarFiltros()">
            Limpiar Filtros
          </button>
        </div>
      </div>

      <!-- Acciones -->
      <div class="actions-section">
        <div class="actions-left">
          <button class="btn btn-primary" (click)="crearPregunta()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nueva Pregunta
          </button>
        </div>
      </div>

      <!-- Tabla -->
      <div class="table-container">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Texto</th>
                <th>Tipo</th>
                <th>Categoría</th>
                <th>Dificultad</th>
                <th>Puntaje</th>
                <th>Crítica</th>
                <th>Respuestas</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
                             <ng-container *ngFor="let pregunta of preguntas">
                 <tr class="pregunta-row">
                   <td class="pregunta-id">{{ pregunta.pregunta_id }}</td>
                   <td class="pregunta-texto">
                     <div class="texto-container">
                       <div class="texto-preview">{{ pregunta.enunciado }}</div>
                       <div class="imagen-preview" *ngIf="pregunta.imagen_url">
                         <img [src]="pregunta.imagen_url" class="imagen-mini" alt="Imagen">
                       </div>
                     </div>
                   </td>
                   <td class="pregunta-tipo">
                     <span class="badge badge-primary">
                       {{ getTipoPreguntaLabel(pregunta.tipo_pregunta) }}
                     </span>
                   </td>
                   <td class="pregunta-categoria">{{ pregunta.categoria_nombre }}</td>
                   <td class="pregunta-dificultad">
                     <span class="badge" [ngClass]="getDificultadClass(pregunta.dificultad)">
                       {{ getDificultadLabel(pregunta.dificultad) }}
                     </span>
                   </td>
                   <td class="pregunta-puntaje">{{ pregunta.puntaje }}</td>
                   <td class="pregunta-critica">
                     <span class="badge" [ngClass]="pregunta.es_critica ? 'badge-danger' : 'badge-secondary'">
                       {{ pregunta.es_critica ? 'Sí' : 'No' }}
                     </span>
                   </td>
                   <td class="pregunta-respuestas">
                     <div class="respuestas-info">
                       <span class="respuestas-count">{{ (pregunta.respuestas && pregunta.respuestas.length) || 0 }} respuestas</span>
                       <div class="respuestas-preview">
                         <div class="respuesta-item">
                           <span class="respuesta-texto">
                             Correctas: {{ getRespuestasCorrectas(pregunta.respuestas) }}
                           </span>
                           <span *ngIf="tieneImagenes(pregunta.respuestas)" class="imagen-indicator" title="Esta pregunta tiene imágenes">
                             📷
                           </span>
                         </div>
                         <button class="btn-toggle-respuestas" (click)="toggleRespuestas(pregunta.pregunta_id)">
                           {{ preguntaExpandida === pregunta.pregunta_id ? 'Ocultar' : 'Ver' }} respuestas
                         </button>
                       </div>
                     </div>
                   </td>
                   <td class="pregunta-acciones">
                     <div class="acciones-buttons">
                       <button class="btn-icon btn-edit" (click)="editarPregunta(pregunta.pregunta_id)" title="Editar">
                         <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                         </svg>
                       </button>
                       <button class="btn-icon btn-delete" (click)="eliminarPregunta(pregunta.pregunta_id)" title="Eliminar">
                         <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                         </svg>
                       </button>
                     </div>
                   </td>
                 </tr>
                 
                 <!-- Fila expandible para mostrar respuestas -->
                 <tr *ngIf="preguntaExpandida === pregunta.pregunta_id" class="respuestas-expanded-row">
                   <td colspan="9">
                     <div class="respuestas-container">
                       <h4>Respuestas de la pregunta:</h4>
                       <div class="respuestas-list">
                         <div *ngFor="let respuesta of pregunta.respuestas; let i = index" class="respuesta-item-expanded">
                           <div class="respuesta-header">
                             <span class="respuesta-numero">{{ i + 1 }}</span>
                             <span class="respuesta-texto-expanded">{{ respuesta.texto }}</span>
                             <span class="respuesta-correcta" [ngClass]="respuesta.es_correcta ? 'correcta' : 'incorrecta'">
                               {{ respuesta.es_correcta ? '✓ Correcta' : '✗ Incorrecta' }}
                             </span>
                           </div>
                           <div *ngIf="respuesta.explicacion" class="respuesta-explicacion">
                             <strong>Explicación:</strong> {{ respuesta.explicacion }}
                           </div>
                           <div *ngIf="respuesta.imagen_url" class="respuesta-imagen">
                             <span class="imagen-label">📷 Imagen de la respuesta:</span>
                             <img 
                               [src]="respuesta.imagen_url" 
                               alt="Imagen de respuesta" 
                               class="imagen-respuesta"
                               (click)="verImagenCompleta(respuesta.imagen_url)"
                               title="Haz clic para ver en tamaño completo"
                             >
                           </div>
                         </div>
                       </div>
                     </div>
                   </td>
                 </tr>
               </ng-container>
             </tbody>
           </table>
         </div>
       </div>

      <!-- Paginación -->
      <div class="pagination-section" *ngIf="totalPages > 1">
        <div class="pagination-info">
          Mostrando {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, totalItems) }} de {{ totalItems }} preguntas
        </div>
        <div class="pagination-controls">
          <button 
            class="btn btn-secondary" 
            [disabled]="currentPage === 1"
            (click)="cambiarPagina(currentPage - 1)"
          >
            Anterior
          </button>
          
          <div class="page-numbers">
            <button 
              *ngFor="let page of getPaginas()" 
              class="btn" 
              [ngClass]="page === currentPage ? 'btn-primary' : 'btn-secondary'"
              (click)="cambiarPagina(page)"
            >
              {{ page }}
            </button>
          </div>
          
          <button 
            class="btn btn-secondary" 
            [disabled]="currentPage === totalPages"
            (click)="cambiarPagina(currentPage + 1)"
          >
            Siguiente
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div class="loading-overlay" *ngIf="loading">
        <div class="loading-spinner"></div>
        <p>Cargando preguntas...</p>
      </div>
    </div>
  `,
  styleUrls: ['./pregunta-list.scss']
})
export class PreguntaList implements OnInit, OnDestroy {
  private destroy$ = new Subject<void>();
  
  preguntas: Pregunta[] = [];
  categorias: any[] = [];
  loading = false;
  currentPage = 1;
  totalPages = 1;
  totalItems = 0;
  itemsPerPage = 10;
  
  // Filtros
  filtros: FiltrosPreguntas = {
    texto: '',
    categoria_id: '',
    dificultad: '',
    tipo: '',
    es_critica: ''
  };
  
  // Paginación
  paginacion = {
    currentPage: 1,
    totalPages: 1,
    totalItems: 0,
    itemsPerPage: 10
  };
  
  // Estado de expansión
  preguntaExpandida: number | null = null;
  
  // Modal de imagen
  showImageModal = false;
  selectedImageUrl = '';
  
  // Subject para filtros
  private filtrosSubject = new Subject<FiltrosPreguntas>();
  
  // Math para template
  Math = Math;

  constructor(
    private apiService: ApiService,
    private notificationService: NotificationService,
    private loadingService: LoadingService,
    private router: Router,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    this.cargarCategorias();
    this.cargarPreguntas();
    
    // Configurar filtros con debounce
    this.filtrosSubject.pipe(
      takeUntil(this.destroy$),
      debounceTime(300),
      distinctUntilChanged()
    ).subscribe(() => {
      this.currentPage = 1;
      this.cargarPreguntas();
    });
  }

  ngOnDestroy() {
    this.destroy$.next();
    this.destroy$.complete();
  }

  cargarCategorias() {
    console.log('🔍 Iniciando carga de categorías...');
    this.apiService.get<CategoriasResponse>('/categorias').subscribe({
      next: (response: ApiResponse<CategoriasResponse>) => {
        console.log('✅ Respuesta de categorías recibida:', response);
        if (response.status === 'success' && response.data) {
          this.categorias = response.data.categorias;
          console.log(`📊 Categorías cargadas: ${this.categorias.length}`);
        } else {
          console.warn('⚠️ Respuesta de categorías sin éxito o sin datos:', response);
        }
      },
      error: (error: any) => {
        console.error('❌ Error cargando categorías:', error);
        console.error('🔍 Detalles del error de categorías:', {
          status: error.status,
          statusText: error.statusText,
          message: error.message,
          url: error.url
        });
        this.notificationService.error('Error cargando categorías');
      }
    });
  }

  cargarPreguntas() {
    this.loading = true;
    console.log('🔍 Iniciando carga de preguntas...');
    
    const params = {
      page: this.currentPage,
      limit: this.itemsPerPage,
      ...this.filtros
    };
    
    console.log('📋 Parámetros de búsqueda:', params);
    console.log('🌐 URL de la API:', '/preguntas');
    
    // Intentar primero con la ruta completa
    this.apiService.get<PreguntasResponse>('/preguntas', params).subscribe({
      next: (response: ApiResponse<PreguntasResponse>) => {
        console.log('✅ Respuesta recibida:', response);
        if (response.status === 'success' && response.data) {
          this.preguntas = response.data.preguntas;
          this.totalItems = response.data.pagination.total;
          this.totalPages = response.data.pagination.last_page;
          
          console.log(`📊 Preguntas cargadas: ${this.preguntas.length}`);
          console.log(`📄 Total de items: ${this.totalItems}`);
          console.log(`📑 Total de páginas: ${this.totalPages}`);
          
          // Cargar respuestas para cada pregunta
          this.preguntas.forEach(pregunta => {
            this.cargarRespuestas(pregunta.pregunta_id);
          });
        } else {
          console.warn('⚠️ Respuesta sin éxito o sin datos:', response);
        }
        this.loading = false;
        this.cdr.detectChanges();
      },
      error: (error: any) => {
        console.error('❌ Error cargando preguntas:', error);
        console.error('🔍 Detalles del error:', {
          status: error.status,
          statusText: error.statusText,
          message: error.message,
          url: error.url
        });
        this.notificationService.error('Error cargando preguntas');
        this.loading = false;
        this.cdr.detectChanges();
      }
    });
  }

  cargarRespuestas(preguntaId: number) {
    this.apiService.get<any>(`/preguntas/${preguntaId}/respuestas`).subscribe({
      next: (response: any) => {
        if (response?.status === 'success' && response?.data) {
          const pregunta = this.preguntas.find(p => p.pregunta_id === preguntaId);
          if (pregunta) {
            pregunta.respuestas = response.data;
          }
        }
      },
      error: (error: any) => {
        console.error('Error cargando respuestas:', error);
      }
    });
  }

  cambiarPagina(page: number) {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.cargarPreguntas();
    }
  }

  getPaginas(): number[] {
    const paginas: number[] = [];
    const inicio = Math.max(1, this.currentPage - 2);
    const fin = Math.min(this.totalPages, this.currentPage + 2);
    
    for (let i = inicio; i <= fin; i++) {
      paginas.push(i);
    }
    
    return paginas;
  }

  // Métodos de filtros
  onFiltroTextoChange(value: string) {
    this.filtros.texto = value;
    this.filtrosSubject.next(this.filtros);
  }

  onFiltroCategoriaChange(value: string) {
    this.filtros.categoria_id = value;
    this.filtrosSubject.next(this.filtros);
  }

  onFiltroDificultadChange(value: string) {
    this.filtros.dificultad = value;
    this.filtrosSubject.next(this.filtros);
  }

  onFiltroTipoChange(value: string) {
    this.filtros.tipo = value;
    this.filtrosSubject.next(this.filtros);
  }

  onFiltroCriticaChange(value: string) {
    this.filtros.es_critica = value;
    this.filtrosSubject.next(this.filtros);
  }

  limpiarFiltros() {
    this.filtros = {
      texto: '',
      categoria_id: '',
      dificultad: '',
      tipo: '',
      es_critica: ''
    };
    this.currentPage = 1;
    this.cargarPreguntas();
  }

  // Métodos de utilidad
  getTipoPreguntaLabel(tipo: string): string {
    const tipos: { [key: string]: string } = {
      'multiple': 'Opción Múltiple',
      'unica': 'Opción Única',
      'verdadero_falso': 'Verdadero/Falso'
    };
    return tipos[tipo] || tipo;
  }

  getDificultadLabel(dificultad: string): string {
    const dificultades: { [key: string]: string } = {
      'facil': 'Fácil',
      'medio': 'Medio',
      'dificil': 'Difícil'
    };
    return dificultades[dificultad] || dificultad;
  }

  getDificultadClass(dificultad: string): string {
    const classes: { [key: string]: string } = {
      'facil': 'badge-success',
      'medio': 'badge-warning',
      'dificil': 'badge-danger'
    };
    return classes[dificultad] || 'badge-secondary';
  }

     getRespuestasCorrectas(respuestas: Respuesta[]): number {
     return respuestas?.filter(r => r.es_correcta).length || 0;
   }

   tieneImagenes(respuestas: Respuesta[]): boolean {
     return respuestas?.some(r => r.imagen_url) || false;
   }

     toggleRespuestas(preguntaId: number) {
     if (this.preguntaExpandida === preguntaId) {
       this.preguntaExpandida = null;
     } else {
       this.preguntaExpandida = preguntaId;
     }
   }

   verImagenCompleta(imageUrl: string) {
     // Abrir la imagen en una nueva pestaña para verla en tamaño completo
     window.open(imageUrl, '_blank');
   }

  // Métodos de acciones
  crearPregunta() {
    this.router.navigate(['/admin/preguntas/crear']);
  }

  editarPregunta(preguntaId: number) {
    this.router.navigate(['/admin/preguntas/editar', preguntaId]);
  }

  eliminarPregunta(preguntaId: number) {
    if (confirm('¿Estás seguro de que quieres eliminar esta pregunta?')) {
      this.apiService.delete<any>(`/preguntas/${preguntaId}`).subscribe({
        next: (response: any) => {
          if (response.status === 'success') {
            this.notificationService.success('Pregunta eliminada correctamente');
            this.cargarPreguntas();
          } else {
            this.notificationService.error('Error eliminando pregunta');
          }
        },
        error: (error: any) => {
          console.error('Error eliminando pregunta:', error);
          this.notificationService.error('Error eliminando pregunta');
        }
      });
    }
  }
}
