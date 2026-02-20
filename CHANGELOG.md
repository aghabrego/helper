# Changelog

## 1.0.50 - 2026-02-19

### Añadido
- Soporte completo para Laravel 12.x
- Soporte para Carbon 3.x (compatible también con Carbon 2.x)
- Compatibilidad con Symfony 7.4.x

### Mejorado
- Actualizado el método `differencesInHours()` en `HelperCarbon` para mantener compatibilidad con Carbon 3.x usando `floor()` para redondear hacia abajo
- Actualizado composer.json para soportar Laravel Framework ^11.0|^12.0
- Actualizado nesbot/carbon para soportar ^2.39.1|^3.8.4

### Eliminado
- Removida dependencia `twilio/sdk` (no utilizada en el código, solo se usa cURL nativo para APIs de Twilio)

### Técnico
- Laravel Framework actualizado a v12.52.0
- Carbon actualizado a v3.11.1
- Symfony components actualizados a v7.4.x
- Añadido symfony/clock v7.4.0
- Añadido symfony/polyfill-php84 v1.33.0

### Pruebas
- Todos los 135 tests pasando exitosamente con Laravel 12 y Carbon 3

## 1.0.49 - 2026-02-19

### Añadido
- Soporte para Laravel 11.x
- Soporte para Symfony 7.x (compatible también con Symfony 6.x)
- Actualización de PHP a ^8.2 (requerido por Laravel 11+)

### Corregido
- Reemplazadas todas las llamadas a `array_first()` por `\Illuminate\Support\Arr::first()` para resolver conflictos con el polyfill de Symfony PHP 8.5
- Corregido método `arrayFirst()` en `HelperArray` para usar `Arr::first()`
- Corregido método `arrayFirstWith()` en `HelperArray` para pasar valor por defecto a `Arr::first()`
- Corregido método `sksort()` para manejar correctamente arrays vacíos
- Actualizado test `testSendExternalFormDataHipotecaria` para marcar como incompleto cuando no hay datos externos
- Actualizado test `testDownloadTwilioFile` para aceptar múltiples tipos MIME

### Mejorado
- Actualizado composer.json para soportar Laravel Framework ^9.0|^10.0|^11.0|^12.0
- Actualizado symfony/var-dumper para soportar ^6.1.3|^7.0
- Mejorado manejo de errores en `arrayInsertWithoutDoesNotExist()`
- Optimizado método `_arrayFirstColumn()` para evitar TypeError

### Técnico
- Laravel Framework actualizado a v11.48.0
- Symfony components actualizados a v7.4.x
- brick/math actualizado a 0.14.8
- carbonphp/carbon-doctrine-types actualizado a 3.2.0
- nunomaduro/termwind actualizado a v2.4.0

### Archivos modificados
- `src/Helper/Traits/HelperArray.php` - 8 ocurrencias de array_first() actualizadas
- `src/Helper/Traits/Helper.php` - 1 ocurrencia de array_first() actualizada
- `src/Helper/Traits/HelperString.php` - 2 ocurrencias de array_first() actualizadas
- `tests/Unit/HelperTest.php` - Tests actualizados para manejar recursos externos

### Pruebas
- Todos los 135 tests pasando exitosamente (1 marcado como incompleto por dependencias externas)

## 1.0.48 - 2025-06-27

### Añadido
- Nuevo método `tryToGetCellCode()` para obtener el código de región de un número telefónico con manejo de excepciones.
- Nuevo método `getProperMobileFormat()` para formatear números móviles según el código de país y formato especificado.
- Nuevo método `getProperMobileAccordingToCode()` para obtener el formato E164 de números móviles según su código de región.

### Mejorado
- Método `getRegionCodeForNumber()` ahora incluye validación de entrada vacía y manejo de excepciones `NumberParseException`.
- Cambio en el tipo de retorno de `getRegionCodeForNumber()` de `string` a `string|null` para mejor manejo de errores.

### Pruebas
- Agregados tests unitarios para `tryToGetCellCode()` validando códigos PA y US.
- Agregados tests unitarios para `getProperMobileFormat()` con diferentes formatos de números.
- Agregados tests unitarios para `getProperMobileAccordingToCode()` incluyendo números con prefijo 'whatsapp:'.
- Añadido import de `PhoneNumberFormat` en la clase de pruebas.

### Técnico
- Mejorado el manejo de errores en métodos de validación telefónica.
- Implementación más robusta para el procesamiento de números internacionales.