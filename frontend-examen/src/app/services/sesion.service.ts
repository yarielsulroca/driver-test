import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class SesionService {
  private API_URL = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  iniciarSesion(dni: string, token: string): Observable<boolean> {
    return this.http.post<{success: boolean}>(`${this.API_URL}/sesion/registrar`, { dni, token })
      .pipe(map(response => response.success));
  }

  verificarSesion(dni: string, token: string): Observable<boolean> {
    return this.http.post<{valid: boolean}>(`${this.API_URL}/sesion/verificar`, { dni, token })
      .pipe(map(response => response.valid));
  }

  cerrarSesion(dni: string): Observable<boolean> {
    return this.http.post<{success: boolean}>(`${this.API_URL}/sesion/cerrar`, { dni })
      .pipe(map(response => response.success));
  }
}
