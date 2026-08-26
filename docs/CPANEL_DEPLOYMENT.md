# Preparación de producción en cPanel

El proyecto no requiere Terminal/SSH para servir imágenes públicas. Configure
`PUBLIC_FILESYSTEM_ROOT` con la ruta absoluta de `public_html/storage` y conceda
permisos de escritura a PHP. Copie allí las carpetas existentes de portadas y
muestras conservando sus rutas relativas. No es necesario ejecutar
`php artisan storage:link`.

## Variables recomendadas

```env
APP_NAME="Cursos de Ingeniería Online"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://DOMINIO_REAL
APP_TIMEZONE=America/Lima

SHOP_CURRENCY=PEN
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
IZIPAY_PAYMENTS_ENABLED=false
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

Extensiones PHP necesarias o recomendadas: PDO MySQL, mbstring, openssl,
fileinfo, GD y DOM. `intl` es recomendable, pero no bloquea el flujo actual.
