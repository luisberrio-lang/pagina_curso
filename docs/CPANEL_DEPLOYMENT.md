# Preparación de producción en cPanel

El proyecto no requiere Terminal/SSH para servir imágenes públicas. Configure
`PUBLIC_FILESYSTEM_ROOT` con la ruta absoluta de `public_html/storage` y conceda
permisos de escritura a PHP. Copie allí las carpetas existentes de portadas y
muestras conservando sus rutas relativas. No es necesario ejecutar
`php artisan storage:link`.

## Almacenamiento público e imágenes históricas

Las rutas guardadas en la base de datos son relativas, por ejemplo
`courses/covers/archivo.webp`. El disco `public` agrega la URL `/storage` y
escribe directamente en una carpeta servida por el sitio:

- Local: deje `PUBLIC_FILESYSTEM_ROOT=` vacío; se usa `public/storage`.
- cPanel: configure la ruta absoluta de `public_html/storage`.

Para migrar archivos históricos sin Terminal, use File Manager y copie, sin
borrar ni sobrescribir archivos existentes:

```text
storage/courses/covers/*  -> public_html/storage/courses/covers/*
storage/courses/samples/* -> public_html/storage/courses/samples/*
```

Conserve los nombres y subcarpetas exactamente. Compruebe después una portada
y una muestra desde sus URLs HTTPS. En local existe el comando no destructivo
`php artisan media:migrate-legacy`; `--dry-run` permite revisar primero. El
comando nunca elimina el origen ni sobrescribe destinos existentes.

Permisos recomendados: carpetas `755` y archivos `644`, propiedad del usuario
de cPanel/PHP. Si el hosting usa escritura por grupo, pruebe `775` solo en las
carpetas de uploads. No use `777`. Bloquee la ejecución de scripts en la carpeta
de uploads desde la configuración del servidor cuando cPanel lo permita.

## Variables recomendadas

```env
APP_NAME="Cursos de Ingeniería Online"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://DOMINIO_REAL
APP_TIMEZONE=America/Lima

SHOP_CURRENCY=PEN
PUBLIC_FILESYSTEM_ROOT=/home/USUARIO/public_html/storage
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
IZIPAY_PAYMENTS_ENABLED=false
IZIPAY_ENVIRONMENT=sandbox
IZIPAY_MERCHANT_CODE=
IZIPAY_API_KEY=
IZIPAY_HASH_KEY=
IZIPAY_PUBLIC_KEY=
```

Complete los datos `BUSINESS_*` únicamente con información real.

Para recuperar contraseñas configure `MAIL_MAILER=smtp`, host, puerto, usuario,
contraseña, cifrado, remitente y nombre con los datos de su proveedor. No guarde
credenciales en Git. Tras cambiar `.env`, elimine únicamente
`bootstrap/cache/config.php` desde File Manager si existe. El envío SMTP real
debe validarse en el servidor con una cuenta autorizada.

## Servidor

Habilite HTTPS antes de activar HSTS. La CSP se pospone hasta conocer los
dominios oficiales del futuro SDK de Izipay para no bloquear recursos legítimos.

Antes de habilitar Izipay confirme certificado HTTPS válido, `APP_URL` con
`https://`, `SESSION_SECURE_COOKIE=true` y una URL pública HTTPS para el
webhook. No active HSTS hasta comprobar todo el sitio mediante HTTPS.

Izipay permanece cerrado mientras el flag esté apagado o falte una credencial.
El panel administrativo solo muestra si cada valor está configurado. Nunca
copie secretos a Blade, JavaScript, logs o Git. La generación del token oficial
de sesión se conectará en `IzipayService::start()` cuando Izipay entregue el
contrato definitivo; hasta entonces no existe pago real ni DCC.

El webhook conserva validación HMAC e idempotencia. No se agrega un throttle
por IP en aplicación porque los reintentos legítimos del proveedor no deben
depender de direcciones fijas; aplique protección de volumen en cPanel/WAF sin
bloquear reintentos firmados.

Extensiones PHP necesarias o recomendadas: PDO MySQL, mbstring, openssl,
fileinfo, GD y DOM. `intl` es recomendable, pero no bloquea el flujo actual.
