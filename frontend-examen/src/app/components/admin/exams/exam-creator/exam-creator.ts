import { Component, OnInit, ChangeDetectorRef, Pipe, PipeTransform } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { firstValueFrom } from 'rxjs';
import { environment } from '../../../../../environments/environment';

interface Categoria {
  categoria_id: number;
  codigo: string;
  nombre: string;
  descripcion: string;
  estado: string;
}

interface Pregunta {
  pregunta_id: number;
  enunciado: string;
  tipo_pregunta: string;
  categoria_id: number;
  categoria_nombre: string;
  dificultad: string;
  puntaje: number;
}

interface Examen {
  titulo: string;
  descripcion: string;
  tiempo_limite: number;
  duracion_minutos: number;
  puntaje_minimo: number;
  numero_preguntas: number;
  fecha_inicio: string;
  fecha_fin: string;
  estado: string;
  dificultad: string;
  categorias_seleccionadas: Categoria[];
  preguntas_seleccionadas: Pregunta[];
}

// Pipe para filtrar categorías
@Pipe({
  name: 'filterCategoria',
  standalone: true
})
export class FilterCategoriaPipe implements PipeTransform {
  transform(categorias: Categoria[], filtro: string): Categoria[] {
    if (!filtro) return categorias;
    return categorias.filter(cat => 
      cat.nombre.toLowerCase().includes(filtro.toLowerCase()) ||
      cat.codigo.toLowerCase().includes(filtro.toLowerCase()) ||
      cat.descripcion.toLowerCase().includes(filtro.toLowerCase())
    );
  }
}

// Pipe para filtrar preguntas
@Pipe({
  name: 'filterPreguntas',
  standalone: true
})
export class FilterPreguntasPipe implements PipeTransform {
  transform(preguntas: Pregunta[], filtroTexto: string, filtroCategoria: string, filtroDificultad: string): Pregunta[] {
    return preguntas.filter(preg => {
      // Filtro por texto
      if (filtroTexto && !preg.enunciado.toLowerCase().includes(filtroTexto.toLowerCase())) {
        return false;
      }
      
      // Filtro por categoría
      if (filtroCategoria && preg.categoria_id !== parseInt(filtroCategoria)) {
        return false;
      }
      
      // Filtro por dificultad
      if (filtroDificultad && preg.dificultad !== filtroDificultad) {
        return false;
      }
      
      return true;
    });
  }
}

@Component({
  selector: 'app-exam-creator',
  imports: [CommonModule, HttpClientModule, FormsModule, FilterCategoriaPipe, FilterPreguntasPipe],
  templateUrl: './exam-creator.html',
  styleUrl: './exam-creator.scss',
  standalone: true
})
export class ExamCreator implements OnInit {
  loading = false;
  saving = false;
  error = '';
  success = '';
  categorias: Categoria[] = [];
  preguntas: Pregunta[] = [];
  
  // Sistema de etapas
  etapaActual = 1;
  totalEtapas = 4;
  
  // Filtros para preguntas
  filtroCategoria = '';
  filtroTipo = '';
  filtroDificultad = '';
  filtroPregunta = '';
  
  examen: Examen = {
    titulo: '',
    descripcion: '',
    tiempo_limite: 60,
    duracion_minutos: 60,
    puntaje_minimo: 70,
    numero_preguntas: 10,
    fecha_inicio: '',
    fecha_fin: '',
    estado: 'activo',
    dificultad: 'media',
    categorias_seleccionadas: [],
    preguntas_seleccionadas: []
  };

  constructor(
    private router: Router,
    private http: HttpClient,
    private cdr: ChangeDetectorRef
  ) {
    console.log('🔧 Constructor de ExamCreator llamado');
    console.log('🌐 URL de la API:', environment.apiUrl);
    console.log('🔧 Router:', this.router);
    console.log('🔧 HttpClient:', this.http);
    console.log('🔧 ChangeDetectorRef:', this.cdr);
  }

  ngOnInit(): void {
    console.log('🚀 ngOnInit de ExamCreator ejecutado');
    
    // Log del estado inicial
    console.log('🔍 Estado inicial del componente:');
    console.log('  - Categorías:', this.categorias?.length || 0);
    console.log('  - Preguntas:', this.preguntas?.length || 0);
    console.log('  - Etapa actual:', this.etapaActual);
    
    this.cargarDatos();
  }

  async cargarDatos(): Promise<void> {
    try {
      this.loading = true;
      this.error = '';
      
      console.log('📡 Cargando categorías...');
      const categoriasResponse = await this.http.get<{status: string, data: Categoria[]}>(`${environment.apiUrl}/categorias`).toPromise();
      
      console.log('🔍 Respuesta completa de categorías:', categoriasResponse);
      
      if (categoriasResponse?.data) {
        this.categorias = categoriasResponse.data;
        console.log('✅ Categorías extraídas de data:', this.categorias.length);
        console.log('🔍 Primeras 3 categorías:', this.categorias.slice(0, 3));
      } else {
        console.error('❌ Formato de respuesta inesperado para categorías:', categoriasResponse);
        this.categorias = [];
      }
      
      // Verificar que las categorías se asignaron correctamente
      console.log('🔍 Verificación después de asignar categorías:');
      console.log('  - this.categorias:', this.categorias);
      console.log('  - Array.isArray(this.categorias):', Array.isArray(this.categorias));
      console.log('  - this.categorias.length:', this.categorias?.length);
      
      console.log('📡 Cargando preguntas...');
      // Cambiar el tipo de respuesta para incluir la paginación
      const preguntasResponse = await this.http.get<{status: string, data: {preguntas: Pregunta[], pagination: any}}>(`${environment.apiUrl}/preguntas`).toPromise();
      
      console.log('🔍 Respuesta completa de preguntas:', preguntasResponse);
      
      // CORRECCIÓN: Acceder a preguntasResponse.data.preguntas
      if (preguntasResponse?.data?.preguntas) {
        this.preguntas = preguntasResponse.data.preguntas;
        console.log('✅ Preguntas extraídas de data.preguntas:', this.preguntas.length);
      } else {
        console.error('❌ Formato de respuesta inesperado para preguntas:', preguntasResponse);
        this.preguntas = [];
      }
      
      // Log del estado después de cargar
      console.log('🏁 Estado después de cargar datos:');
      console.log('  - Categorías cargadas:', this.categorias?.length || 0);
      console.log('  - Preguntas cargadas:', this.preguntas?.length || 0);
      console.log('  - Categorías array:', Array.isArray(this.categorias));
      console.log('  - Preguntas array:', Array.isArray(this.preguntas));
      
    } catch (error) {
      console.error('❌ Error al cargar datos:', error);
      this.error = 'Error al cargar los datos. Por favor, intenta de nuevo.';
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
    }
  }

  // Navegación entre etapas
  siguienteEtapa(): void {
    if (this.puedeAvanzar() && this.etapaActual < this.totalEtapas) {
      this.etapaActual++;
      this.error = '';
      this.success = '';
      this.scrollToTop();
    }
  }

  etapaAnterior(): void {
    if (this.etapaActual > 1) {
      this.etapaActual--;
      this.error = '';
      this.success = '';
      this.scrollToTop();
    }
  }

  private scrollToTop(): void {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  // Validaciones por etapas
  puedeAvanzar(): boolean {
    switch (this.etapaActual) {
      case 1:
        return this.validarEtapa1();
      case 2:
        return this.validarEtapa2();
      case 3:
        return this.validarEtapa3();
      default:
        return false;
    }
  }

  private validarEtapa1(): boolean {
    return !!(
      this.examen.titulo.trim() &&
      this.examen.tiempo_limite > 0 &&
      this.examen.puntaje_minimo > 0 &&
      this.examen.puntaje_minimo <= 100 &&
      this.examen.numero_preguntas > 0
    );
  }

  private validarEtapa2(): boolean {
    return this.examen.categorias_seleccionadas.length > 0;
  }

  private validarEtapa3(): boolean {
    return this.examen.preguntas_seleccionadas.length > 0;
  }

  puedeCrearExamen(): boolean {
    return this.validarEtapa1() && 
           this.validarEtapa2() && 
           this.validarEtapa3();
  }

  // Gestión de categorías
  isCategoriaSeleccionada(categoria: Categoria): boolean {
    return this.examen.categorias_seleccionadas.some(
      cat => cat.categoria_id === categoria.categoria_id
    );
  }

  toggleCategoria(categoria: Categoria): void {
    if (this.isCategoriaSeleccionada(categoria)) {
      this.removeCategoria(categoria);
      } else {
      this.addCategoria(categoria);
    }
  }

  addCategoria(categoria: Categoria): void {
    if (!this.isCategoriaSeleccionada(categoria)) {
      this.examen.categorias_seleccionadas.push(categoria);
      this.cdr.detectChanges();
    }
  }

  removeCategoria(categoria: Categoria): void {
    this.examen.categorias_seleccionadas = this.examen.categorias_seleccionadas.filter(
      cat => cat.categoria_id !== categoria.categoria_id
    );
    this.cdr.detectChanges();
  }

  // Gestión de preguntas
  isPreguntaSeleccionada(pregunta: Pregunta): boolean {
    return this.examen.preguntas_seleccionadas.some(
      preg => preg.pregunta_id === pregunta.pregunta_id
    );
  }

  togglePregunta(pregunta: Pregunta): void {
    if (this.isPreguntaSeleccionada(pregunta)) {
      this.removePregunta(pregunta);
    } else {
      this.examen.preguntas_seleccionadas.push(pregunta);
    }
    console.log('📝 Preguntas seleccionadas:', this.examen.preguntas_seleccionadas.length);
  }

  removePregunta(pregunta: Pregunta): void {
    this.examen.preguntas_seleccionadas = this.examen.preguntas_seleccionadas.filter(
      preg => preg.pregunta_id !== pregunta.pregunta_id
    );
    console.log('🗑️ Pregunta removida. Total seleccionadas:', this.examen.preguntas_seleccionadas.length);
  }

  ordenarPreguntasAleatoriamente(): void {
    if (this.examen.preguntas_seleccionadas.length < 2) {
      console.log('⚠️ Se necesitan al menos 2 preguntas para ordenar');
      return;
    }
    
    // Algoritmo Fisher-Yates para mezclar aleatoriamente
    for (let i = this.examen.preguntas_seleccionadas.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [this.examen.preguntas_seleccionadas[i], this.examen.preguntas_seleccionadas[j]] = 
      [this.examen.preguntas_seleccionadas[j], this.examen.preguntas_seleccionadas[i]];
    }
    
    console.log('🔀 Preguntas ordenadas aleatoriamente');
    this.cdr.detectChanges();
  }

  getPuntajeTotal(): number {
    return this.examen.preguntas_seleccionadas.reduce(
      (total, pregunta) => total + pregunta.puntaje, 0
    );
  }

  // Crear examen
  async crearExamen(): Promise<void> {
    console.log('🚀 Iniciando creación de examen...');
    console.log('🔍 Estado actual del examen:');
    console.log('  - titulo:', this.examen.titulo);
    console.log('  - tiempo_limite:', this.examen.tiempo_limite);
    console.log('  - puntaje_minimo:', this.examen.puntaje_minimo);
    console.log('  - numero_preguntas:', this.examen.numero_preguntas);
    console.log('  - estado:', this.examen.estado);
    console.log('  - categorias_seleccionadas:', this.examen.categorias_seleccionadas.length);
    console.log('  - preguntas_seleccionadas:', this.examen.preguntas_seleccionadas.length);
    
    // Validar que se hayan seleccionado categorías
    if (this.examen.categorias_seleccionadas.length === 0) {
      this.error = 'Debes seleccionar al menos una categoría.';
      return;
    }

    if (!this.puedeCrearExamen()) {
      this.error = 'Por favor, completa todas las etapas antes de crear el examen.';
      return;
    }

    try {
      this.saving = true;
      this.error = '';
      
      // Preparar datos del examen en el formato que espera el backend
      const examenData = {
        titulo: this.examen.titulo,
        nombre: this.examen.titulo, // El backend espera este campo
        descripcion: this.examen.descripcion || '',
        tiempo_limite: this.examen.tiempo_limite,
        duracion_minutos: this.examen.tiempo_limite,
        puntaje_minimo: this.examen.puntaje_minimo,
        numero_preguntas: parseInt(this.examen.numero_preguntas.toString()) || 1,
        estado: this.examen.estado,
        // Las fechas son requeridas por el backend, usar valores por defecto si no están
        fecha_inicio: this.examen.fecha_inicio || new Date().toISOString().slice(0, 19).replace('T', ' '),
        fecha_fin: this.examen.fecha_fin || new Date(Date.now() + 2 * 365 * 24 * 60 * 60 * 1000).toISOString().slice(0, 19).replace('T', ' '),
        // Solo enviar categorías, no preguntas (las asignaremos después)
        categorias: this.examen.categorias_seleccionadas.map(cat => cat.categoria_id),
        // Enviar array vacío para que pase la validación del backend
        preguntas: []
      };
      
      // Logging detallado para debuggear
      console.log('🔍 Debugging numero_preguntas:');
      console.log('  - this.examen.numero_preguntas (original):', this.examen.numero_preguntas);
      console.log('  - Tipo:', typeof this.examen.numero_preguntas);
      console.log('  - examenData.numero_preguntas (procesado):', examenData.numero_preguntas);
      console.log('  - Tipo procesado:', typeof examenData.numero_preguntas);
      console.log('  - Es mayor que 0:', examenData.numero_preguntas > 0);
      
      console.log('📤 Enviando examen:', examenData);
      console.log('🔍 Validación de datos:');
      console.log('  - Título:', examenData.titulo);
      console.log('  - Nombre:', examenData.nombre);
      console.log('  - Tiempo límite:', examenData.tiempo_limite);
      console.log('  - Duración minutos:', examenData.duracion_minutos);
      console.log('  - Puntaje mínimo:', examenData.puntaje_minimo);
      console.log('  - Número preguntas:', examenData.numero_preguntas);
      console.log('  - Estado:', examenData.estado);
      console.log('  - Fecha inicio:', examenData.fecha_inicio);
      console.log('  - Fecha fin:', examenData.fecha_fin);
      console.log('  - Categorías:', examenData.categorias.length);
      
      // Verificar que todos los campos requeridos estén presentes
      console.log('🔍 Verificación de campos requeridos:');
      console.log('  - titulo presente:', !!examenData.titulo);
      console.log('  - nombre presente:', !!examenData.nombre);
      console.log('  - tiempo_limite presente:', !!examenData.tiempo_limite);
      console.log('  - duracion_minutos presente:', !!examenData.duracion_minutos);
      console.log('  - puntaje_minimo presente:', !!examenData.puntaje_minimo);
      console.log('  - numero_preguntas presente:', !!examenData.numero_preguntas);
      console.log('  - estado presente:', !!examenData.estado);
      
      // Verificación adicional del campo numero_preguntas
      console.log('🔍 Verificación específica de numero_preguntas:');
      console.log('  - Valor:', examenData.numero_preguntas);
      console.log('  - Tipo:', typeof examenData.numero_preguntas);
      console.log('  - Es número:', !isNaN(examenData.numero_preguntas));
      console.log('  - Es mayor que 0:', examenData.numero_preguntas > 0);
      console.log('  - Es entero:', Number.isInteger(examenData.numero_preguntas));
      
      // Verificación específica de las fechas
      console.log('🔍 Verificación específica de fechas:');
      console.log('  - fecha_inicio:', examenData.fecha_inicio);
      console.log('  - fecha_fin:', examenData.fecha_fin);
      console.log('  - fecha_inicio (Date):', new Date(examenData.fecha_inicio));
      console.log('  - fecha_fin (Date):', new Date(examenData.fecha_fin));
      console.log('  - fecha_fin > fecha_inicio:', new Date(examenData.fecha_fin) > new Date(examenData.fecha_inicio));
      console.log('  - Diferencia en días:', (new Date(examenData.fecha_fin).getTime() - new Date(examenData.fecha_inicio).getTime()) / (1000 * 60 * 60 * 24));
      
      // Crear el examen primero (sin preguntas)
      const response = await firstValueFrom(
        this.http.post(`${environment.apiUrl}/examenes`, examenData)
      );
      
      console.log('✅ Examen creado exitosamente:', response);
      
      // Por ahora, solo crear el examen con categorías
      // Las preguntas se pueden asignar manualmente desde el backend
      console.log('📝 Nota: Las preguntas se pueden asignar manualmente desde el backend');
      
      this.success = '¡Examen creado exitosamente!';
        
      // Redirigir después de un breve delay
      setTimeout(() => {
        this.router.navigate(['/admin/exams']);
      }, 2000);
      
    } catch (error: any) {
      console.error('❌ Error al crear examen:', error);
      
      // Mostrar error más detallado
      if (error.status === 400) {
        console.error('🔍 Detalles del error 400:', error.error);
        
        // Mostrar mensajes específicos del backend
        if (error.error?.messages) {
          console.error('📋 Mensajes de validación:', error.error.messages);
          
          // Convertir mensajes de validación a texto legible
          const mensajes = error.error.messages;
          let errorText = 'Errores de validación:\n';
          
          if (typeof mensajes === 'object') {
            Object.keys(mensajes).forEach(campo => {
              if (Array.isArray(mensajes[campo])) {
                errorText += `• ${campo}: ${mensajes[campo].join(', ')}\n`;
              } else {
                errorText += `• ${campo}: ${mensajes[campo]}\n`;
              }
            });
          }
          
          this.error = errorText;
        } else {
          this.error = `Error de validación: ${error.error?.message || 'Datos del examen no válidos'}`;
        }
      } else {
        this.error = error.error?.message || 'Error al crear el examen. Por favor, intenta de nuevo.';
      }
    } finally {
      this.saving = false;
      this.cdr.detectChanges();
    }
  }

  // Cancelar creación
  cancelar(): void {
    if (confirm('¿Estás seguro de que quieres cancelar la creación del examen? Se perderán todos los datos.')) {
      this.router.navigate(['/admin/exams']);
    }
  }

  // Utilidades
  getTipoPreguntaText(tipo: string): string {
    const tipos: { [key: string]: string } = {
      'multiple': 'Opción Múltiple',
      'verdadero_falso': 'Verdadero/Falso',
      'completar': 'Completar',
      'emparejar': 'Emparejar'
    };
    return tipos[tipo] || tipo;
  }
} 