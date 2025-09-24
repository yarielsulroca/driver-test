import { Component, OnInit, OnDestroy, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { MatSnackBar } from '@angular/material/snack-bar';
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
  imports: [CommonModule, FormsModule, RouterModule, ExamenSelectorComponent, ConductorEditModalComponent],
  templateUrl: './conductores.html',
  styleUrls: ['./conductores.scss']
})
export class Conductores implements OnInit, OnDestroy {
  conductores: Conductor[] = [];
  conductoresFiltrados: Conductor[] = [];
  loading = false;
  error = '';
  showExamenSelector = false;
  showConductorEditModal = false;
  selectedConductor: Conductor | null = null;
  
  // Filtros y búsqueda
  searchTerm = '';
  estadoFilter = '';
  mostrarContadorResultados = false;
  datosListos = false;

  constructor(
    private apiService: ApiService, 
    private cdr: ChangeDetectorRef, 
    private router: Router,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit() {
    this.cargarConductores();
  }

  ngOnDestroy() {
    // Limpiar recursos si es necesario
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
        this.cdr.detectChanges();
        
        console.log('Conductores cargados:', this.conductores.length);
        console.log('Primer conductor:', this.conductores[0]);
        
        // Inicializar conductores filtrados de manera segura
        setTimeout(() => {
          this.conductoresFiltrados = [...this.conductores];
          this.mostrarContadorResultados = false;
          this.datosListos = true;
          this.cdr.detectChanges();
        }, 0);
        
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

  getValorExamen(categoria: string): number {
    // Valores de exámenes por categoría
    const valoresExamen: { [key: string]: number } = {
      'A1': 2500,
      'A2': 2800,
      'B1': 3200,
      'B2': 3500,
      'C1': 4000,
      'C2': 4200,
      'D1': 4500,
      'D2': 4800
    };
    
    return valoresExamen[categoria] || 3000; // Valor por defecto
  }

  getValorExamenReprobado(conductor: Conductor): any {
    // Buscar el examen reprobado más reciente en categorías asignadas
    const categoriasReprobadas = (conductor.categorias_asignadas || []).filter(
      cat => cat.estado === 'Reprobado'
    );
    
    if (categoriasReprobadas.length === 0) {
      return null;
    }
    
    // Ordenar por fecha de último intento (más reciente primero)
    categoriasReprobadas.sort((a, b) => {
      const fechaA = new Date(a.fecha_ultimo_intento || a.updated_at || a.created_at || '');
      const fechaB = new Date(b.fecha_ultimo_intento || b.updated_at || b.created_at || '');
      return fechaB.getTime() - fechaA.getTime();
    });
    
    const masReciente = categoriasReprobadas[0];
    const categoria = masReciente.categoria_codigo || masReciente.categoria_nombre || 'N/A';
    const valor = this.getValorExamen(categoria);
    
    return {
      categoria: categoria,
      valor: valor,
      puntaje: masReciente.puntaje_obtenido || 0,
      fecha: masReciente.fecha_ultimo_intento || masReciente.updated_at || masReciente.created_at
    };
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
        this.mostrarNotificacion(
          `✅ Examen "${examen.titulo}" asignado exitosamente a ${this.selectedConductor.nombre} ${this.selectedConductor.apellido}`,
          'success'
        );
      } else {
        console.error('❌ Error en respuesta del servidor:', response);
        this.mostrarNotificacion(
          `❌ Error al asignar el examen: ${response?.message || 'Error desconocido'}`,
          'error'
        );
      }
    } catch (error: any) {
      console.error('Error al asignar examen:', error);
      this.mostrarNotificacion(
        `❌ Error al asignar el examen: ${error.message || 'Error de conexión'}`,
        'error'
      );
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
      this.mostrarNotificacion('✅ Conductor actualizado exitosamente', 'success');
    } catch (error) {
      console.error('Error al guardar conductor:', error);
      this.mostrarNotificacion('❌ Error al guardar los cambios del conductor', 'error');
    } finally {
      this.closeConductorEditModal();
    }
  }

  irAInicio() {
    this.router.navigate(['/']);
  }

  // Métodos para filtros y búsqueda
  onSearchChange() {
    this.aplicarFiltros();
  }

  onFilterChange() {
    this.aplicarFiltros();
  }

  aplicarFiltros() {
    if (!this.datosListos) return;
    
    let filtrados = [...this.conductores];

    // Filtro por búsqueda (nombre o DNI)
    if (this.searchTerm.trim()) {
      const termino = this.searchTerm.toLowerCase().trim();
      filtrados = filtrados.filter(conductor => 
        conductor.nombre.toLowerCase().includes(termino) ||
        conductor.apellido.toLowerCase().includes(termino) ||
        conductor.dni.toLowerCase().includes(termino)
      );
    }

    // Filtro por estado
    if (this.estadoFilter) {
      filtrados = filtrados.filter(conductor => conductor.estado === this.estadoFilter);
    }

    // Actualizar de manera segura
    setTimeout(() => {
      this.conductoresFiltrados = filtrados;
      this.mostrarContadorResultados = filtrados.length !== this.conductores.length;
      this.cdr.detectChanges();
    }, 0);
  }

  clearFilters() {
    this.searchTerm = '';
    this.estadoFilter = '';
    
    setTimeout(() => {
      this.conductoresFiltrados = [...this.conductores];
      this.mostrarContadorResultados = false;
      this.cdr.detectChanges();
    }, 0);
    
    this.mostrarNotificacion('Filtros limpiados', 'info');
  }

  // Métodos para notificaciones
  mostrarNotificacion(mensaje: string, tipo: 'success' | 'error' | 'warning' | 'info' = 'info') {
    this.snackBar.open(mensaje, 'Cerrar', {
      duration: 3000,
      horizontalPosition: 'right',
      verticalPosition: 'top',
      panelClass: [`snackbar-${tipo}`]
    });
  }
}
