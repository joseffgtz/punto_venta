# Punto de Venta UBM - Laravel

Proyecto web hecho en **PHP + Laravel + MySQL** para cumplir con el punto de venta solicitado.

## Funcionalidades incluidas

- Login con usuario y contraseña.
- Registro de nuevos usuarios clientes.
- Rol administrador y rol cliente.
- Cliente: puede iniciar sesión, consultar productos y comprar.
- Administrador: puede agregar, consultar, editar, eliminar y vender productos.
- Registro de imágenes de productos.
- Tabla de productos actualizada automáticamente con JavaScript/AJAX.
- ModalView para confirmar venta o compra.
- Base de datos relacional con tablas `users`, `products` y `sales`.
- Diagrama entidad-relación incluido en `/docs`.
- Archivo SQL incluido: `punto_venta_ubm.sql`.

## Usuarios de prueba

Administrador:

```txt
Correo: admin@gmail.com
Contraseña: admin123
```

Cliente:

```txt
Correo: cliente@gmail.com
Contraseña: cliente123
```

También puedes crear clientes nuevos desde la pantalla de registro.

## Instalación local en XAMPP

1. Copia la carpeta `Proyecto` dentro de:

```txt
C:\xampp\htdocs\
```

2. Abre CMD en la carpeta del proyecto:

```bash
cd C:\xampp\htdocs\Proyecto
```

3. Instala dependencias:

```bash
composer install
```

4. Crea la base de datos en phpMyAdmin:

```txt
punto_venta_ubm
```

5. Revisa el archivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=punto_venta_ubm
DB_USERNAME=root
DB_PASSWORD=
```

Si tu MySQL tiene contraseña, colócala en `DB_PASSWORD`.

6. Ejecuta:

```bash
php artisan optimize:clear
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

7. Abre:

```txt
http://127.0.0.1:8000
```

## Subir a Hostinger

1. Crea una base de datos MySQL desde Hostinger.
2. Sube todos los archivos del proyecto.
3. Configura el `.env` con los datos reales de Hostinger.
4. Ejecuta por SSH:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --seed --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

5. El dominio debe apuntar a la carpeta `public` de Laravel.

Si Hostinger compartido no permite apuntar directamente a `public`, deja el `.htaccess` del proyecto y conserva el archivo `.htaccess` de la raíz.

## GitHub solicitado por el profesor

Crear repositorio y ramas:

```bash
git init
git add .
git commit -m "Creación inicial del proyecto Laravel"
git branch development
git branch qa
git branch -M main
```

Después sube a GitHub:

```bash
git remote add origin URL_DE_TU_REPOSITORIO
git push -u origin main
git push -u origin development
git push -u origin qa
```
