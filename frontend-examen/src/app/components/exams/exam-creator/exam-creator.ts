import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { ApiService } from '../../../services/api.service';

interface Examen {
  examen_id?: number;
  nombre: string;
  descripcion?: string;
  fecha_inicio: string;
  fecha_fin: string;
  duracion_minutos: number;
  puntaje_minimo: number;
  numero_preguntas: number;
  categorias: number[];
  preguntas: PreguntaExamen[];
}

interface PreguntaExamen {
  pregunta_id: number;
  orden: number;
}

interface Categoria {
  categoria_id: number;
  nombre: string;
  codigo: string;
  descripcion?: string;
  estado: 'activo' | 'inactivo';
}

interface Pregunta {
  pregunta_id: number;
  enunciado: string;
  tipo: 'multiple' | 'verdadero_falso';
  puntaje: number;
  dificultad: 'baja' | 'media' | 'alta';
  es_critica: boolean;
  categoria_id?: number;
  respuestas?: Respuesta[];
}

interface Respuesta {
  respuesta_id: number;
  texto: string;
  es_correcta: boolean;
}

@Component({
  selector: 'app-exam-creator',
  templateUrl: './exam-creator.html',
  styleUrls: ['./exam-creator.scss'],
  imports: [CommonModule, FormsModule],
  standalone: true
})
export class ExamCreator implements OnInit {
  examen: Examen = {
    nombre: '',
    descripcion: '',
    fecha_inicio: '',
    fecha_fin: '',
    duracion_minutos: 30,
    puntaje_minimo: 70,
    numero_preguntas: 0,
    categorias: [],
    preguntas: []
  };

  categorias: Categoria[] = [];
  preguntas: Pregunta[] = [];
  preguntasSeleccionadas: Pregunta[] = [];
  
  // Estados
  loading = false;
  error = '';
  success = '';
  
  // Pasos del formulario
  pasoActual = 1;
  totalPasos = 4;
  
  // Filtros para preguntas
  filtroCategoria = '';
  filtroDificultad = '';
  filtroTipo = '';
  filtroTexto = '';

  constructor(
    private apiService: ApiService,
    private router: Router,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    this.cargarCategorias();
    this.cargarPreguntas();
    this.establecerFechasPorDefecto();
  }

  private establecerFechasPorDefecto() {
    const ahora = new Date();
    const fin = new Date();
    fin.setDate(fin.getDate() + 30); // 30 días después
    
    this.examen.fecha_inicio = ahora.toISOString().slice(0, 16);
    this.examen.fecha_fin = fin.toISOString().slice(0, 16);
  }

  async cargarCategorias() {
    try {
      console.log('🔄 Cargando categorías...');
      const response = await this.apiService.get('/categorias').toPromise();
      console.log('✅ Categorías cargadas:', response);
      this.categorias = (response?.data as Categoria[]) || [];
    } catch (error: any) {
      console.error('❌ Error al cargar categorías:', error);
      this.error = 'Error al cargar las categorías: ' + (error.message || 'Error desconocido');
    } finally {
      this.cdr.detectChanges();
    }
  }

  async cargarPreguntas() {
    try {
      console.log('🔄 Cargando preguntas...');
      const response = await this.apiService.get('/preguntas').toPromise();
      console.log('✅ Preguntas cargadas:', response);
      this.preguntas = (response?.data as Pregunta[]) || [];
    } catch (error: any) {
      console.error('❌ Error al cargar preguntas:', error);
      this.error = 'Error al cargar las preguntas: ' + (error.message || 'Error desconocido');
    } finally {
      this.cdr.detectChanges();
    }
  }

  getPreguntasFiltradas(): Pregunta[] {
    return this.preguntas.filter(pregunta => {
      // Filtro por categoría
      if (this.filtroCategoria && pregunta.categoria_id?.toString() !== this.filtroCategoria) {
        return false;
      }
      
      // Filtro por dificultad
      if (this.filtroDificultad && pregunta.dificultad !== this.filtroDificultad) {
        return false;
      }
      
      // Filtro por tipo
      if (this.filtroTipo && pregunta.tipo !== this.filtroTipo) {
        return false;
      }
      
      // Filtro por texto
      if (this.filtroTexto && !pregunta.enunciado.toLowerCase().includes(this.filtroTexto.toLowerCase())) {
        return false;
      }
      
      return true;
    });
  }

  togglePregunta(pregunta: Pregunta) {
    const index = this.preguntasSeleccionadas.findIndex(p => p.pregunta_id === pregunta.pregunta_id);
    
    if (index >= 0) {
      this.preguntasSeleccionadas.splice(index, 1);
    } else {
      this.preguntasSeleccionadas.push(pregunta);
    }
    
    this.actualizarPreguntasExamen();
  }

  private actualizarPreguntasExamen() {
    this.examen.preguntas = this.preguntasSeleccionadas.map((pregunta, index) => ({
      pregunta_id: pregunta.pregunta_id,
      orden: index + 1
    }));
    
    this.examen.numero_preguntas = this.preguntasSeleccionadas.length;
  }

  isPreguntaSeleccionada(pregunta: Pregunta): boolean {
    return this.preguntasSeleccionadas.some(p => p.pregunta_id === pregunta.pregunta_id);
  }

  moverPregunta(index: number, direccion: 'arriba' | 'abajo') {
    if (direccion === 'arriba' && index > 0) {
      const temp = this.preguntasSeleccionadas[index];
      this.preguntasSeleccionadas[index] = this.preguntasSeleccionadas[index - 1];
      this.preguntasSeleccionadas[index - 1] = temp;
    } else if (direccion === 'abajo' && index < this.preguntasSeleccionadas.length - 1) {
      const temp = this.preguntasSeleccionadas[index];
      this.preguntasSeleccionadas[index] = this.preguntasSeleccionadas[index + 1];
      this.preguntasSeleccionadas[index + 1] = temp;
    }
    
    this.actualizarPreguntasExamen();
  }

  eliminarPregunta(index: number) {
    this.preguntasSeleccionadas.splice(index, 1);
    this.actualizarPreguntasExamen();
  }

  siguientePaso() {
    if (this.validarPasoActual()) {
      this.pasoActual = Math.min(this.pasoActual + 1, this.totalPasos);
      this.error = '';
      this.success = '';
    }
  }

  pasoAnterior() {
    this.pasoActual = Math.max(this.pasoActual - 1, 1);
    this.error = '';
    this.success = '';
  }

  validarPasoActual(): boolean {
    switch (this.pasoActual) {
      case 1: // Información básica
        if (!this.examen.nombre.trim()) {
          this.error = 'El nombre del examen es requerido';
          return false;
        }
        if (!this.examen.fecha_inicio || !this.examen.fecha_fin) {
          this.error = 'Las fechas de inicio y fin son requeridas';
          return false;
        }
        if (new Date(this.examen.fecha_inicio) >= new Date(this.examen.fecha_fin)) {
          this.error = 'La fecha de fin debe ser posterior a la fecha de inicio';
          return false;
        }
        if (this.examen.duracion_minutos <= 0) {
          this.error = 'La duración debe ser mayor a 0';
          return false;
        }
        if (this.examen.puntaje_minimo < 0 || this.examen.puntaje_minimo > 100) {
          this.error = 'El puntaje mínimo debe estar entre 0 y 100';
          return false;
        }
        break;
        
      case 2: // Categorías
        if (this.examen.categorias.length === 0) {
          this.error = 'Debe seleccionar al menos una categoría';
          return false;
        }
        break;
        
      case 3: // Preguntas
        if (this.preguntasSeleccionadas.length === 0) {
          this.error = 'Debe seleccionar al menos una pregunta';
          return false;
        }
        break;
    }
    
    return true;
  }

  async crearExamen() {
    if (!this.validarPasoActual()) {
      return;
    }

    this.loading = true;
    this.error = '';
    this.success = '';

    try {
      // Enviar datos según el controlador ExamenController
      const datosExamen = {
        nombre: this.examen.nombre,
        descripcion: this.examen.descripcion || '',
        categorias: this.examen.categorias.map(c => parseInt(c.toString())), // Array de IDs de categorías
        preguntas: this.preguntasSeleccionadas.map(pregunta => ({
          categoria_id: pregunta.categoria_id || parseInt(this.examen.categorias[0]?.toString() || '0'),
          enunciado: pregunta.enunciado,
          tipo: pregunta.tipo,
          dificultad: pregunta.dificultad,
          puntaje: parseInt(pregunta.puntaje.toString()),
          es_critica: pregunta.es_critica,
          respuestas: pregunta.respuestas || []
        })),
        fecha_inicio: new Date(this.examen.fecha_inicio).toISOString(),
        fecha_fin: new Date(this.examen.fecha_fin).toISOString(),
        duracion_minutos: parseInt(this.examen.duracion_minutos.toString()),
        puntaje_minimo: parseInt(this.examen.puntaje_minimo.toString())
      };

      // Validar que todos los campos requeridos estén presentes
      if (!datosExamen.nombre || datosExamen.categorias.length === 0 || datosExamen.preguntas.length === 0) {
        this.error = 'Faltan datos requeridos para crear el examen';
        this.loading = false;
        this.cdr.detectChanges();
        return;
      }

      console.log('🚀 Creando examen con datos:', datosExamen);
      console.log('📊 JSON que se enviará:', JSON.stringify(datosExamen, null, 2));
      
      const response = await this.apiService.post('/examenes', datosExamen).toPromise();
      console.log('✅ Examen creado:', response);
      this.success = 'Examen creado exitosamente';
      
      setTimeout(() => {
        this.router.navigate(['/admin']);
      }, 2000);
      
    } catch (error: any) {
      console.error('❌ Error al crear examen:', error);
      this.error = error.message || 'Error al crear el examen';
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
    }
  }

  getProgreso(): number {
    return (this.pasoActual / this.totalPasos) * 100;
  }

  getPreguntasPorCategoria(): {categoria: string, count: number}[] {
    const categorias = new Map<string, number>();
    
    this.preguntasSeleccionadas.forEach(pregunta => {
      const categoria = this.categorias.find(c => c.categoria_id === pregunta.categoria_id)?.nombre || 'Sin categoría';
      categorias.set(categoria, (categorias.get(categoria) || 0) + 1);
    });
    
    return Array.from(categorias.entries()).map(([categoria, count]) => ({ categoria, count }));
  }

  getPreguntasPorDificultad(): {dificultad: string, count: number}[] {
    const dificultades = new Map<string, number>();
    
    this.preguntasSeleccionadas.forEach(pregunta => {
      const dificultad = pregunta.dificultad.charAt(0).toUpperCase() + pregunta.dificultad.slice(1);
      dificultades.set(dificultad, (dificultades.get(dificultad) || 0) + 1);
    });
    
    return Array.from(dificultades.entries()).map(([dificultad, count]) => ({ dificultad, count }));
  }

  getPuntajeTotal(): number {
    return this.preguntasSeleccionadas.reduce((total, pregunta) => {
      const puntaje = typeof pregunta.puntaje === 'number' ? pregunta.puntaje : parseFloat(pregunta.puntaje) || 0;
      return total + puntaje;
    }, 0);
  }

  getDificultadPromedio(): string {
    const dificultades = { baja: 1, media: 2, alta: 3 };
    const promedio = this.preguntasSeleccionadas.reduce((total, pregunta) => total + dificultades[pregunta.dificultad], 0) / this.preguntasSeleccionadas.length;
    
    if (promedio <= 1.5) return 'Baja';
    if (promedio <= 2.5) return 'Media';
    return 'Alta';
  }

  toggleCategoria(categoriaId: number) {
    const index = this.examen.categorias.indexOf(categoriaId);
    if (index >= 0) {
      this.examen.categorias.splice(index, 1);
    } else {
      this.examen.categorias.push(categoriaId);
    }
  }

  getCategoriaNombre(categoriaId?: number): string {
    if (!categoriaId) return 'Sin categoría';
    const categoria = this.categorias.find(c => c.categoria_id === categoriaId);
    return categoria ? `${categoria.codigo} - ${categoria.nombre}` : 'Sin categoría';
  }

  formatDate(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }
} 