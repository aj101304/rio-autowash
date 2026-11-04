# Rio Autowash - Paquete Render (sin pantalla roja)

## Que incluye este paquete:
- index.php con Lavado Camión Grande 110k y 130k (solo 1 chip con 2 opciones)
- Alerta de placa duplicada en el mismo día con # de ticket
- Liquidar pista con detalle de placas y tickets
- Usuario Yanet (yanet / yanet1) solo consulta + liquidar
- Hora Colombia (America/Bogota)
- Control para bloquear a paola

## Como desplegar en Render (gratis):

1. Crea cuenta en https://render.com
2. New + > Web Service > Connect GitHub o Upload
   - Si subes ZIP: descomprime y sube a GitHub, o usa el botón "Deploy from public Git"
3. Selecciona Docker, Plan Free
4. En Environment Variables agrega:
   DB_HOST = sql109.infinityfree.com
   DB_NAME = if0_43239394_ventas_rio
   DB_USER = if0_43239394
   DB_PASS = ventaspaola10 (tu clave actual)

5. Deploy y en 2 minutos te da link tipo https://rio-autowash.onrender.com

## Migrar base de datos (cuando quieras salir de InfinityFree):

1. En Render crea una base MySQL gratis en https://railway.app o https://clever-cloud.com
2. Importa el Ventas.txt (renombrado a .sql) que ya tienes
3. Cambia las variables DB_HOST, DB_NAME, etc en Render

## Usuarios actuales:
- admin / admin123 (o tu clave actual $2y$12$...)
- paola / paolita8
- yanet / yanet1 (creada con crear_yanet.php)

Soporte: Revisa que api/check_placa.php exista en /api/
