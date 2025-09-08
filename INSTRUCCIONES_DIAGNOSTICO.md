# 🔍 Instrucciones de Diagnóstico - Problema con Creación de Examen

## Problema Identificado

El examen no se está creando. Vamos a diagnosticar paso a paso para encontrar la causa raíz.

## 🛠️ **Pasos de Diagnóstico**

### **Paso 1: Ejecutar Test de Diagnóstico**

1. **Abrir** `test_diagnostico_simple.html` en tu navegador
2. **Ejecutar** cada test en orden:
   - Test 1: Verificar Categorías
   - Test 2: Verificar Exámenes  
   - Test 3: Verificar Preguntas
   - Test 4: Verificar Respuestas
   - Test 5: Crear Examen Simple

### **Paso 2: Verificar Logs del Sistema**

```bash
# Verificar logs de CodeIgniter
tail -f writable/logs/log-$(date +%Y-%m-%d).log

# Verificar logs de CORS
tail -f writable/cors_simple.log
```

### **Paso 3: Verificar Base de Datos**

```bash
# Verificar que las tablas existan
php spark db:show_tables

# Verificar categoría B2
php spark db:query "SELECT * FROM categorias WHERE codigo = 'B2' OR sigla = 'B2'"

# Verificar estructura de tabla examenes
php spark db:query "DESCRIBE examenes"

# Verificar estructura de tabla preguntas  
php spark db:query "DESCRIBE preguntas"

# Verificar estructura de tabla respuestas
php spark db:query "DESCRIBE respuestas"
```

### **Paso 4: Verificar Rutas**

```bash
# Listar todas las rutas disponibles
php spark route:list | grep -E "(examenes|preguntas|respuestas|categorias)"
```

## 🔧 **Soluciones por Problema**

### **Problema 1: "Categoría B2 no encontrada"**

**Síntomas:**
- Error en Test 1 del diagnóstico
- Mensaje: "Categoría B2 no encontrada"

**Solución:**
```bash
# Ejecutar seeder de categorías
php spark db:seed CategoriasSeeder

# Verificar que se creó
php spark db:query "SELECT * FROM categorias WHERE codigo = 'B2'"
```

### **Problema 2: "Endpoint no encontrado"**

**Síntomas:**
- Error 404 en cualquier test
- Mensaje: "HTTP error! status: 404"

**Solución:**
```bash
# Verificar que los controladores existan
ls app/Controllers/ExamenController.php
ls app/Controllers/PreguntaController.php  
ls app/Controllers/RespuestaController.php
ls app/Controllers/CategoriaController.php

# Si falta RespuestaController, ya lo creamos
# Verificar rutas
php spark route:list
```

### **Problema 3: "Error de validación"**

**Síntomas:**
- Error 400 en Test 5
- Mensaje con errores de validación

**Solución:**
```bash
# Verificar estructura de tabla examenes
php spark db:query "DESCRIBE examenes"

# Verificar que los campos requeridos existan
php spark db:query "SHOW COLUMNS FROM examenes LIKE 'categoria_id'"
```

### **Problema 4: "Error de CORS"**

**Síntomas:**
- Error en consola del navegador
- Mensaje: "Access to fetch at... has been blocked by CORS policy"

**Solución:**
```bash
# Verificar filtro CORS
cat app/Config/Filters.php | grep -A 5 -B 5 "cors"

# Verificar .htaccess
cat public/.htaccess | grep -i "header"
```

### **Problema 5: "Error de base de datos"**

**Síntomas:**
- Error 500 interno del servidor
- Mensaje de error relacionado con SQL

**Solución:**
```bash
# Verificar conexión a la base de datos
php spark db:show_tables

# Verificar configuración
cat app/Config/Database.php | grep -A 10 "default"

# Ejecutar migraciones si es necesario
php spark migrate
```

## 📊 **Resultados Esperados del Diagnóstico**

### **Test Exitoso:**
- ✅ **Test 1**: Debe mostrar categorías (incluyendo B2)
- ✅ **Test 2**: Debe mostrar exámenes existentes (puede estar vacío)
- ✅ **Test 3**: Debe mostrar preguntas existentes (puede estar vacío)
- ✅ **Test 4**: Debe mostrar respuestas existentes (puede estar vacío)
- ✅ **Test 5**: Debe crear un examen de prueba exitosamente

### **Si Todos los Tests Pasan:**
El problema está en el test principal. Proceder con:
1. Abrir `test_crear_examen_b2_completo.html`
2. Ejecutar paso a paso
3. Revisar errores específicos

### **Si Algún Test Falla:**
Seguir las soluciones específicas arriba.

## 🔍 **Verificación Manual**

### **Verificar que el examen se creó:**
```bash
# Buscar el examen creado
php spark db:query "SELECT * FROM examenes WHERE titulo LIKE '%B2%' ORDER BY created_at DESC LIMIT 1"

# Verificar preguntas del examen
php spark db:query "SELECT COUNT(*) as total FROM preguntas WHERE examen_id = [ID_EXAMEN]"

# Verificar respuestas
php spark db:query "SELECT COUNT(*) as total FROM respuestas r JOIN preguntas p ON r.pregunta_id = p.pregunta_id WHERE p.examen_id = [ID_EXAMEN]"
```

## 📞 **Siguiente Paso**

Una vez que el diagnóstico esté completo:

1. **Si todos los tests pasan**: Usar el test principal
2. **Si hay errores**: Aplicar las soluciones específicas
3. **Si persisten problemas**: Revisar logs detallados

---

**¡El diagnóstico nos ayudará a identificar exactamente dónde está el problema!** 🔍 