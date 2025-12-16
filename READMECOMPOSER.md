# Gestión de Dependencias (Composer)

Este proyecto utiliza **Composer** para gestionar las librerías externas (como Mercado Pago, Cloudinary, etc.).

La carpeta `vendor/` **NO** se incluye en el control de versiones (Git) porque es pesada y se debe generar automáticamente en cada entorno.

## ¿Cómo instalar las dependencias en otra PC?

Si clonas este repositorio en otra computadora, la carpeta `vendor` no aparecerá. Para generarla y descargar todas las librerías necesarias, sigue estos pasos:

1.  Asegúrate de tener **Composer** instalado en tu sistema.
    *   Para verificar, ejecuta en tu terminal: `composer --version`
    *   Si no lo tienes, descárgalo aquí: [https://getcomposer.org/download/](https://getcomposer.org/download/)

2.  Abre una terminal en la carpeta raíz del proyecto (donde está el archivo `composer.json`).

3.  Ejecuta el siguiente comando:
    ```bash
    composer install
    ```

Este comando leerá el archivo `composer.json` (y `composer.lock` si existe localmente) y descargará todas las librerías necesarias en la carpeta `vendor`.

## Notas Importantes

*   **Siempre** ejecuta `composer install` después de descargar el proyecto por primera vez.
*   Si agregas nuevas dependencias en el futuro con `composer require nombre-libreria`, recuerda que tus compañeros deberán ejecutar `composer install` (o `composer update`) para tenerlas también.
