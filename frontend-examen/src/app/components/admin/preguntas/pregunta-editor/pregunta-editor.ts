import { Component, OnInit, ChangeDetectorRef, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, ActivatedRoute } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { firstValueFrom } from 'rxjs';
import { ApiService } from '../../../../services/api.service';
import { NotificationService } from '../../../../services/notification.service';
import { LoadingService } from '../../../../services/loading.service';
import { NgZone } from '@angular/core';
import { Pregunta, Respuesta, Categoria, CategoriasResponse } from '../../../../shared/interfaces';

@Component({
  selector: 'app-pregunta-editor',
  imports: [CommonModule, FormsModule],
  templateUrl: './pregunta-editor.html',
  styleUrl: './pregunta-editor.scss',
  standalone: true
})
export class PreguntaEditor implements OnInit {
  pregunta: Partial<Pregunta> = {
    enunciado: '',
    tipo_pregunta: 'multiple',
    categoria_id: 0,
    puntaje: 1,
    dificultad: 'medio',
    es_critica: false,
    respuestas: []
  };

  categorias: any[] = [];
  loading = false;
  saving = false;
  preguntaId?: number;

  // Configuración de tipos de pregunta (corregidos para coincidir con el backend)
  tiposPregunta = [
    { value: 'multiple', label: 'Opción Múltiple', desc: 'Seleccionar una o más respuestas correctas' },
    { value: 'unica', label: 'Opción Única', desc: 'Seleccionar una sola respuesta correcta' },
    { value: 'verdadero_falso', label: 'Verdadero/Falso', desc: 'Respuesta de verdadero o falso' }
  ];

  dificultades = [
    { value: 'facil', label: 'Fácil' },
    { value: 'medio', label: 'Medio' },
    { value: 'dificil', label: 'Difícil' }
  ];

  // Inyección de servicios
  private apiService = inject(ApiService);
  private notificationService = inject(NotificationService);
  private loadingService = inject(LoadingService);
  private router = inject(Router);
  private route = inject(ActivatedRoute);
  private cdr = inject(ChangeDetectorRef);
  private ngZone = inject(NgZone);

  ngOnInit() {
    console.log('🚀 PreguntaEditor inicializado');
    console.log('📊 Estado inicial de loading:', this.loading);
    this.cargarCategorias();
    
    // Si es edición, cargar la pregunta
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.preguntaId = Number(id);
      console.log('📝 Editando pregunta ID:', this.preguntaId);
      this.cargarPregunta(this.preguntaId);
    } else {
      console.log('🆕 Creando nueva pregunta');
      this.inicializarRespuestas();
    }
  }

  async cargarCategorias() {
    try {
      const response = await firstValueFrom(
        this.apiService.get<CategoriasResponse>('/categorias')
      );
      
      if (response?.data) {
        // Manejo directo de las categorías
        if (Array.isArray(response.data)) {
          this.categorias = response.data;
        } else if (response.data.categorias) {
          this.categorias = response.data.categorias;
        }
        this.cdr.markForCheck();
      }
    } catch (error) {
      console.error('Error cargando categorías:', error);
    }
  }

  async cargarPregunta(id: number) {
    console.log('🔄 Iniciando carga de pregunta...');
    
    // Usar setTimeout(0) para evitar ExpressionChangedAfterItHasBeenCheckedError
    this.ngZone.runOutsideAngular(() => {
      setTimeout(() => {
        this.ngZone.run(() => {
          this.loading = true;
          this.loadingService.showGlobalLoading('Cargando pregunta...');
          this.cdr.markForCheck();
        });
      }, 0);
    });
    
    console.log('📊 Estado loading:', this.loading);
    
    try {
      const response = await firstValueFrom(
        this.apiService.get(`/preguntas/${id}`)
      );
      
      if (response?.data) {
        console.log('Pregunta cargada del servidor:', response.data);
        this.pregunta = response.data as Pregunta;
        
        // Solo inicializar respuestas si no existen
        if (!this.pregunta.respuestas || this.pregunta.respuestas.length === 0) {
          this.inicializarRespuestas();
        } else {
          console.log('Respuestas existentes encontradas:', this.pregunta.respuestas);
        }
      }
    } catch (error: any) {
      console.error('Error cargando pregunta:', error);
      
      // Manejar errores de autenticación
      if (error.status === 401) {
        this.notificationService.error('Sesión expirada. Redirigiendo al login...');
        setTimeout(() => {
          this.router.navigate(['/login']);
        }, 2000);
      } else if (error.status === 404) {
        this.notificationService.error('Pregunta no encontrada');
        setTimeout(() => {
          this.router.navigate(['/admin/preguntas']);
        }, 2000);
      } else {
        this.notificationService.error('Error al cargar la pregunta: ' + (error.message || 'Error desconocido'));
      }
    } finally {
      console.log('🏁 Finalizando carga de pregunta...');
      
      // Usar setTimeout(0) para evitar ExpressionChangedAfterItHasBeenCheckedError
      this.ngZone.runOutsideAngular(() => {
        setTimeout(() => {
          this.ngZone.run(() => {
            this.loading = false;
            this.loadingService.hideGlobalLoading();
            this.cdr.markForCheck();
          });
        }, 0);
      });
      
      console.log('📊 Estado loading después de finalizar:', this.loading);
    }
  }

  inicializarRespuestas() {
    console.log('Inicializando respuestas para tipo:', this.pregunta.tipo_pregunta);
    
    // Solo inicializar si no hay respuestas existentes
    if (!this.pregunta.respuestas || this.pregunta.respuestas.length === 0) {
      // Inicializar respuestas según el tipo de pregunta
      switch (this.pregunta.tipo_pregunta) {
        case 'verdadero_falso':
          this.pregunta.respuestas = [
            { respuesta_id: undefined, texto: 'Verdadero', es_correcta: false, uploading: false },
            { respuesta_id: undefined, texto: 'Falso', es_correcta: false, uploading: false }
          ];
          break;
        case 'multiple':
        case 'unica':
          this.pregunta.respuestas = [
            { respuesta_id: undefined, texto: '', es_correcta: false, uploading: false },
            { respuesta_id: undefined, texto: '', es_correcta: false, uploading: false }
          ];
          break;
        default:
          // Para otros tipos, crear una básica
          this.pregunta.respuestas = [
            { respuesta_id: undefined, texto: '', es_correcta: false, uploading: false }
          ];
          break;
      }
      console.log('Respuestas inicializadas:', this.pregunta.respuestas);
    } else {
      console.log('Manteniendo respuestas existentes:', this.pregunta.respuestas);
    }
    this.cdr.markForCheck();
  }

  onTipoPreguntaChange() {
    console.log('Tipo de pregunta cambiado a:', this.pregunta.tipo_pregunta);
    
    // Solo reinicializar si no hay respuestas con contenido
    const tieneRespuestasConContenido = this.pregunta.respuestas && 
      this.pregunta.respuestas.some((r: Respuesta) => r.texto && r.texto.trim() !== '');
    
    if (!tieneRespuestasConContenido) {
      console.log('Reinicializando respuestas para nuevo tipo');
      this.inicializarRespuestas();
    } else {
      console.log('Manteniendo respuestas existentes al cambiar tipo');
    }
  }

  agregarRespuesta() {
    // Usar setTimeout(0) para evitar ExpressionChangedAfterItHasBeenCheckedError
    this.ngZone.runOutsideAngular(() => {
      setTimeout(() => {
        this.ngZone.run(() => {
          if (!this.pregunta.respuestas) {
            this.pregunta.respuestas = [];
          }
          this.pregunta.respuestas.push({
            respuesta_id: undefined,
            texto: '',
            es_correcta: false,
            uploading: false
          });
          this.cdr.markForCheck();
        });
      }, 0);
    });
  }

  eliminarRespuesta(index: number) {
    // Permitir eliminar respuestas siempre que queden al menos 2
    if (this.pregunta.respuestas && this.pregunta.respuestas.length > 2) {
      // Usar NgZone para evitar ExpressionChangedAfterItHasBeenCheckedError
      this.ngZone.runOutsideAngular(() => {
        // Programar el cambio de estado para el siguiente tick
        Promise.resolve().then(() => {
          this.ngZone.run(() => {
            this.pregunta.respuestas?.splice(index, 1);
            this.cdr.markForCheck();
          });
        });
      });
    } else {
      this.notificationService.error('Debe mantener al menos 2 respuestas');
    }
  }

  async subirImagen(event: any, respuestaIndex: number) {
    const file = event.target.files[0];
    if (!file || !this.pregunta.respuestas) return;

    // Marcar como subiendo
    this.pregunta.respuestas[respuestaIndex].uploading = true;

    // Logging temporal para debugging
    console.log('Archivo seleccionado:', file);
    console.log('Tipo MIME:', file.type);
    console.log('Tamaño:', file.size, 'bytes');
    console.log('Nombre:', file.name);

    try {
      // Usar el método correcto del ApiService
      console.log('Enviando archivo a:', '/files/upload-image');
      const response = await firstValueFrom(
        this.apiService.uploadFile('/files/upload-image', file)
      );

      if (response?.data && (response.data as any).filename) {
        console.log('Respuesta del servidor:', response);
        console.log('Filename recibido:', (response.data as any).filename);
        console.log('Respuesta antes de asignar imagen:', this.pregunta.respuestas?.[respuestaIndex]);
        
        // Usar NgZone para evitar ExpressionChangedAfterItHasBeenCheckedError
        this.ngZone.runOutsideAngular(() => {
          // Programar el cambio de estado para el siguiente tick
          Promise.resolve().then(() => {
            this.ngZone.run(() => {
              if (this.pregunta.respuestas) {
                this.pregunta.respuestas[respuestaIndex].imagen = (response.data as any).filename;
                this.pregunta.respuestas[respuestaIndex].uploading = false;
                console.log('Respuesta después de asignar imagen:', this.pregunta.respuestas[respuestaIndex]);
              }
              this.cdr.markForCheck();
            });
          });
        });
        
        // Mostrar notificación usando NgZone
        this.ngZone.runOutsideAngular(() => {
          Promise.resolve().then(() => {
            this.ngZone.run(() => {
              this.notificationService.success('Imagen subida correctamente');
            });
          });
        });
      }
    } catch (error) {
      console.error('Error subiendo imagen:', error);
      
      // Usar NgZone para evitar ExpressionChangedAfterItHasBeenCheckedError
      this.ngZone.runOutsideAngular(() => {
        // Programar el cambio de estado para el siguiente tick
        Promise.resolve().then(() => {
          this.ngZone.run(() => {
            if (this.pregunta.respuestas) {
              this.pregunta.respuestas[respuestaIndex].uploading = false;
            }
            this.cdr.markForCheck();
          });
        });
      });
      
      // Mostrar notificación usando NgZone
      this.ngZone.runOutsideAngular(() => {
        Promise.resolve().then(() => {
          this.ngZone.run(() => {
            this.notificationService.error('Error al subir la imagen');
          });
        });
      });
    }
  }

  onImageLoad(event: any) {
    console.log('Imagen cargada correctamente:', event.target.src);
  }

  onImageError(event: any) {
    console.error('Error cargando imagen:', event.target.src);
    // Opcional: mostrar notificación de error
    setTimeout(() => {
      this.notificationService.error('Error al cargar la imagen');
    }, 100);
  }

  eliminarImagen(respuestaIndex: number) {
    if (!this.pregunta.respuestas) return;
    const respuesta = this.pregunta.respuestas[respuestaIndex];
    if (respuesta.imagen) {
      // Usar NgZone para evitar ExpressionChangedAfterItHasBeenCheckedError
      this.ngZone.runOutsideAngular(() => {
        // Programar el cambio de estado para el siguiente tick
        Promise.resolve().then(() => {
          this.ngZone.run(() => {
            respuesta.imagen = undefined;
            this.cdr.markForCheck();
          });
        });
      });
    }
  }

     async guardarPregunta() {
    console.log('=== INICIO GUARDAR PREGUNTA ===');
    console.log('Pregunta a guardar:', this.pregunta);
    console.log('Pregunta ID:', this.preguntaId);
    
    if (!this.validarPregunta()) {
      console.log('Validación falló');
      return;
    }

    // Usar setTimeout(0) para evitar ExpressionChangedAfterItHasBeenCheckedError
    this.ngZone.runOutsideAngular(() => {
      setTimeout(() => {
        this.ngZone.run(() => {
          this.saving = true;
          this.cdr.markForCheck();
        });
      }, 0);
    });

    console.log('Enviando petición al servidor...');

    try {
      let response: any;
      if (this.preguntaId) {
        console.log('Actualizando pregunta existente...');
        response = await firstValueFrom(
          this.apiService.put(`/preguntas/${this.preguntaId}`, this.pregunta)
        );
      } else {
        console.log('Creando nueva pregunta...');
        response = await firstValueFrom(
          this.apiService.post('/preguntas', this.pregunta)
        );
      }

      console.log('Respuesta del servidor:', response);
      console.log('Status de respuesta:', response?.status);
      console.log('¿Es success?', response?.status === 'success');
      console.log('Tipo de status:', typeof response?.status);

      // Validación más robusta de la respuesta
      const esExitoso = response?.status === 'success' || 
                        response?.success === true || 
                        response?.status === 200 ||
                        (response?.data && response?.message && response?.message.includes('exitosamente'));

      console.log('🔍 Debug de validación:');
      console.log('  - response?.status:', response?.status);
      console.log('  - response?.success:', response?.success);
      console.log('  - response?.status === 200:', response?.status === 200);
      console.log('  - response?.data:', response?.data);
      console.log('  - response?.message:', response?.message);
      console.log('  - message.includes("exitosamente"):', response?.message?.includes('exitosamente'));
      console.log('  - esExitoso:', esExitoso);

      if (esExitoso) {
        console.log('✅ Pregunta guardada exitosamente');
        
        // Mostrar notificación exitosa usando setTimeout
        this.ngZone.runOutsideAngular(() => {
          setTimeout(() => {
            this.ngZone.run(() => {
              this.notificationService.success(this.preguntaId ? 'Pregunta actualizada correctamente' : 'Pregunta creada correctamente');
            });
          }, 0);
        });
        
        // Redirigir después de un breve delay
        setTimeout(() => {
          this.router.navigate(['/admin/preguntas']);
        }, 2000);
      } else {
        console.log('❌ Respuesta no exitosa:', response);
        console.log('Status recibido:', response?.status);
        console.log('Comparación falló');
        
        // Mostrar notificación de error usando setTimeout
        this.ngZone.runOutsideAngular(() => {
          setTimeout(() => {
            this.ngZone.run(() => {
              this.notificationService.error('Error: La respuesta del servidor no fue exitosa');
            });
          }, 0);
        });
      }
    } catch (error: any) {
      console.error('Error guardando pregunta:', error);
      
      // Mostrar notificación de error usando setTimeout
      this.ngZone.runOutsideAngular(() => {
        setTimeout(() => {
          this.ngZone.run(() => {
            this.notificationService.error(error.error?.message || 'Error al guardar la pregunta');
          });
        }, 0);
      });
    } finally {
      // Resetear estado de saving usando setTimeout
      this.ngZone.runOutsideAngular(() => {
        setTimeout(() => {
          this.ngZone.run(() => {
            this.saving = false;
            this.cdr.markForCheck();
          });
        }, 0);
      });
      console.log('=== FIN GUARDAR PREGUNTA ===');
    }
  }

  validarPregunta(): boolean {
    // Validar enunciado
    if (!this.pregunta.enunciado || !this.pregunta.enunciado.trim()) {
      this.mostrarErrorValidacion('El enunciado es requerido');
      return false;
    }

    // Validar categoría
    if (!this.pregunta.categoria_id || this.pregunta.categoria_id <= 0) {
      this.mostrarErrorValidacion('Debe seleccionar una categoría');
      return false;
    }

    // Validar puntaje
    if (!this.pregunta.puntaje || this.pregunta.puntaje <= 0) {
      this.mostrarErrorValidacion('El puntaje debe ser mayor a 0');
      return false;
    }

    // Validar respuestas según el tipo
    if (!this.pregunta.respuestas || this.pregunta.respuestas.length === 0) {
      this.mostrarErrorValidacion('Debe tener al menos una respuesta');
      return false;
    }

    switch (this.pregunta.tipo_pregunta) {
      case 'verdadero_falso':
        if (!this.pregunta.respuestas.some((r: Respuesta) => r.es_correcta)) {
          this.mostrarErrorValidacion('Debe seleccionar al menos una respuesta correcta');
          return false;
        }
        break;
      case 'multiple':
      case 'unica':
        // Filtrar respuestas que tengan texto o imagen
        const respuestasValidas = this.pregunta.respuestas.filter((r: Respuesta) => 
          (r.texto && r.texto.trim()) || r.imagen
        );
        
        if (respuestasValidas.length < 2) {
          this.mostrarErrorValidacion('Debe tener al menos 2 opciones de respuesta válidas');
          return false;
        }
        
        if (!this.pregunta.respuestas.some((r: Respuesta) => r.es_correcta)) {
          this.mostrarErrorValidacion('Debe seleccionar al menos una respuesta correcta');
          return false;
        }
        break;
      default:
        // Para otros tipos, validación básica
        const tieneContenido = this.pregunta.respuestas.some((r: Respuesta) => 
          (r.texto && r.texto.trim()) || r.imagen
        );
        if (!tieneContenido) {
          this.mostrarErrorValidacion('Debe tener al menos una respuesta con contenido');
          return false;
        }
        break;
    }

    return true;
  }
  
  // Método auxiliar para mostrar errores de validación
  private mostrarErrorValidacion(mensaje: string): void {
    this.notificationService.error(mensaje);
  }

  cancelar() {
    this.router.navigate(['/admin/preguntas']);
  }

  getImagenUrl(filename: string): string {
    return `/api/files/image/${filename}`;
  }

  getTipoDescripcion(): string {
    const tipo = this.tiposPregunta.find(t => t.value === this.pregunta.tipo_pregunta);
    return tipo ? tipo.desc : '';
  }
} 