# TEST PROGRAMACIÓN BACKEND

## Tabla de contenidos

- [Setup](#Setup)
    - [Requisitos](#Requisitos)
    - [Instalación](#Instalación)
- [Prueba](#Prueba)
    - [Consideraciones](#Consideraciones)
    - [Explicación](#Explicación)
    - [Entrega](#Entrega)

## Setup

### Requisitos

- [Docker desktop](https://www.docker.com/products/docker-desktop)

### Instalación

1. Iniciar los contenedores con el siguiente comando:
    ```shell
    > (cd ./laradock && docker-compose up -d workspace nginx php-fpm mysql)
    ```
2. Para entrar al workspace ejecutar:
    ```shell
    > (cd ./laradock && docker-compose exec --user=laradock workspace bash)
    ```
3. Instalamos las dependencias:
    ```shell
    > composer install
    ```
4. Si vamos a [localhost example](http://localhost/api/example) debería estar funcionando devolviendo un uuid v4 random.

## PRUEBA

### Consideraciones

- Se valorara la implementación de un frontend con vue 3.
- Se valorará el uso de CQRS, DDD y arquitectura hexagonal, para ello se ha dejado en el namespace Hoyvoy (carpeta src) un ejemplo muy básico usado en el endpoint http://localhost/api/example pero se puede estructurar el código de la manera que consideres oportuna, tienes un ejemplo de como lo implementan en symfony en el repositorio https://github.com/CodelyTV/php-ddd-example.
- Los datos se pueden almacenar en cualquier formato ya sea base de datos, json...
- Evitar estar acoplados a la API de cambio de divisas [Fixer](https://fixer.io/documentation) o la que se utilice para poder cambiar de servicio lo más facil possible.

### Explicación

Necesitamos una API para tratar el tema de divisas. Para ello necesitamos:

- Actualizar cada hora las tasas de conversión entre divisas con alguna API externa, un ejemplo podría ser [Fixer](https://fixer.io/documentation) pero se puede utilizar cualquier otra y almacenar un histórico de los cambios.
- Cuando se actualize alguna divisa, enviar un email a `cambio@moneda.es` (puedes usar [mailtrap](https://mailtrap.io) o Mailhog que lo encontraras como contenedor de [laradock](https://laradock.io))

- 2 ENDPOINTS

  [GET] http://localhost/api/currencies 
    
  En este endpoint tenemos que poder ver todas las divisas y la respuesta debe ser:
  ```json
  {
      "data": [
        {
          "code": "EUR",
          "name": "Euro",
          "rate_USD": "1,06"
        },
        {
          "code": "USD",
          "name": "Dollar",
          "rate_USD": "1"
        }
        //.....
     ]
  }
  ```

  [GET] http://localhost/api/currencies/rate-conversion

    En este endpoint tenemos que enviar 3 parametros que són `from`, `to` y `amount`. El retorno debería ser:
    ```json
    {
      "data": {
        "from": "EUR",
        "to": "USD",
        "amount": 1,
        "result": 1.06
     }
    }
    ```

### Entrega

La entrega de la prueba será mediante la creación de un repositorio privado dando acceso a maguilar@hoyvoy.com (maguilar92).
