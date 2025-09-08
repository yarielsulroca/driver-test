const https = require('https');
const http = require('http');

// Configuración
const API_BASE_URL = 'http://examen.test/api';
const ENDPOINT = '/examenes';

// Función para hacer petición HTTP
function hacerPeticion(url, data) {
    return new Promise((resolve, reject) => {
        const urlObj = new URL(url);
        const isHttps = urlObj.protocol === 'https:';
        const client = isHttps ? https : http;
        
        const postData = JSON.stringify(data);
        
        const options = {
            hostname: urlObj.hostname,
            port: urlObj.port || (isHttps ? 443 : 80),
            path: urlObj.pathname,
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Content-Length': Buffer.byteLength(postData),
                'Accept': 'application/json'
            }
        };

        console.log('🌐 Haciendo petición a:', url);
        console.log('📊 Datos enviados:', JSON.stringify(data, null, 2));

        const req = client.request(options, (res) => {
            let responseData = '';
            
            res.on('data', (chunk) => {
                responseData += chunk;
            });
            
            res.on('end', () => {
                console.log('📡 Status Code:', res.statusCode);
                console.log('📡 Headers:', res.headers);
                
                try {
                    const parsedData = JSON.parse(responseData);
                    resolve({
                        statusCode: res.statusCode,
                        data: parsedData,
                        raw: responseData
                    });
                } catch (e) {
                    resolve({
                        statusCode: res.statusCode,
                        data: responseData,
                        raw: responseData
                    });
                }
            });
        });

        req.on('error', (error) => {
            console.error('❌ Error en la petición:', error.message);
            reject(error);
        });

        req.write(postData);
        req.end();
    });
}

// Datos de prueba simplificados
const datosExamen = {
    "nombre": "Test Examen Backend",
    "descripcion": "Examen de prueba para verificar el backend",
    "categoria_id": 10,
    "categorias": [10],
    "preguntas": [
        {
            "categoria_id": "10",
            "enunciado": "¿Cuál es la velocidad máxima en zona urbana?",
            "tipo": "multiple",
            "dificultad": "medio",
            "puntaje": 10,
            "es_critica": false,
            "respuestas": [
                {
                    "texto": "40 km/h",
                    "es_correcta": false
                },
                {
                    "texto": "60 km/h",
                    "es_correcta": true
                },
                {
                    "texto": "80 km/h",
                    "es_correcta": false
                },
                {
                    "texto": "100 km/h",
                    "es_correcta": false
                }
            ]
        },
        {
            "categoria_id": "10",
            "enunciado": "¿Qué significa la señal de Pare?",
            "tipo": "multiple",
            "dificultad": "baja",
            "puntaje": 5,
            "es_critica": true,
            "respuestas": [
                {
                    "texto": "Detenerse completamente",
                    "es_correcta": true
                },
                {
                    "texto": "Reducir velocidad",
                    "es_correcta": false
                },
                {
                    "texto": "Ceder el paso",
                    "es_correcta": false
                },
                {
                    "texto": "Precaución",
                    "es_correcta": false
                }
            ]
        }
    ],
    "fecha_inicio": "2025-08-02T04:00:00.000Z",
    "fecha_fin": "2026-09-01T04:00:00.000Z",
    "duracion_minutos": 30,
    "puntaje_minimo": 70
};

async function testBackendReal() {
    console.log('🚀 Iniciando test real del backend...\n');
    
    try {
        const url = `${API_BASE_URL}${ENDPOINT}`;
        const resultado = await hacerPeticion(url, datosExamen);
        
        console.log('\n📋 RESULTADO DEL TEST:');
        console.log('=====================');
        console.log('Status Code:', resultado.statusCode);
        
        if (resultado.statusCode === 201 || resultado.statusCode === 200) {
            console.log('✅ ¡ÉXITO! El examen se creó correctamente');
            console.log('📊 Respuesta del servidor:', JSON.stringify(resultado.data, null, 2));
        } else {
            console.log('❌ ERROR: El examen no se pudo crear');
            console.log('📊 Respuesta del servidor:', JSON.stringify(resultado.data, null, 2));
        }
        
        return resultado;
        
    } catch (error) {
        console.error('💥 ERROR CRÍTICO:', error.message);
        return { error: error.message };
    }
}

// Ejecutar el test
testBackendReal().then(resultado => {
    console.log('\n🏁 Test completado');
    if (resultado.error) {
        console.log('❌ El backend NO está funcionando correctamente');
        process.exit(1);
    } else if (resultado.statusCode === 201 || resultado.statusCode === 200) {
        console.log('✅ El backend SÍ está funcionando correctamente');
        process.exit(0);
    } else {
        console.log('⚠️ El backend respondió pero con error');
        process.exit(1);
    }
}); 