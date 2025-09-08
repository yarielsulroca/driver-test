// Script para generar env.js desde archivos .env
const fs = require('fs');
const path = require('path');

// Configuración por defecto
const defaultConfig = {
  API_URL: '/api',
  AUTH_URL: '/api/auth',
  APP_NAME: 'Sistema de Exámenes',
  APP_VERSION: '1.0.0',
  JWT_KEY: 'exam_token',
  PAGE_SIZE: '10',
  REQUEST_TIMEOUT: '30000',
  WITH_CREDENTIALS: 'false'
};

// Función para leer archivo .env
function readEnvFile() {
  const envPath = path.join(__dirname, '..', '.env');
  const envExamplePath = path.join(__dirname, '..', '.env.example');
  
  let envContent = '';
  
  // Intentar leer .env, si no existe, usar .env.example
  if (fs.existsSync(envPath)) {
    envContent = fs.readFileSync(envPath, 'utf8');
  } else if (fs.existsSync(envExamplePath)) {
    envContent = fs.readFileSync(envExamplePath, 'utf8');
  }
  
  return envContent;
}

// Función para parsear variables de entorno
function parseEnvContent(content) {
  const env = {};
  
  content.split('\n').forEach(line => {
    line = line.trim();
    if (line && !line.startsWith('#')) {
      const [key, value] = line.split('=');
      if (key && value) {
        env[key.trim()] = value.trim();
      }
    }
  });
  
  return env;
}

// Función para generar el archivo env.js
function generateEnvJs(env) {
  const envJsContent = `// Archivo de configuración de variables de entorno
// Este archivo se genera automáticamente desde .env
window.__env = {
  // Configuración del API
  API_URL: '${env.API_URL || '/api'}',
  AUTH_URL: '${env.AUTH_URL || '/api/auth'}',
  
  // Configuración de la aplicación
  APP_NAME: '${env.APP_NAME || 'Sistema de Exámenes'}',
  APP_VERSION: '${env.APP_VERSION || '1.0.0'}',
  
  // Configuración de JWT
  JWT_KEY: '${env.JWT_KEY || 'exam_token'}',
  
  // Configuración de paginación
  PAGE_SIZE: '${env.PAGE_SIZE || '10'}',
  
  // Configuración de timeout
  REQUEST_TIMEOUT: '${env.REQUEST_TIMEOUT || '30000'}',
  
  // Configuración de CORS
  WITH_CREDENTIALS: '${env.WITH_CREDENTIALS || 'false'}'
};
`;

  const outputPath = path.join(__dirname, '..', 'src', 'assets', 'env.js');
  fs.writeFileSync(outputPath, envJsContent);
  console.log('✅ Archivo env.js generado exitosamente');
}

// Ejecutar el script
try {
  const envContent = readEnvFile();
  const env = parseEnvContent(envContent);
  generateEnvJs(env);
} catch (error) {
  console.error('❌ Error generando env.js:', error);
  process.exit(1);
} 