import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../services/api.service';
import { ExamenSelectorComponent, Examen } from '../examen-selector/examen-selector.component';
import { ConductorEditModalComponent } from '../conductor-edit-modal/conductor-edit-modal.component';

interface Conductor {
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

@Component({
  selector: 'app-conductores',
  standalone: true,
  imports: [CommonModule, ExamenSelectorComponent, ConductorEditModalComponent],
  template: `
    <div class="container">
      <h2>Gestión de Conductores</h2>
      
      <!-- DEBUG INFO -->
      <div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border: 1px solid #ccc;">
        <h3>DEBUG INFO:</h3>
        <p>Loading: {{ loading }}</p>
        <p>Error: "{{ error }}"</p>
        <p>Conductores length: {{ conductores.length }}</p>
        <p>¿Mostrar tabla?: {{ !loading && !error }}</p>
        <p>¿Conductores es array?: {{ esArray(conductores) }}</p>
      </div>
      
      <div *ngIf="loading" class="loading">
        <div class="spinner"></div>
        Cargando conductores...
      </div>
      
      <div *ngIf="error" class="error">
        <strong>Error:</strong> {{ error }}
      </div>
      
      <div *ngIf="!loading && !error" class="content">
        <div class="summary">
          <p><strong>Total de conductores:</strong> {{ conductores.length }}</p>
        </div>
        
        <div class="table-container">
          <table class="conductores-table">
            <thead>
              <tr>
                <th>Nombre del Conductor</th>
                <th>DNI</th>
                <th>Estado</th>
                <th>Categorías Aprobadas</th>
                <th>Categorías Pendientes</th>
                <th>Intentos por Categoría</th>
                <th>Valor de Exámenes Aprobados</th>
                <th>Fechas</th>
                <th>Habilitar Examen</th>
              </tr>
            </thead>
            <tbody>
              <tr *ngFor="let conductor of conductores; trackBy: trackByConductorId">
                <!-- Nombre del Conductor -->
                <td>
                  <div class="conductor-name">
                    <strong>{{ conductor.nombre }} {{ conductor.apellido }}</strong>
                  </div>
                </td>
                
                <!-- DNI -->
                <td>{{ conductor.dni }}</td>
                
                <!-- Estado -->
                <td>
                  <span [class]="conductor.estado === 'b' ? 'estado-bueno' : 'estado-pendiente'">
                    {{ conductor.estado === 'b' ? 'Aprobado' : 'Pendiente' }}
                  </span>
                </td>
                
                <!-- Categorías Aprobadas -->
                <td>
                  <div class="categories-approved">
                    <div *ngIf="(conductor.categorias_aprobadas || []).length > 0; else noApprovedCategories">
                      <div *ngFor="let categoria of conductor.categorias_aprobadas" class="category-item approved">
                        <div class="category-name">{{ categoria.categoria_codigo || categoria.categoria || 'N/A' }}</div>
                        <div class="category-score">{{ categoria.puntaje_obtenido || 0 }}%</div>
                      </div>
                    </div>
                    <ng-template #noApprovedCategories>
                      <div class="no-categories">Sin categorías aprobadas</div>
                    </ng-template>
                  </div>
                </td>
                
                <!-- Categorías Pendientes -->
                <td>
                  <div class="categories-pending">
                    <div *ngIf="(conductor.categorias_pendientes || []).length > 0; else noPendingCategories">
                      <div *ngFor="let categoria of conductor.categorias_pendientes" class="category-item pending">
                        <div class="category-name">{{ categoria.categoria_codigo || categoria.categoria_nombre || 'N/A' }}</div>
                        <div class="category-state" [class]="'state-' + categoria.estado.toLowerCase()">
                          {{ categoria.estado }}
                        </div>
                      </div>
                    </div>
                    <ng-template #noPendingCategories>
                      <div class="no-categories">Sin categorías pendientes</div>
                    </ng-template>
                  </div>
                </td>
                
                <!-- Intentos por Categoría -->
                <td>
                  <div class="attempts-info">
                    <div *ngIf="(conductor.categorias_pendientes || []).length > 0; else noAttempts">
                      <div *ngFor="let categoria of conductor.categorias_pendientes" class="attempt-item">
                        <strong>{{ categoria.categoria_codigo || categoria.categoria_nombre || 'N/A' }}:</strong>
                        {{ categoria.intentos_realizados || 0 }}/{{ categoria.intentos_maximos || 0 }} intentos
                      </div>
                    </div>
                    <ng-template #noAttempts>
                      <div class="no-attempts">Sin intentos</div>
                    </ng-template>
                  </div>
                </td>
                
                <!-- Valor de Exámenes Aprobados -->
                <td>
                  <div class="exam-values">
                    <div *ngIf="(conductor.categorias_aprobadas || []).length > 0; else noValues">
                      <div *ngFor="let categoria of conductor.categorias_aprobadas" class="value-item">
                        <strong>{{ categoria.categoria_codigo || categoria.categoria || 'N/A' }}:</strong>
                        {{ categoria.puntaje_obtenido || 0 }}%
                      </div>
                    </div>
                    <ng-template #noValues>
                      <div class="no-values">Sin exámenes aprobados</div>
                    </ng-template>
                  </div>
                </td>
                
                <!-- Fechas -->
                <td>
                  <div class="dates-info">
                    <div *ngIf="(conductor.categorias_pendientes || []).length > 0; else noDates">
                      <div *ngFor="let categoria of conductor.categorias_pendientes" class="date-item">
                        <div *ngIf="categoria.fecha_ultimo_intento">
                          <strong>Último:</strong> {{ categoria.fecha_ultimo_intento | date:'dd/MM/yyyy' }}
                        </div>
                        <div *ngIf="categoria.fecha_asignacion">
                          <strong>Asignado:</strong> {{ categoria.fecha_asignacion | date:'dd/MM/yyyy' }}
                        </div>
                      </div>
                    </div>
                    <ng-template #noDates>
                      <div class="no-dates">Sin fechas</div>
                    </ng-template>
                  </div>
                </td>
                
                <!-- Editar Conductor -->
                <td>
                  <button class="btn btn-primary btn-sm" 
                          (click)="editarConductor(conductor)">
                    Editar Conductor
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Selector de Exámenes -->
      <app-examen-selector 
        [isOpen]="showExamenSelector"
        [conductor]="selectedConductor"
        (close)="closeExamenSelector()"
        (examenSeleccionado)="onExamenSeleccionado($event)">
      </app-examen-selector>
      
      <!-- Modal de Edición de Conductor -->
      <app-conductor-edit-modal
        [isOpen]="showConductorEditModal"
        [conductor]="selectedConductor"
        (close)="closeConductorEditModal()"
        (save)="onConductorSaved($event)">
      </app-conductor-edit-modal>
    </div>
  `,
  styles: [`
    .container {
      padding: 24px;
      max-width: 1400px;
      margin: 0 auto;
      background: #f8fafc;
      min-height: 100vh;
    }
    
    h2 {
      color: #1e293b;
      margin-bottom: 32px;
      font-size: 2rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    h2::before {
      content: '👥';
      font-size: 1.5rem;
    }
    
    .loading {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
      font-size: 18px;
      color: #666;
    }
    
    .spinner {
      width: 20px;
      height: 20px;
      border: 2px solid #f3f3f3;
      border-top: 2px solid #3498db;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-right: 10px;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    .error {
      background-color: #f8d7da;
      color: #721c24;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      border: 1px solid #f5c6cb;
    }
    
    .content {
      margin-top: 20px;
    }
    
    .summary {
      background-color: #e9ecef;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    
    .table-container {
      overflow-x: auto;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      background: white;
      border: 1px solid #e2e8f0;
    }
    
    .conductores-table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      border-radius: 16px;
      overflow: hidden;
    }
    
    .conductores-table th,
    .conductores-table td {
      padding: 16px 20px;
      text-align: left;
      border-bottom: 1px solid #f1f5f9;
    }
    
    .conductores-table th {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      font-weight: 600;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      position: sticky;
      top: 0;
      z-index: 10;
    }
    
    .conductores-table tr:hover {
      background-color: #f8fafc;
      transform: scale(1.01);
      transition: all 0.2s ease;
    }
    
    .conductor-name {
      font-weight: 500;
    }
    
    .estado-bueno {
      color: #065f46;
      font-weight: 600;
      padding: 6px 12px;
      background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
      border-radius: 20px;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border: 1px solid #10b981;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }
    
    .estado-bueno::before {
      content: '✅';
      font-size: 0.7rem;
    }
    
    .estado-pendiente {
      color: #92400e;
      font-weight: 600;
      padding: 6px 12px;
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      border-radius: 20px;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border: 1px solid #f59e0b;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }
    
    .estado-pendiente::before {
      content: '⏳';
      font-size: 0.7rem;
    }
    
    .exam-stats,
    .category-stats {
      font-size: 0.9em;
      line-height: 1.4;
    }
    
    .exam-stats div,
    .category-stats div {
      margin-bottom: 2px;
    }
    
    .btn {
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }
    
    .btn-primary {
      background-color: #007bff;
      color: white;
    }
    
    .btn-primary:hover {
      background-color: #0056b3;
    }
    
    .btn-success {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: white;
      border: none;
      border-radius: 8px;
      padding: 8px 16px;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }
    
    .btn-success:hover:not(:disabled) {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .btn-success:disabled {
      background: #94a3b8;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }
    
    .btn-sm {
      padding: 6px 12px;
      font-size: 0.8rem;
    }
    
    .action-buttons {
      display: flex;
      gap: 5px;
      flex-wrap: wrap;
    }
    
    /* Estilos para categorías aprobadas */
    .categories-approved,
    .categories-pending {
      max-width: 200px;
    }
    
    .category-item {
      margin-bottom: 8px;
      padding: 8px;
      border-radius: 6px;
      border-left: 3px solid;
    }
    
    .category-item.approved {
      background-color: #d4edda;
      border-left-color: #28a745;
    }
    
    .category-item.pending {
      background-color: #f8f9fa;
      border-left-color: #6c757d;
    }
    
    .category-name {
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 4px;
    }
    
    .category-score {
      font-weight: 700;
      color: #28a745;
      font-size: 1rem;
    }
    
    .category-state {
      font-size: 0.8rem;
      padding: 2px 6px;
      border-radius: 4px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 4px 0;
    }
    
    .state-reprobada {
      background-color: #f8d7da;
      color: #721c24;
    }
    
    .state-iniciado {
      background-color: #d1ecf1;
      color: #0c5460;
    }
    
    .state-aprobada {
      background-color: #d4edda;
      color: #155724;
    }
    
    .category-attempts,
    .category-date {
      font-size: 0.75rem;
      color: #6c757d;
      margin-top: 2px;
    }
    
    .no-categories,
    .no-attempts,
    .no-values,
    .no-dates {
      color: #6c757d;
      font-style: italic;
      text-align: center;
      padding: 10px;
    }
    
    /* Estilos para intentos */
    .attempts-info {
      max-width: 150px;
    }
    
    .attempt-item {
      margin-bottom: 4px;
      font-size: 0.85rem;
      line-height: 1.3;
    }
    
    /* Estilos para valores de exámenes */
    .exam-values {
      max-width: 150px;
    }
    
    .value-item {
      margin-bottom: 4px;
      font-size: 0.85rem;
      line-height: 1.3;
    }
    
    /* Estilos para fechas */
    .dates-info {
      max-width: 150px;
    }
    
    .date-item {
      margin-bottom: 4px;
      font-size: 0.85rem;
      line-height: 1.3;
    }

    /* Estilos para motivo deshabilitado */
    .motivo-deshabilitado {
      font-size: 0.75rem;
      color: #dc2626;
      font-style: italic;
      margin-top: 4px;
      text-align: center;
      background: #fef2f2;
      padding: 4px 8px;
      border-radius: 4px;
      border: 1px solid #fecaca;
    }
  `]
})
export class Conductores implements OnInit {
  conductores: Conductor[] = [];
  loading = false;
  error = '';
  showExamenSelector = false;
  showConductorEditModal = false;
  selectedConductor: Conductor | null = null;

  constructor(private apiService: ApiService, private cdr: ChangeDetectorRef) {}

  ngOnInit() {
    this.cargarConductores();
  }

  async cargarConductores() {
    this.loading = true;
    this.error = '';
    
    try {
      console.log('Iniciando carga de conductores...');
      const response = await this.apiService.get('/conductores').toPromise();
      console.log('Respuesta del servidor:', response);
      
      if (response && response.status === 'success' && response.data && Array.isArray(response.data)) {
        this.conductores = response.data.map((conductor: any) => ({
          conductor_id: conductor.conductor_id,
          usuario_id: conductor.usuario_id,
          estado: conductor.estado,
          documentos_presentados: conductor.documentos_presentados,
          created_at: conductor.created_at,
          updated_at: conductor.updated_at,
          nombre: conductor.nombre,
          apellido: conductor.apellido,
          email: conductor.email,
          dni: conductor.dni,
          total_examenes_asignados: conductor.total_examenes_asignados || 0,
          examenes_aprobados: conductor.examenes_aprobados || 0,
          examenes_pendientes: conductor.examenes_pendientes || 0,
          categorias_asignadas: conductor.categorias_asignadas || [],
          categorias_aprobadas: conductor.categorias_aprobadas || [],
          categorias_pendientes: conductor.categorias_pendientes || [],
          resumen_categorias: conductor.resumen_categorias || {}
        }));
        this.loading = false;
        this.cdr.detectChanges(); // Forzar detección de cambios
        console.log('Conductores cargados:', this.conductores.length);
        console.log('Primer conductor:', this.conductores[0]);
        console.log('Loading después de cargar:', this.loading);
        console.log('Error después de cargar:', this.error);
        console.log('¿Debería mostrar tabla?', !this.loading && !this.error);
        console.log('¿Conductores array?', Array.isArray(this.conductores));
        console.log('¿Conductores length?', this.conductores.length);
        console.log('Datos completos del primer conductor:', this.conductores[0]);
        console.log('Categorías aprobadas del primer conductor:', this.conductores[0]?.categorias_aprobadas);
        console.log('Categorías asignadas del primer conductor:', this.conductores[0]?.categorias_asignadas);
      } else {
        this.loading = false;
        console.log('No se encontraron conductores o respuesta inválida');
        this.conductores = [];
      }
    } catch (error: any) {
      console.error('Error al cargar conductores:', error);
      this.error = error.message || 'Error al cargar conductores';
    } finally {
      this.loading = false;
    }
  }

  trackByConductorId(index: number, conductor: Conductor): number {
    return conductor.conductor_id;
  }

  verDetalles(conductor: Conductor) {
    console.log('Detalles del conductor:', conductor);
    alert(`Conductor: ${conductor.nombre} ${conductor.apellido}\nEmail: ${conductor.email}\nDNI: ${conductor.dni}`);
  }

  esArray(value: any): boolean {
    return Array.isArray(value);
  }

  puedeHabilitarExamen(conductor: Conductor): boolean {
    console.log('🔍 Verificando si puede habilitar examen para conductor:', conductor.conductor_id);
    
    // 1. Verificar si es un conductor nuevo (sin exámenes asignados)
    const esNuevo = (conductor.total_examenes_asignados || 0) === 0;
    console.log('📝 Es conductor nuevo:', esNuevo);
    
    // 2. Verificar si tiene exámenes iniciados (estado "Iniciado")
    const tieneExamenIniciado = (conductor.categorias_pendientes || []).some(
      cat => cat.estado === 'Iniciado'
    );
    console.log('⏳ Tiene examen iniciado:', tieneExamenIniciado);
    
    // 3. Verificar si tiene categorías reprobadas con intentos restantes
    const tieneCategoriasReprobadasConIntentos = (conductor.categorias_pendientes || []).some(
      cat => cat.estado === 'Reprobado' && (cat.intentos_maximos || 0) > (cat.intentos_realizados || 0)
    );
    console.log('🎯 Tiene categorías reprobadas con intentos:', tieneCategoriasReprobadasConIntentos);
    
    // LÓGICA DEL NEGOCIO CORREGIDA:
    // El botón "Habilitar Examen" SOLO debe estar activo si:
    // 1. Es conductor nuevo (sin exámenes asignados), O
    // 2. NO tiene ningún intento activo (ni iniciado, ni reprobado con intentos)
    
    let puedeHabilitar = false;
    
    if (esNuevo) {
      // Conductor nuevo: puede habilitar examen
      puedeHabilitar = true;
      console.log('✅ Conductor nuevo - puede habilitar');
    } else if (tieneExamenIniciado || tieneCategoriasReprobadasConIntentos) {
      // Tiene intentos activos (iniciado o reprobado con intentos): NO puede habilitar
      puedeHabilitar = false;
      console.log('❌ Tiene intentos activos - NO puede habilitar');
    } else {
      // No tiene intentos activos: puede habilitar
      puedeHabilitar = true;
      console.log('✅ Sin intentos activos - puede habilitar');
    }
    
    console.log('✅ Puede habilitar examen:', puedeHabilitar);
    return puedeHabilitar;
  }

  getTooltipHabilitarExamen(conductor: Conductor): string {
    if (this.puedeHabilitarExamen(conductor)) {
      return 'Habilitar Examen';
    }
    
    const tieneExamenIniciado = (conductor.categorias_pendientes || []).some(
      cat => cat.estado === 'Iniciado'
    );
    
    const tieneCategoriasReprobadasConIntentos = (conductor.categorias_pendientes || []).some(
      cat => cat.estado === 'Reprobado' && (cat.intentos_maximos || 0) > (cat.intentos_realizados || 0)
    );
    
    if (tieneExamenIniciado) {
      return 'No se puede habilitar: Tiene un examen iniciado. Debe completar todos los intentos de la categoría actual.';
    }
    
    if (tieneCategoriasReprobadasConIntentos) {
      return 'No se puede habilitar: Tiene intentos restantes en categorías reprobadas. Debe agotar todos los intentos antes de asignar un nuevo examen.';
    }
    
    return 'No se puede habilitar examen en este momento';
  }

  getMotivoDeshabilitado(conductor: Conductor): string {
    const tieneExamenIniciado = (conductor.categorias_pendientes || []).some(
      cat => cat.estado === 'Iniciado'
    );
    
    const tieneCategoriasReprobadasConIntentos = (conductor.categorias_pendientes || []).some(
      cat => cat.estado === 'Reprobado' && (cat.intentos_maximos || 0) > (cat.intentos_realizados || 0)
    );
    
    if (tieneExamenIniciado) {
      const categoriaIniciada = (conductor.categorias_pendientes || []).find(
        cat => cat.estado === 'Iniciado'
      );
      return `Debe completar ${categoriaIniciada?.categoria_codigo || 'categoría actual'} primero`;
    }
    
    if (tieneCategoriasReprobadasConIntentos) {
      const categoriasConIntentos = (conductor.categorias_pendientes || []).filter(
        cat => cat.estado === 'Reprobado' && (cat.intentos_maximos || 0) > (cat.intentos_realizados || 0)
      );
      const categoriasTexto = categoriasConIntentos.map(cat => 
        `${cat.categoria_codigo} (${cat.intentos_realizados}/${cat.intentos_maximos})`
      ).join(', ');
      return `Debe agotar intentos: ${categoriasTexto}`;
    }
    
    return 'No disponible';
  }

  habilitarExamen(conductor: Conductor) {
    console.log('Habilitar examen para conductor:', conductor);
    this.selectedConductor = conductor;
    this.showExamenSelector = true;
  }

  closeExamenSelector() {
    this.showExamenSelector = false;
    this.selectedConductor = null;
  }

  async onExamenSeleccionado(examen: Examen) {
    if (!this.selectedConductor) return;

    try {
      console.log('Asignando examen:', examen, 'para conductor:', this.selectedConductor);
      
      // Determinar qué categoría asignar basada en la lógica del negocio
      let categoriaId = examen.categoria_id;
      
      // Si el conductor tiene categorías reprobadas con intentos, usar la primera
      const categoriasReprobadasConIntentos = (this.selectedConductor.categorias_pendientes || []).filter(
        cat => cat.estado === 'Reprobado' && (cat.intentos_maximos || 0) > (cat.intentos_realizados || 0)
      );
      
      if (categoriasReprobadasConIntentos.length > 0) {
        // Usar la primera categoría reprobada con intentos
        categoriaId = categoriasReprobadasConIntentos[0].categoria_id;
        console.log('Usando categoría reprobada con intentos:', categoriaId);
      } else {
        // Si es conductor nuevo o no tiene categorías pendientes, usar la categoría del examen
        console.log('Usando categoría del examen:', categoriaId);
      }
      
      // Mostrar confirmación antes de asignar
      const confirmacion = confirm(
        `¿Asignar examen "${examen.titulo}" (${examen.categoria_codigo}) a ${this.selectedConductor.nombre} ${this.selectedConductor.apellido}?\n\n` +
        `Categoría: ${examen.categoria_nombre}\n` +
        `Duración: ${examen.duracion_minutos} minutos\n` +
        `Preguntas: ${examen.numero_preguntas || 'N/A'}`
      );
      
      if (!confirmacion) {
        console.log('Asignación cancelada por el usuario');
        return;
      }
      
      // Llamada al backend para asignar el examen
      const response = await this.apiService.post('/examenes/asignar', {
        conductor_id: this.selectedConductor.conductor_id,
        examen_id: examen.examen_id,
        categoria_id: categoriaId
      }).toPromise();

      if (response && response.status === 'success') {
        console.log('✅ Examen asignado exitosamente:', response);
        
        // Recargar los conductores para mostrar los cambios
        await this.cargarConductores();
        
        // Mostrar mensaje de éxito
        alert(`✅ Examen "${examen.titulo}" asignado exitosamente a ${this.selectedConductor.nombre} ${this.selectedConductor.apellido}\n\n` +
              `Categoría: ${examen.categoria_nombre}\n` +
              `Estado: Iniciado`);
      } else {
        console.error('❌ Error en respuesta del servidor:', response);
        alert(`❌ Error al asignar el examen: ${response?.message || 'Error desconocido'}`);
      }
    } catch (error: any) {
      console.error('Error al asignar examen:', error);
      alert(`❌ Error al asignar el examen: ${error.message || 'Error de conexión'}`);
    } finally {
      this.closeExamenSelector();
    }
  }

  editarConductor(conductor: Conductor) {
    console.log('Editando conductor:', conductor);
    this.selectedConductor = conductor;
    this.showConductorEditModal = true;
  }

  closeConductorEditModal() {
    this.showConductorEditModal = false;
    this.selectedConductor = null;
  }

  async onConductorSaved(conductor: Conductor) {
    console.log('Conductor guardado:', conductor);
    
    try {
      // Aquí se implementará la lógica para guardar los cambios del conductor
      // Por ahora solo recargamos la lista
      await this.cargarConductores();
      
      // Mostrar mensaje de éxito
      alert('✅ Conductor actualizado exitosamente');
    } catch (error) {
      console.error('Error al guardar conductor:', error);
      alert('❌ Error al guardar los cambios del conductor');
    } finally {
      this.closeConductorEditModal();
    }
  }
}
