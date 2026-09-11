# serv_producmlm — catálogo de empresas y productos

Microservicio Laravel independiente. Fuente de verdad de **empresas MLM**, **catálogo**, **documentación**, **planes de compensación** y **herramientas de bienestar**. REXmlm sigue siendo el núcleo de red, comisiones SaaS y suscripciones.

## Arranque local

```bash
cd serv_producmlm
# PHP 8.2 de Laragon (el PATH suele apuntar a 8.1)
E:\laragon\bin\php\php-8.2.28-Win32-vs16-x64\php.exe artisan migrate
E:\laragon\bin\php\php-8.2.28-Win32-vs16-x64\php.exe artisan serve --port=8001
```

Base: `serv_producmlm` (MySQL). Caché y sesión en `file`; colas `sync`.

Cabecera obligatoria en todas las rutas `/api/v1`:

```
X-Service-Token: {SERVICE_TOKEN}
Accept: application/json
```

El mismo valor está en REXmlm como `PRODUCT_SERVICE_TOKEN`.

## Producción

- URL: `https://spxr.rexmlm.tech`
- Base: `Rex_microserv_bd` (la API usa `Rex_principal_bd`)
- Plantilla: `.env.production.example`
- REXmlm (`xeft.rexmlm.tech`) debe usar `PRODUCT_SERVICE_URL=https://spxr.rexmlm.tech/api/v1`
- El admin (`https://adxm.rexmlm.tech`) entra por REXmlm, no directo al catálogo
- Deploy: [`deploy/digitalocean.md`](../deploy/digitalocean.md) / [`deploy/hostinger.md`](../deploy/hostinger.md)

## Esquema

```
companies
  ├── categories
  ├── products          (ficha técnica en el propio producto)
  ├── documents         (pdf | image | video)
  ├── compensation_plans
  │     ├── compensation_ranks
  │     ├── compensation_packages   (pv, cost, image)
  │     └── compensation_bonuses
  ├── five_day_fundamentals
  ├── star_products
  ├── wellness_needs + wellness_need_items
  └── imc_packages + imc_package_items   (lose_weight | gain_weight)
```

| Tabla | Qué guarda |
| --- | --- |
| `companies` | Nombre, logo, paleta JSON, web oficial |
| `products` | Código, nombre, categoría, imagen, descripción, ficha técnica, precio |
| `documents` | Título + archivo (pdf, imagen o video) de la empresa |
| `compensation_plans` | Tipo de red + descripción, ejemplo y ventajas |
| `compensation_ranks` | Rangos del plan |
| `compensation_packages` | Paquetes con PV, costo e imagen |
| `compensation_bonuses` | Bonos con descripción, ejemplo y ventaja |
| `five_day_fundamentals` | Fundamentos del sistema de 5 días (título + descripción) |
| `star_products` | Productos estrella de la empresa |
| `wellness_needs` | Paquetes por necesidad o enfermedad |
| `imc_packages` | Paquetes para bajar (`lose_weight`) o subir (`gain_weight`) de peso |

Paleta de ejemplo: `{ "primary": "#1a1a2e", "secondary": "#e94560", "accent": "#0f3460" }`.

## API `/api/v1`

Middleware: `service.token` + `throttle:catalog` (120 req/min).

### Empresas

| Método | Ruta |
| --- | --- |
| GET | `/companies` |
| GET | `/companies/{id}` (incluye docs, plan, 5 días, estrellas, bienestar, IMC) |
| POST | `/companies` (`name`; opcionales `logo`, `color_palette`, `website`) |
| PUT | `/companies/{id}` |
| DELETE | `/companies/{id}` |

### Productos y categorías

| Método | Ruta | Notas |
| --- | --- | --- |
| GET/POST/PUT/DELETE | `/categories` | Filtro `company_id` |
| GET | `/products` | Filtros: `company_id`, `category_id`, `search`, `min_price`, `max_price`, `is_active` |
| GET/POST/PUT/DELETE | `/products`, `/products/{id}` | Alta: `company_id` + `code` + `name` |
| GET/POST | `/products/{productId}/technical-sheet` | Lee/escribe `products.technical_sheet` |
| PUT | `/technical-sheets/{id}` | `{id}` es el **product_id** |

### Documentación

| Método | Ruta |
| --- | --- |
| GET | `/companies/{companyId}/documents` (`file_type` opcional) |
| POST | `/companies/{companyId}/documents` |
| PUT/DELETE | `/documents/{id}` |

`file_type`: `pdf` \| `image` \| `video`. `file_path` es la ruta o URL del archivo.

### Plan de compensación

| Método | Ruta |
| --- | --- |
| GET | `/companies/{companyId}/compensation-plans` |
| POST | `/companies/{companyId}/compensation-plans` |
| PUT | `/compensation-plans/{id}` |

El POST/PUT acepta anidados `ranks`, `packages` (pv, cost, image) y `bonuses`. Si se envían, **reemplazan** los hijos.

### Herramientas de bienestar

| Método | Ruta | Uso |
| --- | --- | --- |
| GET/POST | `/companies/{companyId}/five-day-fundamentals` | Sistema de 5 días |
| PUT/DELETE | `/five-day-fundamentals/{id}` | |
| GET/POST | `/companies/{companyId}/star-products` | Productos estrella |
| PUT/DELETE | `/star-products/{id}` | |
| GET/POST | `/companies/{companyId}/wellness-needs` | Paquetes por necesidad/enfermedad |
| PUT/DELETE | `/wellness-needs/{id}` | |
| GET/POST | `/companies/{companyId}/imc-packages` | Filtro `goal` |
| PUT/DELETE | `/imc-packages/{id}` | |

`items` de bienestar e IMC: `{ product_id, quantity, notes }`. El producto debe ser de la misma empresa.

## Auth entre servicios

`VerifyServiceToken` compara `X-Service-Token` con `config('catalog.service_token')` (`hash_equals`). Token vacío o distinto → `401`.

## Integración REXmlm

| Pieza | Dónde |
| --- | --- |
| Cliente HTTP | `REXmlm/app/Services/Catalog/CatalogClient.php` |
| Env | `PRODUCT_SERVICE_URL`, `PRODUCT_SERVICE_TOKEN` |

La tienda `/s/:slug` **sigue usando** productos locales de REXmlm.
