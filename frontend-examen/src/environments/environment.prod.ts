// Función para obtener variables de entorno con valores por defecto
function getEnvVar(key: string, defaultValue: string): string {
  // En el navegador, las variables de entorno se pueden acceder a través de window.__env
  // o a través de process.env en el build
  if (typeof window !== 'undefined' && (window as any).__env) {
    return (window as any).__env[key] || defaultValue;
  }
  
  // Fallback para producción
  return defaultValue;
}

export const environment = {
  production: true,
  apiUrl: 'https://examen.test/api',
  authUrl: 'https://examen.test/api/auth',
  appName: 'Sistema de Exámenes',
  version: '1.0.0',
  // Configuración de JWT
  jwtKey: 'exam_token',
  // Configuración de paginación
  pageSize: 10,
  // Configuración de timeout
  requestTimeout: 30000,
  // Configuración de CORS
  withCredentials: true
}; 