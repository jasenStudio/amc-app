# Prompt para agente constructor: módulo Proyectos (parte 1 de 2)

Este es el primero de dos prompts separados (Proyectos y luego Servicios)
para evitar que se mezclen campos entre las dos entidades al ejecutarlos
juntos. Construye el módulo `Projects` completo (modelo, migración,
Livewire Index + Form, Filter, Action, Policy, rutas, vista pública),
replicando la arquitectura ya establecida en Blog (PostFilter +
WithFilters, SavePost + trait de imagen, SlugGenerator reusado).

Este prompt también crea dos piezas **compartidas** que el siguiente
prompt (Servicios) va a reusar tal cual: el enum `ActiveStatus` y el
trait `WithGallery`. Constrúyelas de forma genérica, sin nada específico
de Proyectos hardcodeado.

Antes de escribir código, inspecciona los archivos ya existentes de Blog
(`PostFilter`, `WithFilters`, `PostForm`, `SavePost`, `SlugGenerator`,
`PostPolicy`) y revisa si ya hay algo empezado de Proyectos en el
proyecto (no asumas que partes de cero).

## 1. Enum `App\Enums\ActiveStatus` (compartido con Servicios)

Distinto de `PostStatus` a propósito — "borrador/publicado" es un
concepto editorial de Blog, no aplica idiomáticamente a un proyecto o
servicio, que están simplemente activos o inactivos:

```php
enum ActiveStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Activo',
            self::Inactive => 'Inactivo',
        };
    }
}
```

## 2. Tabla y modelo `Project`

```
projects
  id
  title
  title_seo (nullable) — override opcional del <title> SEO; fallback = title
  slug (unique)
  description (text)
  excerpt (nullable, string ~160) — tarjeta de listado + meta descripción;
    si vacío, fallback = Str::limit(strip_tags(description), 160)
  client (string)
  location (string, nullable si aplica)
  date (date — fecha del proyecto, distinta de created_at)
  status (string, cast a ActiveStatus, default 'active')
  featured (bool, default false)
  order (int, default 0)
  created_at / updated_at
  deleted_at (soft delete)
```

Modelo `App\Models\Project`: `use SoftDeletes`, casts (`status` =>
`ActiveStatus::class`, `date` => `date`), relación `images()` hacia
`ProjectImage` (hasMany, ordenada por `order`), y un accessor o método
`coverImage()` que devuelva la imagen con `is_cover = true` (usa el
mismo patrón que ya tenga `Post::coverImage()` si es una relación
`hasOne`, adaptado a filtrar por `is_cover`).

En el form, `title_seo` es opcional (no `required`), con texto de ayuda
tipo "Déjalo vacío para usar el título público".

## 3. Tabla y modelo `ProjectImage` (galería)

```
project_images
  id
  project_id (FK -> projects, cascade on delete)
  image_path (string)
  order (int, default 0)
  is_cover (bool, default false)
  created_at / updated_at
```

Al guardar/actualizar la galería, garantiza a nivel de Action que **como
máximo una** imagen por proyecto tenga `is_cover = true` (si el usuario
marca una nueva como portada, desmarca la anterior en la misma
transacción). La imagen `is_cover` sirve doble propósito: miniatura del
listado público Y `og:image` — no crees un campo separado para "imagen
SEO".

## 4. Trait de galería: `App\Livewire\Concerns\WithGallery` (compartido con Servicios)

Distinto de `WithCoverImage` (que es para una sola imagen tipo Blog).
Este trait maneja múltiples imágenes con reordenamiento y marca de
portada, y debe quedar genérico (sin nada específico de "Project" en su
código, para que Servicios lo reuse sin modificarlo):

```php
trait WithGallery
{
    /** @var array<int, array{path: string, order: int, is_cover: bool}> */
    public array $galleryImages = [];

    #[On('gallery-image-uploaded')]
    public function onGalleryImageUploaded(array $imageData): void { /* agrega a $galleryImages */ }

    public function removeGalleryImage(int $index): void { /* quita del array */ }

    public function setCoverImage(int $index): void { /* marca esa como is_cover=true, desmarca las demás */ }
}
```

Ajusta la firma exacta según cómo esté implementado el subida de
imágenes existente (`ImageUploadController`) — reusa ese mismo
controller/endpoint si ya sirve para esto, no crees uno paralelo.

## 5. `App\Filters\ProjectFilter`

Mismo patrón que `PostFilter`: constructor con `search`, `status`,
`featured`, y `location` si tiene sentido como filtro adicional, método
`apply(): Builder`. El componente Index usa el trait `WithFilters` ya
existente.

## 6. `App\Actions\Projects\SaveProject`

Mismo espíritu que `SavePost`: transacción, crea/actualiza el registro
principal, sincroniza la galería (upsert de imágenes, garantiza una sola
`is_cover`, elimina las removidas).

## 7. Livewire: `ProjectsIndex` y `ProjectForm`

Mismo patrón que Blog (`mount`, `rules()`, `save()`, `render()`,
autorización vía `ProjectPolicy`). `title_seo` y `excerpt` van en el
form como campos opcionales, agrupados visualmente bajo una sección
"SEO" si el Blade de Blog ya tiene ese patrón de agrupación visual
(revisa `post-form.blade.php` antes de decidir el layout).

## 8. `App\Policies\ProjectPolicy`

Sigue el mismo esquema de roles que `PostPolicy`: `SuperAdmin` y `Admin`
gestionan sin restricción. Antes de decidir si `Editor` también tiene
acceso a Proyectos, pregúntame — hoy `Editor` está limitado a Blog y no
está confirmado si debe extenderse a Proyectos/Servicios.

## 9. Vista pública `projects/{slug}`

Metadatos:
- `<title>`: `$project->title_seo ?: $project->title`
- meta descripción: `$project->excerpt ?: Str::limit(strip_tags($project->description), 160)`
- `og:image`: URL de la imagen con `is_cover = true`, si existe.

Sigue el mismo patrón de metadatos que ya use el Blog para su vista de
detalle (`blog/{slug}`), si existe.

## Criterios de aceptación

- Migración corre limpio, con FK y cascade delete en `project_images`.
- Guardar sin llenar `title_seo` ni `excerpt` no rompe nada; la parte
  pública usa los fallbacks correctamente.
- Marcar una imagen nueva como portada desmarca automáticamente la
  anterior — nunca hay dos `is_cover = true` en la misma galería.
- `ActiveStatus` y `WithGallery` quedan genéricos, sin nada específico
  de Project hardcodeado (el siguiente prompt de Servicios los reusa tal
  cual).
- Ejecuta o añade tests de Feature: creación, edición, filtro por
  status/featured, autorización por rol, y el comportamiento de
  `is_cover` único en la galería.
