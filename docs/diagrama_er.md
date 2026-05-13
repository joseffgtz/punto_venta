# Diagrama entidad-relación

El sistema utiliza una base de datos relacional con tres entidades principales:

- **users:** guarda administradores y clientes. El campo `role` separa permisos: `admin` puede agregar, editar y eliminar productos; `cliente` puede iniciar sesión y comprar.
- **products:** guarda los productos del punto de venta, su precio, stock e imagen.
- **sales:** guarda cada operación de venta/compra, relacionando el usuario que compró o vendió con el producto.

```mermaid
erDiagram
    USERS ||--o{ SALES : realiza
    PRODUCTS ||--o{ SALES : contiene

    USERS {
        bigint id PK
        varchar name
        varchar email UK
        varchar password
        enum role "admin o cliente"
        timestamp created_at
        timestamp updated_at
    }

    PRODUCTS {
        bigint id PK
        varchar name
        varchar description
        varchar brand
        decimal price
        int stock
        varchar image_path
        timestamp created_at
        timestamp updated_at
    }

    SALES {
        bigint id PK
        bigint product_id FK
        bigint user_id FK
        int quantity
        decimal unit_price
        decimal total
        timestamp created_at
        timestamp updated_at
    }
```
