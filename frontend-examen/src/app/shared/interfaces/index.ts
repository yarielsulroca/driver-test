export interface Pregunta {
  pregunta_id: number;
  enunciado: string;
  tipo_pregunta: string;
  categoria_id: number;
  categoria_nombre?: string;
  categoria_codigo?: string;
  dificultad: string;
  es_critica: boolean;
  puntaje: number;
  imagen_url?: string;
  respuestas: Respuesta[];
  created_at: string;
  updated_at: string;
  expanded?: boolean;
  loadingRespuestas?: boolean;
}

export interface Respuesta {
  respuesta_id?: number;
  texto: string;
  es_correcta: boolean;
  imagen_url?: string;
  imagen?: string;
  explicacion?: string;
  orden?: number;
  uploading?: boolean;
}

export interface Categoria {
  categoria_id: number;
  codigo: string;
  nombre: string;
  descripcion?: string;
  requisitos?: string;
  estado: string;
}

export interface ApiResponse<T = any> {
  status: string;
  success?: boolean; // Mantener para compatibilidad
  data?: T;
  message?: string;
  errors?: any;
  pagination?: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

export interface CategoriasResponse {
  categorias: Categoria[];
}

export interface PreguntasResponse {
  preguntas: Pregunta[];
  pagination: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

export interface FiltrosPreguntas {
  texto: string;
  categoria_id: string;
  dificultad: string;
  tipo: string;
  es_critica: string;
}
