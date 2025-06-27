# Changelog

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