## Importador de Contactos

Esta aplicacion está desarrollada en Laravel, con base de datos en MySQL con el objetivo de cumplir prueba técnica para Koombea, con las especificaciones descritas en el documento ubicado en la raíz Prueba_Tecnica.pdf. 
## Instalación

- Clonar el repositorio y dentro de la carpeta del proyecto ejecutar:

    composer install
    npm install

- Una vez ha finalizado la carga de dependencias, realizaremos la configuración ejecutando los comandos:
    cp .env.example .env
    php artisan key:generate

- Cuando hemos terminado la configuración, creamos una base de datos llamada "import_contacts", y ejecutaremos los comandos:
    php artisan migrate
    php artisan db:seed

- Ya podemos ingresar a la aplicacion entrando en la url de nuestro servidor y utilizando las credenciales:
    user: usertest@test.com
    password: koombea

- En la carpeta csv encontraran un archivo csv de prueba

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
