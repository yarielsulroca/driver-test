# 🌱 Seeders de Categorías de Licencias de Conducción

Este proyecto incluye seeders completos para poblar la tabla de categorías con todas las licencias de conducción según la ley argentina.

## 📋 Categorías Incluidas

### 🏍️ Motocicletas
- **A1**: Motos hasta 150cc (edad mínima: 16 años)
- **A2**: Motos hasta 300cc (edad mínima: 17 años, requiere A1 por 1 año)
- **A3**: Motos sin límite de cilindrada (edad mínima: 18 años, requiere A2 por 1 año)

### 🚗 Automóviles Particulares
- **B1**: Automóviles particulares hasta 3500 kg (edad mínima: 17 años)
- **B2**: Automóviles y camiones livianos hasta 3500 kg (edad mínima: 18 años, requiere B1 por 1 año)

### 🚛 Vehículos de Carga
- **C1**: Camiones medianos 3500-8000 kg (edad mínima: 21 años, requiere B2 por 2 años)
- **C2**: Camiones pesados más de 8000 kg (edad mínima: 21 años, requiere C1 por 2 años)
- **C3**: Camiones con acoplado (edad mínima: 21 años, requiere C2 por 2 años)

### 🚌 Vehículos de Pasajeros
- **D1**: Ómnibus medianos hasta 20 asientos (edad mínima: 21 años, requiere B2 por 2 años)
- **D2**: Ómnibus grandes más de 20 asientos (edad mínima: 21 años, requiere D1 por 2 años)
- **D3**: Ómnibus con acoplado (edad mínima: 21 años, requiere D2 por 2 años)

### 🚜 Maquinaria Especial
- **E1**: Tractores agrícolas (edad mínima: 16 años)
- **E2**: Maquinaria vial y construcción (edad mínima: 18 años, requiere B1 por 1 año)

### ♿ Vehículos Especiales
- **F**: Vehículos para discapacitados (edad mínima: 17 años, evaluación especial)

### 🚛 Transporte Profesional
- **G1**: Transporte de carga profesional (edad mínima: 21 años, requiere C2 por 3 años)
- **G2**: Transporte de pasajeros profesional (edad mínima: 21 años, requiere D2 por 3 años)
- **G3**: Transporte de sustancias peligrosas (edad mínima: 23 años, requiere G1/G2 por 2 años)

### ⏰ Licencias Temporales
- **T1**: Licencia temporal de aprendizaje (edad mínima: 16 años, válida 1 año)
- **T2**: Licencia temporal de prueba (edad mínima: 17 años, válida 6 meses)

## 🚀 Formas de Ejecutar los Seeders

### Opción 1: Script PHP Directo (Recomendado)
```bash
# Desde la raíz del proyecto
php seed_categorias.php
```

### Opción 2: Comando CLI de CodeIgniter
```bash
# Usando el comando estándar
php spark db:seed CategoriasSeeder

# O usando el comando personalizado
php spark seed:categorias
```

### Opción 3: Desde el DatabaseSeeder Principal
```bash
# Ejecutar todos los seeders
php spark db:seed DatabaseSeeder
```

## 📊 Estructura de la Tabla

La tabla `categorias` debe tener estos campos:
- `categoria_id` (INT, autoincrement)
- `codigo` (VARCHAR 10, único)
- `nombre` (VARCHAR 50, único)
- `descripcion` (VARCHAR 255)
- `requisitos` (TEXT)
- `estado` (ENUM: 'activo', 'inactivo')
- `created_at` (DATETIME)
- `updated_at` (DATETIME)

## ⚠️ Consideraciones Importantes

1. **Antes de ejecutar**: Asegúrate de que las migraciones estén ejecutadas
2. **Base de datos**: Verifica que la conexión esté configurada correctamente
3. **Duplicados**: El script maneja automáticamente registros duplicados
4. **Backup**: Es recomendable hacer un backup antes de ejecutar

## 🔧 Configuración de Base de Datos

Si necesitas cambiar la configuración, edita estas líneas en `seed_categorias.php`:

```php
$host = 'localhost';      // Host de la base de datos
$dbname = 'examen';       // Nombre de la base de datos
$username = 'root';       // Usuario de la base de datos
$password = '';           // Contraseña de la base de datos
```

## 📝 Logs y Verificación

El script mostrará:
- ✅ Categorías insertadas exitosamente
- ⚠️ Categorías que ya existen
- ❌ Errores durante la inserción
- 📊 Resumen final con totales

## 🆘 Solución de Problemas

### Error: "Tabla no existe"
```bash
# Ejecutar las migraciones primero
php spark migrate
```

### Error: "Duplicate entry"
- El script maneja esto automáticamente
- Solo se insertan las categorías que no existen

### Error de conexión
- Verifica que MySQL esté ejecutándose
- Confirma las credenciales de la base de datos
- Asegúrate de que la base de datos `examen` exista

## 📞 Soporte

Si encuentras algún problema:
1. Revisa los logs de error
2. Verifica la estructura de la tabla
3. Confirma la configuración de la base de datos
4. Ejecuta las migraciones si es necesario

---

**Nota**: Este seeder está basado en la legislación argentina vigente para licencias de conducción. Las edades mínimas y requisitos pueden variar según la jurisdicción específica.
