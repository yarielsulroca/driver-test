import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError, BehaviorSubject } from 'rxjs';
import { catchError, retry, tap } from 'rxjs/operators';
import { environment } from '../../environments/environment';
import { ApiResponse } from '../shared/interfaces';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private loadingSubject = new BehaviorSubject<boolean>(false);
  public loading$ = this.loadingSubject.asObservable();

  private errorSubject = new BehaviorSubject<string | null>(null);
  public error$ = this.errorSubject.asObservable();

  constructor(private http: HttpClient) {}

  private getHeaders(): HttpHeaders {
    // SIN AUTENTICACIÓN: Solo headers básicos
    let headers = new HttpHeaders({
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    });

    return headers;
  }

  private handleError(error: HttpErrorResponse): Observable<never> {
    let errorMessage = 'Ha ocurrido un error inesperado';

    if (error.error instanceof ErrorEvent) {
      // Error del cliente
      errorMessage = `Error: ${error.error.message}`;
    } else {
      // Error del servidor
      switch (error.status) {
        case 400:
          errorMessage = 'Solicitud incorrecta';
          break;
        case 401:
          errorMessage = 'No autorizado. Por favor, inicie sesión nuevamente.';
          // TEMPORALMENTE DESHABILITADO: No limpiar token
          // this.clearToken();
          break;
        case 403:
          errorMessage = 'Acceso denegado';
          break;
        case 404:
          errorMessage = 'Recurso no encontrado';
          break;
        case 422:
          errorMessage = error.error?.message || 'Datos de validación incorrectos';
          break;
        case 500:
          errorMessage = 'Error interno del servidor';
          break;
        default:
          errorMessage = `Error ${error.status}: ${error.message}`;
      }
    }

    this.errorSubject.next(errorMessage);
    return throwError(() => new Error(errorMessage));
  }

  private clearToken(): void {
    localStorage.removeItem(environment.jwtKey);
  }

  private setLoading(loading: boolean): void {
    this.loadingSubject.next(loading);
  }

  private clearError(): void {
    this.errorSubject.next(null);
  }

  // Métodos HTTP básicos
  get<T>(endpoint: string, params?: any): Observable<ApiResponse<T>> {
    this.setLoading(true);
    this.clearError();

    return this.http.get<ApiResponse<T>>(`${environment.apiUrl}${endpoint}`, {
      headers: this.getHeaders(),
      params,
      withCredentials: environment.withCredentials
    }).pipe(
      retry(1),
      tap(() => this.setLoading(false)),
      catchError((error) => {
        this.setLoading(false);
        return this.handleError(error);
      })
    );
  }

  post<T>(endpoint: string, data: any): Observable<ApiResponse<T>> {
    this.setLoading(true);
    this.clearError();

    return this.http.post<ApiResponse<T>>(`${environment.apiUrl}${endpoint}`, data, {
      headers: this.getHeaders(),
      withCredentials: environment.withCredentials
    }).pipe(
      tap(() => this.setLoading(false)),
      catchError((error) => {
        this.setLoading(false);
        return this.handleError(error);
      })
    );
  }

  put<T>(endpoint: string, data: any): Observable<ApiResponse<T>> {
    this.setLoading(true);
    this.clearError();

    return this.http.put<ApiResponse<T>>(`${environment.apiUrl}${endpoint}`, data, {
      headers: this.getHeaders(),
      withCredentials: environment.withCredentials
    }).pipe(
      tap(() => this.setLoading(false)),
      catchError((error) => {
        this.setLoading(false);
        return this.handleError(error);
      })
    );
  }

  delete<T>(endpoint: string): Observable<ApiResponse<T>> {
    this.setLoading(true);
    this.clearError();

    return this.http.delete<ApiResponse<T>>(`${environment.apiUrl}${endpoint}`, {
      headers: this.getHeaders(),
      withCredentials: environment.withCredentials
    }).pipe(
      tap(() => this.setLoading(false)),
      catchError((error) => {
        this.setLoading(false);
        return this.handleError(error);
      })
    );
  }

  // Métodos específicos para autenticación
  login(credentials: { email: string; password: string }): Observable<ApiResponse> {
    return this.http.post<ApiResponse>(`${environment.authUrl}/login`, credentials, {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }),
      withCredentials: environment.withCredentials
    }).pipe(
      catchError((error) => this.handleError(error))
    );
  }

  logout(): Observable<ApiResponse> {
    return this.http.post<ApiResponse>(`${environment.authUrl}/logout`, {}, {
      headers: this.getHeaders(),
      withCredentials: environment.withCredentials
    }).pipe(
      tap(() => this.clearToken()),
      catchError((error) => {
        this.clearToken();
        return this.handleError(error);
      })
    );
  }

  refreshToken(): Observable<ApiResponse> {
    return this.http.post<ApiResponse>(`${environment.authUrl}/refresh`, {}, {
      headers: this.getHeaders(),
      withCredentials: environment.withCredentials
    }).pipe(
      catchError((error) => this.handleError(error))
    );
  }

  // Método para subir archivos
  uploadFile<T>(endpoint: string, file: File, additionalData?: any): Observable<ApiResponse<T>> {
    this.setLoading(true);
    this.clearError();

    const formData = new FormData();
    formData.append('imagen', file); // Cambiar 'file' por 'imagen' para que coincida con el backend
    
    if (additionalData) {
      Object.keys(additionalData).forEach(key => {
        formData.append(key, additionalData[key]);
      });
    }

    // Logging temporal para debugging
    console.log('ApiService.uploadFile - Endpoint:', endpoint);
    console.log('ApiService.uploadFile - File:', file);
    console.log('ApiService.uploadFile - FormData entries:');
    for (let [key, value] of formData.entries()) {
      console.log('  ', key, ':', value);
    }
    console.log('ApiService.uploadFile - URL completa:', `${environment.apiUrl}${endpoint}`);

    // Usar el proxy normal para todas las peticiones
    const uploadUrl = `${environment.apiUrl}${endpoint}`;
    
    console.log('ApiService.uploadFile - URL final:', uploadUrl);

    // Para archivos, NO establecer Content-Type - el navegador lo hace automáticamente
    // Solo establecer Accept para JSON
    const headers = new HttpHeaders({
      'Accept': 'application/json'
    });

    console.log('ApiService.uploadFile - Headers:', headers);

    return this.http.post<ApiResponse<T>>(uploadUrl, formData, {
      headers,
      withCredentials: environment.withCredentials
    }).pipe(
      tap(() => this.setLoading(false)),
      catchError((error) => {
        this.setLoading(false);
        console.error('ApiService.uploadFile - Error:', error);
        return this.handleError(error);
      })
    );
  }

  /**
   * Subir imagen para una respuesta
   */
  async subirImagenRespuesta(file: File): Promise<any> {
    try {
      const formData = new FormData();
      formData.append('imagen', file);

      // SIN AUTENTICACIÓN: Usar el método uploadFile del servicio
      return await this.uploadFile('/preguntas/subir-imagen', file).toPromise();
    } catch (error: any) {
      console.error('Error al subir imagen:', error);
      throw error;
    }
  }

  /**
   * Obtener URL de imagen de respuesta
   */
  getImagenRespuestaUrl(nombreArchivo: string): string {
    return `${environment.apiUrl}/preguntas/imagen/${nombreArchivo}`;
  }
} 