import { Component, OnInit, OnDestroy, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { ActivatedRoute, Router } from '@angular/router';
import { environment } from '../../../../environments/environment';

interface Examen {
  examen_id: number;
  titulo: string;
  nombre: string;
  descripcion: string;
  tiempo_limite: number;
  duracion_minutos: number;
  puntaje_minimo: number;
  numero_preguntas: number;
  estado: 'activo' | 'inactivo';
  fecha_inicio: string;
  fecha_fin: string;
  categoria_id: number;
  categorias?: Categoria[];
  preguntas?: Pregunta[];
}

interface Pregunta {
  pregunta_id: number;
  examen_id: number;
  categoria_id: number;
  enunciado: string;
  tipo_pregunta: 'multiple' | 'unica';
  dificultad: 'facil' | 'medio' | 'dificil';
  puntaje: number;
  es_critica: boolean;
  respuestas?: Respuesta[];
  categoria?: Categoria;
}

interface Respuesta {
  respuesta_id: number;
  pregunta_id: number;
  texto: string;
  imagen?: string;
  es_correcta: boolean;
}

interface Categoria {
  categoria_id: number;
  nombre: string;
  descripcion: string;
}

interface RespuestaConductor {
  pregunta_id: number;
  respuesta_id?: number;
  respuesta_ids?: number[]; // Para preguntas múltiples
  tiempo_respuesta: number;
}

@Component({
  selector: 'app-exam-taker',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './exam-taker.html',
  styleUrls: ['./exam-taker.scss']
})
export class ExamTakerComponent implements OnInit, OnDestroy {
  examen: Examen | null = null;
  preguntas: Pregunta[] = [];
  respuestasConductor: RespuestaConductor[] = [];
  
  // Estados del examen
  loading = false;
  error = '';
  examStarted = false;
  examCompleted = false;
  examSubmitted = false;
  
  // Control de tiempo
  tiempoRestante = 0;
  tiempoInicio = 0;
  intervalId: any;
  
  // Navegación
  preguntaActual = 0;
  totalPreguntas = 0;
  
  // Progreso
  progreso = 0;
  preguntasRespondidas = 0;
  
  // Resultados
  puntajeObtenido = 0;
  puntajeTotal = 0;
  preguntasCorrectas = 0;
  preguntasIncorrectas = 0;
  preguntasCriticasFalladas = 0;

  constructor(
    private http: HttpClient,
    private route: ActivatedRoute,
    private router: Router,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    this.route.params.subscribe(params => {
      const examenId = params['id'];
      if (examenId) {
        this.cargarExamen(examenId);
      }
    });
  }

  ngOnDestroy() {
    if (this.intervalId) {
      clearInterval(this.intervalId);
    }
  }

  async cargarExamen(examenId: string) {
    this.loading = true;
    this.error = '';

    try {
      // Cargar examen
      const examenResponse = await this.http.get<{data: Examen}>(`${environment.apiUrl}/examenes/${examenId}`).toPromise();
      this.examen = examenResponse?.data || null;

      if (!this.examen) {
        throw new Error('Examen no encontrado');
      }

      // Cargar preguntas
      const preguntasResponse = await this.http.get<{data: {preguntas: Pregunta[]}}>(`${environment.apiUrl}/preguntas/examen/${examenId}`).toPromise();
      this.preguntas = preguntasResponse?.data?.preguntas || [];

      // Cargar respuestas para cada pregunta
      for (let pregunta of this.preguntas) {
        const respuestasResponse = await this.http.get<{data: Respuesta[]}>(`${environment.apiUrl}/respuestas/pregunta/${pregunta.pregunta_id}`).toPromise();
        pregunta.respuestas = respuestasResponse?.data || [];
      }

      this.totalPreguntas = this.preguntas.length;
      this.calcularPuntajeTotal();
      this.inicializarRespuestas();

    } catch (error: any) {
      console.error('Error cargando examen:', error);
      this.error = error.message || 'Error al cargar el examen';
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
    }
  }

  inicializarRespuestas() {
    this.respuestasConductor = this.preguntas.map(pregunta => ({
      pregunta_id: pregunta.pregunta_id,
      tiempo_respuesta: 0
    }));
  }

  iniciarExamen() {
    this.examStarted = true;
    this.tiempoInicio = Date.now();
    this.tiempoRestante = (this.examen?.tiempo_limite || 90) * 60; // Convertir a segundos

    this.intervalId = setInterval(() => {
      this.tiempoRestante--;
      if (this.tiempoRestante <= 0) {
        this.terminarExamen();
      }
      this.cdr.detectChanges();
    }, 1000);
  }

  terminarExamen() {
    if (this.intervalId) {
      clearInterval(this.intervalId);
    }
    this.examCompleted = true;
    this.calcularResultados();
  }

  async enviarExamen() {
    if (!this.examen) return;

    this.loading = true;
    this.error = '';

    try {
      const tiempoUtilizado = Math.floor((Date.now() - this.tiempoInicio) / 1000);
      
      const resultadoData = {
        examen_id: this.examen.examen_id,
        puntaje_obtenido: this.puntajeObtenido,
        puntaje_total: this.puntajeTotal,
        tiempo_utilizado: tiempoUtilizado,
        preguntas_correctas: this.preguntasCorrectas,
        preguntas_incorrectas: this.preguntasIncorrectas,
        preguntas_criticas_falladas: this.preguntasCriticasFalladas,
        aprobado: (this.puntajeObtenido / this.puntajeTotal) * 100 >= (this.examen.puntaje_minimo || 70),
        fecha_examen: new Date().toISOString()
      };

      const response = await this.http.post<{data: any}>(`${environment.apiUrl}/resultados/registrar`, resultadoData).toPromise();
      
      if (response) {
        this.examSubmitted = true;
        // Redirigir a resultados después de un momento
        setTimeout(() => {
          this.router.navigate(['/resultados']);
        }, 3000);
      }
    } catch (error: any) {
      console.error('Error enviando examen:', error);
      this.error = error.message || 'Error al enviar el examen';
    } finally {
      this.loading = false;
      this.cdr.detectChanges();
    }
  }

  seleccionarRespuesta(preguntaId: number, respuestaId: number, esMultiple: boolean = false) {
    const respuestaIndex = this.respuestasConductor.findIndex(r => r.pregunta_id === preguntaId);
    
    if (respuestaIndex === -1) return;

    const pregunta = this.preguntas.find(p => p.pregunta_id === preguntaId);
    if (!pregunta) return;

    if (esMultiple || pregunta.tipo_pregunta === 'multiple') {
      // Pregunta múltiple
      if (!this.respuestasConductor[respuestaIndex].respuesta_ids) {
        this.respuestasConductor[respuestaIndex].respuesta_ids = [];
      }
      
      const index = this.respuestasConductor[respuestaIndex].respuesta_ids!.indexOf(respuestaId);
      if (index > -1) {
        this.respuestasConductor[respuestaIndex].respuesta_ids!.splice(index, 1);
      } else {
        this.respuestasConductor[respuestaIndex].respuesta_ids!.push(respuestaId);
      }
    } else {
      // Pregunta única
      this.respuestasConductor[respuestaIndex].respuesta_id = respuestaId;
    }

    this.actualizarProgreso();
  }

  esRespuestaSeleccionada(preguntaId: number, respuestaId: number): boolean {
    const respuesta = this.respuestasConductor.find(r => r.pregunta_id === preguntaId);
    if (!respuesta) return false;

    const pregunta = this.preguntas.find(p => p.pregunta_id === preguntaId);
    if (!pregunta) return false;

    if (pregunta.tipo_pregunta === 'multiple') {
      return respuesta.respuesta_ids?.includes(respuestaId) || false;
    } else {
      return respuesta.respuesta_id === respuestaId;
    }
  }

  siguientePregunta() {
    if (this.preguntaActual < this.totalPreguntas - 1) {
      this.preguntaActual++;
    }
  }

  preguntaAnterior() {
    if (this.preguntaActual > 0) {
      this.preguntaActual--;
    }
  }

  irAPregunta(index: number) {
    if (index >= 0 && index < this.totalPreguntas) {
      this.preguntaActual = index;
    }
  }

  actualizarProgreso() {
    this.preguntasRespondidas = this.respuestasConductor.filter(r => 
      r.respuesta_id !== undefined || (r.respuesta_ids && r.respuesta_ids.length > 0)
    ).length;
    this.progreso = (this.preguntasRespondidas / this.totalPreguntas) * 100;
  }

  calcularPuntajeTotal() {
    this.puntajeTotal = this.preguntas.reduce((total, pregunta) => total + pregunta.puntaje, 0);
  }

    calcularResultados() {
    this.preguntasCorrectas = 0;
    this.preguntasIncorrectas = 0;
    this.preguntasCriticasFalladas = 0;
    this.puntajeObtenido = 0;

    for (let i = 0; i < this.preguntas.length; i++) {
      const pregunta = this.preguntas[i];
      const respuesta = this.respuestasConductor[i];
      
      if (!respuesta) continue;

      let esCorrecta = false;

      if (pregunta.tipo_pregunta === 'multiple') {
        // Para preguntas múltiples, todas las respuestas seleccionadas deben ser correctas
        const respuestasCorrectas = pregunta.respuestas?.filter(r => r.es_correcta).map(r => r.respuesta_id) || [];
        const respuestasSeleccionadas = respuesta.respuesta_ids || [];
        
        esCorrecta = respuestasCorrectas.length === respuestasSeleccionadas.length &&
                    respuestasCorrectas.every(id => respuestasSeleccionadas.includes(id));
      } else {
        // Para preguntas únicas
        const respuestaCorrecta = pregunta.respuestas?.find(r => r.es_correcta);
        esCorrecta = respuesta.respuesta_id === respuestaCorrecta?.respuesta_id;
      }

      if (esCorrecta) {
        this.preguntasCorrectas++;
        this.puntajeObtenido += pregunta.puntaje;
      } else {
        this.preguntasIncorrectas++;
        if (pregunta.es_critica) {
          this.preguntasCriticasFalladas++;
        }
      }
    }
  }

  formatearTiempo(segundos: number): string {
    const horas = Math.floor(segundos / 3600);
    const minutos = Math.floor((segundos % 3600) / 60);
    const segs = segundos % 60;
    
    if (horas > 0) {
      return `${horas}:${minutos.toString().padStart(2, '0')}:${segs.toString().padStart(2, '0')}`;
    } else {
      return `${minutos}:${segs.toString().padStart(2, '0')}`;
    }
  }

  getDificultadClass(dificultad: string): string {
    const clases = {
      'facil': 'badge-success',
      'medio': 'badge-warning',
      'dificil': 'badge-danger'
    };
    return clases[dificultad as keyof typeof clases] || 'badge-secondary';
  }

  getDificultadText(dificultad: string): string {
    const dificultades = {
      'facil': 'Fácil',
      'medio': 'Medio',
      'dificil': 'Difícil'
    };
    return dificultades[dificultad as keyof typeof dificultades] || dificultad;
  }

  getTipoText(tipo: string): string {
    const tipos = {
      'multiple': 'Múltiple',
      'unica': 'Única',
      'verdadero_falso': 'Verdadero/Falso'
    };
    return tipos[tipo as keyof typeof tipos] || tipo;
  }

  getResultadoClass(): string {
    if (!this.examen) return '';
    
    const porcentaje = (this.puntajeObtenido / this.puntajeTotal) * 100;
    
    if (this.preguntasCriticasFalladas > 0) {
      return 'resultado-reprobado';
    } else if (porcentaje >= this.examen.puntaje_minimo) {
      return 'resultado-aprobado';
    } else {
      return 'resultado-reprobado';
    }
  }

  getResultadoText(): string {
    if (this.preguntasCriticasFalladas > 0) {
      return 'REPROBADO - Pregunta crítica fallada';
    } else if (this.puntajeObtenido >= (this.examen?.puntaje_minimo || 0)) {
      return 'APROBADO';
    } else {
      return 'REPROBADO';
    }
  }
} 