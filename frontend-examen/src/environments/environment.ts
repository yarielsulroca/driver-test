// Función para obtener variables de entorno con valores por defecto
function getEnvVar(key: string, defaultValue: string): string {
  // En el navegador, las variables de entorno se pueden acceder a través de window.__env
  // o a través de process.env en el build
  if (typeof window !== 'undefined' && (window as any).__env) {
    return (window as any).__env[key] || defaultValue;
  }
  
  // Fallback para desarrollo
  return defaultValue;
}

export const environment = {
  production: false,
  apiUrl: getEnvVar('API_URL', '/api'),
  authUrl: getEnvVar('AUTH_URL', '/api/auth'),
  appName: getEnvVar('APP_NAME', 'Sistema de Exámenes'),
  version: getEnvVar('APP_VERSION', '1.0.0'),
  // Configuración de JWT
  jwtKey: getEnvVar('JWT_KEY', 'exam_token'),
  // Configuración de paginación
  pageSize: parseInt(getEnvVar('PAGE_SIZE', '10')),
  // Configuración de timeout
  requestTimeout: parseInt(getEnvVar('REQUEST_TIMEOUT', '30000')),
  // Configuración de CORS
  withCredentials: false
}; 