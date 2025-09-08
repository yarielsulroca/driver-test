# 🚗 Instrucciones para Test de Creación de Examen B2

## Descripción del Test

Este test crea automáticamente un examen completo para la categoría B2 (Vehículos de Carga) con las siguientes características:

### 📋 **Características del Examen:**
- **60 preguntas** generadas por IA
- **4 respuestas** por pregunta (múltiple opción)
- **3 niveles de dificultad**: fácil, medio, difícil
- **Preguntas críticas** cada 5 preguntas
- **Puntaje variable** según dificultad (1-3 puntos)
- **Tiempo límite**: 90 minutos
- **Puntaje mínimo**: 70%

### 🎯 **Temas Cubiertos:**
1. **Normativa de tránsito** para vehículos de carga
2. **Señales de tránsito** específicas para camiones
3. **Límites de velocidad** para vehículos pesados
4. **Documentación requerida** para vehículos de carga
5. **Mecánica básica** de vehículos pesados
6. **Seguridad vial** en carreteras
7. **Mantenimiento preventivo**
8. **Carga y descarga segura**
9. **Emergencias en carretera**
10. **Legislación de transporte**

## 🚀 **Cómo Usar el Test**

### **Paso 1: Preparación**
1. Asegúrate de que el servidor backend esté corriendo
2. Verifica que la categoría B2 exista en la base de datos
3. Abre el archivo `test_crear_examen_b2_completo.html` en tu navegador

### **Paso 2: Ejecución Secuencial**
El test se ejecuta en 4 pasos secuenciales:

#### **Paso 1: Verificar Categoría B2**
- ✅ Busca automáticamente la categoría B2 en la base de datos
- ✅ Obtiene el ID de la categoría para usar en el examen
- ✅ Muestra información de la categoría encontrada

#### **Paso 2: Crear Examen Base**
- ✅ Crea el examen base con configuración completa
- ✅ Establece parámetros de tiempo, puntaje y fechas
- ✅ Asigna la categoría B2 al examen

#### **Paso 3: Generar Preguntas con IA**
- ✅ Genera 60 preguntas usando IA simulada
- ✅ Crea 4 respuestas por pregunta
- ✅ Asigna dificultades y puntajes automáticamente
- ✅ Marca preguntas críticas cada 5 preguntas
- ✅ Muestra progreso en tiempo real

#### **Paso 4: Verificar Examen Completo**
- ✅ Verifica que el examen se haya creado correctamente
- ✅ Muestra estadísticas completas del examen
- ✅ Confirma que todas las preguntas y respuestas estén en la BD

## 📊 **Estadísticas que Genera**

El test genera las siguientes estadísticas:

- **Total de Preguntas**: 60
- **Preguntas Críticas**: 12 (cada 5 preguntas)
- **Dificultad Fácil**: ~20 preguntas
- **Dificultad Media**: ~20 preguntas  
- **Dificultad Difícil**: ~20 preguntas
- **Puntaje Total**: Variable según dificultad

## 🔧 **Requisitos del Sistema**

### **Backend (CodeIgniter)**
- ✅ Endpoint `/api/categorias` funcionando
- ✅ Endpoint `/api/examenes` funcionando
- ✅ Endpoint `/api/preguntas` funcionando
- ✅ Endpoint `/api/respuestas` funcionando
- ✅ Categoría B2 existente en la base de datos

### **Base de Datos**
- ✅ Tabla `categorias` con categoría B2
- ✅ Tabla `examenes` configurada
- ✅ Tabla `preguntas` configurada
- ✅ Tabla `respuestas` configurada

### **Frontend**
- ✅ Proxy de Angular configurado
- ✅ CORS habilitado
- ✅ Conexión al backend funcionando

## 🛠️ **Solución de Problemas**

### **Error: "Categoría B2 no encontrada"**
```bash
# Verificar que la categoría B2 exista
php spark db:query "SELECT * FROM categorias WHERE codigo = 'B2' OR sigla = 'B2'"

# Si no existe, ejecutar el seeder
php spark db:seed CategoriasSeeder
```

### **Error: "Endpoint no encontrado"**
```bash
# Verificar rutas disponibles
php spark route:list

# Verificar que los controladores existan
ls app/Controllers/ExamenController.php
ls app/Controllers/PreguntaController.php
```

### **Error: "Error de CORS"**
- Verificar configuración del filtro CORS
- Revisar headers en `.htaccess`
- Confirmar que el proxy de Angular esté configurado

### **Error: "Error de base de datos"**
```bash
# Verificar conexión a la base de datos
php spark db:show_tables

# Ejecutar migraciones si es necesario
php spark migrate
```

## 📝 **Estructura de Datos Creada**

### **Examen Creado:**
```json
{
  "titulo": "Examen Teórico Categoría B2 - Vehículos de Carga",
  "nombre": "Examen B2 Completo",
  "descripcion": "Examen teórico completo para la obtención de licencia de conducir categoría B2...",
  "tiempo_limite": 90,
  "duracion_minutos": 90,
  "puntaje_minimo": 70,
  "numero_preguntas": 60,
  "estado": "activo",
  "categoria_id": "[ID_CATEGORIA_B2]"
}
```

### **Preguntas Generadas:**
- **60 preguntas** con enunciados variados
- **Tipo**: múltiple opción y única respuesta
- **Dificultad**: distribuida entre fácil, medio y difícil
- **Puntaje**: 1-3 puntos según dificultad
- **Críticas**: 12 preguntas marcadas como críticas

### **Respuestas Creadas:**
- **240 respuestas** totales (4 por pregunta)
- **60 correctas** (1 por pregunta)
- **180 incorrectas** (3 por pregunta)

## 🎯 **Resultado Esperado**

Al finalizar el test exitosamente, tendrás:

1. ✅ **1 examen** creado en la base de datos
2. ✅ **60 preguntas** asociadas al examen
3. ✅ **240 respuestas** (4 por pregunta)
4. ✅ **Estadísticas completas** del examen
5. ✅ **Examen listo** para ser usado por conductores

## 🔍 **Verificación Manual**

Para verificar manualmente que el examen se creó correctamente:

```bash
# Verificar el examen creado
php spark db:query "SELECT * FROM examenes WHERE titulo LIKE '%B2%' ORDER BY created_at DESC LIMIT 1"

# Verificar las preguntas
php spark db:query "SELECT COUNT(*) as total_preguntas FROM preguntas WHERE examen_id = [ID_EXAMEN]"

# Verificar las respuestas
php spark db:query "SELECT COUNT(*) as total_respuestas FROM respuestas r JOIN preguntas p ON r.pregunta_id = p.pregunta_id WHERE p.examen_id = [ID_EXAMEN]"
```

## 📞 **Soporte**

Si encuentras problemas:

1. Revisar los logs de CodeIgniter en `writable/logs/`
2. Verificar la consola del navegador (F12)
3. Confirmar que todos los endpoints estén funcionando
4. Verificar la configuración de la base de datos

---

**¡El examen estará listo para ser usado por los conductores que quieran obtener su licencia B2!** 🚛 