# 🧮 **Currencies DDD CQRS**

API desarrollada en **Laravel** aplicando **DDD**, **CQRS** y **arquitectura hexagonal**, que permite gestionar divisas,
actualizar tasas de conversión automáticamente y realizar intercambios entre monedas.

---

## **Características principales**

✅ **Comando Artisan** para actualizar tasas, nombres y códigos de divisas.  
✅ **Listado completo de currencies** con sus tasas frente al USD.  
✅ **Conversión de importes entre dos monedas** mediante endpoint REST.  
✅ **Histórico de cambios de tasas** almacenado en base de datos.  
✅ **Notificación por correo electrónico** cada vez que se actualiza una divisa.

---

## ⚙ **Instalación y ejecución**

### Requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)

### Pasos de instalación

```bash
# Levantar contenedores
(cd ./laradock && docker-compose up -d workspace nginx php-fpm mysql)

# Entrar en el contenedor
(cd ./laradock && docker-compose exec --user=laradock workspace bash)

# Instalar dependencias
composer install
```

## **Comando de actualización**

Ejecuta el siguiente comando para actualizar toda la información de las divisas:

```bash

php artisan currencies:update
```

---

### Este comando:

- Obtiene las tasas desde una API externa ( en este caso Fixer.io)
- Actualiza los códigos, nombres y tasas.
- Guarda el histórico de conversiones.
- Lanza un evento que envía un email a cambio@moneda.es (con Mailhog configurado en Laradock).

---

## **Endpoints disponibles**

### [GET] /api/currencies

Devuelve todas las divisas registradas.

Ejemplo de respuesta:

```bash

{
  "data": [
    { "code": "EUR", "name": "Euro", "rate_USD": "1.06" },
    { "code": "USD", "name": "Dollar", "rate_USD": "1" }
  ]
}
```

---

### [GET] /api/currencies/rate-conversion

Convierte un importe entre dos monedas.

Parámetros:

- from-> string
- to-> string
- amount-> integer

Ejemplo de respuesta:

```bash

{
  "data": {
    "from": "EUR",
    "to": "USD",
    "amount": 1,
    "result": 1.06
  }
}
```

---
