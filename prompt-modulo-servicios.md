# Prompt para agente constructor: módulo Servicios (parte 2 de 2)

Ejecuta este prompt **después** de que el módulo Proyectos ya esté
construido — este reusa dos piezas creadas ahí: el enum
`App\Enums\ActiveStatus` y el trait
`App\Livewire\Concerns\WithGallery`. No los recrees ni los modifiques;
si notas que quedaron con algo específico de Project hardcodeado que te
impide reusarlos tal cual para Servicios, avísame antes de tocarlos.

Construye el módulo `Services` completo (modelo, migración, Livewire
Index + Form, Filter, Action, Policy, rutas, vista pública), replicando
la misma arquitectura que Blog y que ya usaste para Proyectos.

Servicio es un catálogo de oferta, no una ejecución puntual — **no
tiene** `client`, `location` ni `date`. No copies esos campos del
módulo de Proyectos.

## 1. Tabla y modelo `Service`

```
services
  id
  title
  title_seo (nullable) — override opcional del <title> SEO; fallback = title
  slug (unique)
  description (text)
  excerpt (nullable, string ~160) — tarjeta de listado + meta descripción;
    si vacío, fallback = Str::limit(strip_tags(description), 160)
  price (decimal nullable — listo para uso futuro, NO se muestra en el
         sitio público por ahora; no lo expongas en la vista pública de
         detalle/listado, pero sí déjalo editable en el form admin)
  status (string, cast a ActiveStatus, default 'active')
  featured (bool, default false)
  order (int, default 0)
  created_at / updated_at
  deleted_at (soft delete)
```

Modelo `App\Models\Service`: `use SoftDeletes`, casts (`status` =>
`ActiveStatus::class`), relación `images()` hacia `ServiceImage`
(hasMany, ordenada por `order`), método `coverImage()` igual que se hizo
en `Project`.

En el form, `title_seo` es opcional, con el mismo texto de ayuda usado
en `ProjectForm`.

## 2. Tabla y modelo `ServiceImage` (galería)

```
service_images
  id
  service_id (FK -> services, cascade on delete)
  image_path (string)
  order (int, default 0)
  is_cover (bool, default false)
  created_at / updated_at
```

Misma regla que Proyectos: como máximo una imagen con `is_cover = true`
por servicio. Usa el trait `WithGallery` ya existente sin modificarlo.

## 3. `App\Filters\ServiceFilter`

Mismo patrón que `ProjectFilter`: constructor con `search`, `status`,
`featured` (sin `location`, ya que Service no tiene ese campo), método
`apply(): Builder`. El componente Index usa el trait `WithFilters` ya
existente.

## 4. `App\Actions\Services\SaveService`

Mismo espíritu que `SaveProject`: transacción, crea/actualiza el
registro principal, sincroniza la galería.

## 5. Livewire: `ServicesIndex` y `ServiceForm`

Mismo patrón que `ProjectForm`. Recuerda: el campo `price` va en el
form admin pero **nunca** se renderiza en las vistas públicas.

## 6. `App\Policies\ServicePolicy`

Mismo esquema de roles que se decidió para `ProjectPolicy` en el prompt
anterior (confirma cuál quedó definido — si `Editor` tiene acceso a
Proyectos, aplica la misma regla aquí para mantener consistencia; si
`ProjectPolicy` restringió a solo `SuperAdmin`/`Admin`, replica eso).

## 7. Vista pública `services/{slug}`

Metadatos:
- `<title>`: `$service->title_seo ?: $service->title`
- meta descripción: `$service->excerpt ?: Str::limit(strip_tags($service->description), 160)`
- `og:image`: URL de la imagen con `is_cover = true`, si existe.
- **No renderices `price` en ningún punto de esta vista ni del
  listado `/services`.**

## Criterios de aceptación

- Migración corre limpio, con FK y cascade delete en `service_images`.
- `Service` no tiene columnas `client`, `location` ni `date` — confirma
  que no se copiaron por error desde el módulo de Proyectos.
- `price` se guarda y edita correctamente en el admin pero no aparece
  en ninguna vista pública (ni listado, ni detalle, ni metadatos).
- Guardar sin llenar `title_seo` ni `excerpt` no rompe nada; la parte
  pública usa los fallbacks correctamente.
- Marcar una imagen nueva como portada desmarca automáticamente la
  anterior.
- `ActiveStatus` y `WithGallery` se reusan tal cual del módulo de
  Proyectos, sin duplicarlos ni modificarlos.
- Ejecuta o añade tests de Feature: creación, edición, filtro por
  status/featured, autorización por rol, comportamiento de `is_cover`
  único, y una aserción explícita de que `price` no aparece en el HTML
  de la vista pública.
