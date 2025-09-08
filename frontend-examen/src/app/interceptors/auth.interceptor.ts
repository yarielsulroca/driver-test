import { HttpInterceptorFn, HttpRequest, HttpHandlerFn } from '@angular/common/http';

export const AuthInterceptor: HttpInterceptorFn = (request: HttpRequest<any>, next: HttpHandlerFn) => {
  // SIN AUTENTICACIÓN: Interceptor simplificado que solo pasa las peticiones
  // No agrega tokens, no maneja refresh, no intercepta autenticación
  
  // Simplemente pasar la petición sin modificar
  return next(request);
}; 