# Tax & Fees Report Module

Este módulo permite generar reportes detallados de todos los impuestos y fees aplicados a las ventas.

## Archivos del módulo:

### 1. `index.php`
- Vista principal del reporte mensual
- Filtros por mes y año
- Resumen de impuestos en cards
- Tabla detallada de todos los impuestos
- Gráfico de distribución de fees
- Botón de exportación a Excel

### 2. `annualComparison.php`
- Vista de comparación anual
- Gráfico de tendencias mensuales
- Tabla comparativa mes a mes
- Estadísticas anuales

### 3. `getFeesData.php`
- API para obtener datos detallados de impuestos
- Consulta combinada de tablas `sell` y `sell_summary`
- Evita duplicación de final_fee y fixed_charge por sell_order

### 4. `getFeesSummary.php`
- API para obtener resumen de impuestos
- Cálculos de totales por categoría
- Datos para gráficos

### 5. `getAnnualComparison.php`
- API para datos de comparación anual
- Datos mes a mes para el año seleccionado
- Estadísticas anuales

### 6. `exportFeesExcel.php`
- Exportación de datos a Excel
- Formato tabular con totales
- Headers optimizados para Excel

### 7. `feesReport.js`
- Funciones JavaScript para la interfaz
- Manejo de AJAX
- Inicialización de gráficos
- Funciones de formato

## Tipos de impuestos y fees incluidos:

### Por Item (tabla `sell`):
- `tax` - Impuesto general
- `withheld_tax` - Impuesto retenido
- `international_fee` - Fee internacional
- `ad_fee` - Fee de publicidad
- `other_fee` - Otros fees

### Por Orden (tabla `sell_summary`):
- `final_fee` - Fee final
- `fixed_charge` - Cargo fijo

## Características principales:

1. **Filtrado por fechas**: Búsqueda por mes y año
2. **Resumen visual**: Cards con totales y gráficos
3. **Exportación**: Descarga de datos en Excel
4. **Comparación anual**: Vista de tendencias mensuales
5. **Evita duplicación**: Final fees y fixed charges se cuentan una sola vez por sell_order

## Consultas principales:

El módulo utiliza consultas que combinan:
- Tabla `sell`: Para impuestos por item individual
- Tabla `sell_summary`: Para fees por orden de venta
- Tabla `store`: Para nombres de tiendas
- Tabla `sucursal`: Para códigos de sucursales

## Uso:

1. Acceder a `/code/fees/index.php` para vista mensual
2. Seleccionar mes y año deseados
3. Ver resumen en cards y tabla detallada
4. Exportar a Excel si se requiere
5. Usar `/code/fees/annualComparison.php` para vista anual

## Permisos:

- Requiere sesión activa
- Acceso controlado por `$_SESSION['id']`
- Todos los archivos PHP verifican autenticación
