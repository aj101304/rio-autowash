
RIO AUTOWASH - VERSION FINAL CON BUSCADOR DE PLACA - INFINITYFREE

INSTALACION:
1. InfinityFree > MySQL Databases > Crear BD
   Anota Host, DB Name, User, Pass
2. phpMyAdmin > Importar > database.sql
3. Editar config/database.php con tus 4 datos
4. Subir TODO a htdocs/ (que quede htdocs/index.php)

USO:
- Nueva: Escribe placa -> si ya existe te muestra cliente frecuente, puedes cargar vehículo y ver historial
- Elige vehículo -> solo aparecen servicios de ese vehículo
- Marca varios servicios -> se suman automático (ej: General + Adicional + Alfombra)
- Valor editable
- Hoy: Buscar por placa/ticket, Editar si hay error, Eliminar, Generar PDF
- PDF: Ticket, Placa, Vehículo, Servicios (todos), Valor, totales agrupados por vehículo y por servicio individual
- Resumen: Totales del día agrupados

LOGIN:
admin / admin123
cajera / cajera123
Cambia claves en BD con password_hash()

NOTA: api/buscar_placa.php es el buscador inteligente de placas frecuentes.
