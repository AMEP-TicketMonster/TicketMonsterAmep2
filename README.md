# Proyecto Grupo Amep

Instrucciones para preparar el entorno de trabajo

Dependencias:
- PHP 8.3.6
- Mariadb
- Composer 2.7.1

Instalación para un Debian/Ubuntu:

```
sudo apt update
sudo apt install php php-cli php-fpm php-mbstring php-xml php-mysql php-curl php-zip php-json php-common php-mysql php-cli mariadb-server mariadb-client composer

```
Configurar
Una vez instalado, en la carpeta del proyecto instalar composer:
```
composer install
composer init
composer dump-autoload
```
(en el caso de que no funcione el autoload, revisar el composer.json, tiene que tener la siguiente información):
```
{
    "name": "amep06/ticketmonster",
    "autoload": {
        "psr-4": {
            "App\\": "App/"
        }
    },
    "require": {}
}

```

Faltaría crear a mano un archivo `.env` el cual utilizaremos para guardar todos los credenciales de la base de datos. Este archivo solo lo crearemos y modificaremos una vez ya que está incluido en el .gitignore. Así que cada uno puede tener la conexión que quiera (ubiwan o local).

Una vez hecho eso instalamos el paquete:
```
composer require vlucas/phpdotenv
```

```
APP_NAME='TicketMonster'
APP_ENV=local
APP_DEBUG=true
DB_HOST='entre comillas la url de el host de la bd'
DB_PORT=3306
DB_DATABASE='entre comillas el nombre de la base de datos'
DB_USERNAME='... el nombre de la bd'
DB_PASSWORD='... la contraseña'

```






Ejecutar el servidor de forma local:
```
php -S localhost:8000
```

Desde el navegador, acceder a http://localhost:8000





