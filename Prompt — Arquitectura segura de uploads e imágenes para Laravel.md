Necesito revisar e implementar una política segura y consistente para la subida y almacenamiento de imágenes en este proyecto Laravel.

El proyecto es un CMS pequeño para una empresa, con administración de:

- Proyectos / casos de éxito.
- Blog.
- Imágenes de portada.
- Imágenes insertadas dentro del contenido enriquecido mediante Tiptap.

La prioridad es **seguridad, mantenibilidad y una futura migración sencilla a almacenamiento externo**, sin introducir infraestructura innecesaria.

Antes de modificar código, inspecciona la implementación actual y determina:

1. Cómo se están almacenando actualmente las imágenes.
2. Qué disco de Laravel se está utilizando.
3. Qué endpoints/controladores manejan uploads.
4. Cómo se guardan las imágenes de proyectos.
5. Cómo se guarda la portada del blog.
6. Cómo funciona el endpoint usado por Tiptap para imágenes inline.
7. Qué campos de base de datos almacenan las rutas.
8. Si actualmente se utiliza `public/`, `storage/app/public`, Cloudinary u otro servicio.
9. Qué configuración de filesystem existe en `.env` y `config/filesystems.php`.
10. Qué servidor/configuración web utiliza el proyecto y si existe alguna regla para impedir ejecución de scripts dentro de directorios de uploads.

No hagas cambios hasta entender la implementación existente.

---

# Objetivo

Diseñar una estrategia de uploads que cumpla:

```text
Usuario
   │
   ▼
Validación estricta
   │
   ▼
Procesamiento de imagen
   │
   ├── validar tipo
   ├── validar tamaño
   ├── validar dimensiones
   ├── eliminar metadata innecesaria
   └── convertir a WebP
   │
   ▼
Generar nombre controlado por servidor
   │
   ▼
Storage de Laravel
   │
   ▼
Guardar únicamente la ruta en DB
```

Nunca confiar en:

- nombre original;
- extensión original;
- MIME declarado por el navegador;
- contenido proporcionado por el usuario para construir rutas;
- nombres proporcionados directamente por el cliente.

---

# 1. Almacenamiento

La solución inicial debe utilizar el filesystem abstraído de Laravel.

Preferencia:

```text
storage/app/public/
```

con:

```bash
php artisan storage:link
```

La estructura conceptual será:

```text
storage/
└── app/
    └── public/
        ├── projects/
        ├── blog/
        └── blog/content/
```

No guardar uploads directamente en:

```text
public/uploads/
```

si puede evitarse.

El acceso público debe realizarse mediante el mecanismo de Storage de Laravel, por ejemplo:

```php
Storage::disk('public')->url($path);
```

No construir manualmente rutas físicas del servidor.

---

# 2. Abstracción del filesystem

No acoplar la lógica de negocio a:

```text
storage/app/public
```

ni a rutas físicas.

Usar:

```php
Storage::disk(...)
```

o la abstracción equivalente que ya utilice el proyecto.

El objetivo es que posteriormente pueda cambiarse:

```env
FILESYSTEM_DISK=public
```

por:

```env
FILESYSTEM_DISK=s3
```

sin tener que reescribir los controladores.

No implementar S3 ahora.

No agregar AWS, Cloudinary, R2 ni otro proveedor si actualmente no es necesario.

---

# 3. Validación de uploads

Todos los endpoints de imágenes deben validar estrictamente los archivos.

Para imágenes utilizar una validación equivalente a:

```php
[
    'required',
    'image',
    'mimes:jpg,jpeg,png,webp',
    'max:2048',
    'dimensions:max_width=3000,max_height=3000',
]
```

Adapta las reglas a las necesidades reales del proyecto si ya existe una política centralizada.

Importante:

NO utilizar:

```php
$file->getClientOriginalExtension()
```

para decidir si un archivo es seguro.

NO utilizar:

```php
$file->getClientOriginalName()
```

como nombre de almacenamiento.

NO confiar en:

```text
Content-Type
```

enviado por el navegador como única validación.

La validación debe comprobar que el archivo es realmente una imagen.

---

# 4. Límite de tamaño

Mantener un límite razonable para este CMS.

La propuesta inicial es:

```text
2 MB
```

pero revisa los límites actuales del proyecto antes de fijarlo.

No modificar límites globales de PHP (`upload_max_filesize`, `post_max_size`) salvo que sea estrictamente necesario.

El objetivo es limitar el upload desde Laravel y evitar que un usuario pueda enviar archivos excesivamente grandes.

---

# 5. Límite de dimensiones

Las imágenes deben tener un límite de dimensiones para evitar imágenes extremadamente grandes que puedan consumir demasiada memoria durante el procesamiento.

Usar inicialmente:

```text
3000 x 3000 px
```

salvo que el análisis del proyecto justifique otro límite.

Ejemplo:

```php
'dimensions:max_width=3000,max_height=3000'
```

No permitir imágenes absurdamente grandes como:

```text
80000 x 80000
```

aunque el archivo comprimido pese poco.

---

# 6. Conversión a WebP

Evaluar si el proyecto ya tiene:

```text
Intervention Image
```

u otra librería de procesamiento de imágenes.

Si ya existe, reutilizarla.

Si no existe, instalar una solución estable y apropiada para Laravel, preferiblemente Intervention Image si es compatible con la versión actual del proyecto.

Flujo:

```text
JPG / JPEG / PNG / WebP
          │
          ▼
    validar imagen
          │
          ▼
  procesar con librería
          │
          ▼
       WebP
          │
          ▼
      Storage
```

Las imágenes finales deben almacenarse como:

```text
.webp
```

No conservar automáticamente el archivo original salvo que exista una necesidad explícita.

Beneficios buscados:

- menor tamaño;
- mejor rendimiento;
- formato uniforme;
- eliminación/reducción de metadata innecesaria;
- menor dependencia del archivo original proporcionado por el usuario.

Verifica que el procesamiento de la imagen no conserve innecesariamente metadata EXIF.

No aceptar SVG como imagen de usuario salvo que exista una necesidad específica y se implemente una sanitización adecuada. Por defecto, mantener:

```text
jpg
jpeg
png
webp
```

---

# 7. Nombres de archivos

No utilizar nombres proporcionados directamente por el usuario.

No guardar:

```text
cliente_subio_foto.jpg
```

ni:

```text
IMG_8392.jpg
```

ni utilizar directamente:

```php
$file->getClientOriginalName()
```

Tampoco utilizar UUID puro como nombre SEO:

```text
550e8400-e29b-41d4-a716-446655440000.webp
```

La estrategia preferida es:

```text
slug-semantico + identificador-corto.webp
```

Ejemplos:

```text
linea-vida-horizontal-planta-industrial-a82f91.webp

punto-anclaje-certificado-construccion-b71d20.webp

normativa-trabajo-seguro-alturas-colombia-c82f91.webp
```

Utilizar un identificador generado por el servidor para evitar colisiones.

Puede utilizarse:

```php
Str::random(8)
```

o un mecanismo equivalente.

El slug debe provenir de información controlada por la aplicación, por ejemplo:

```php
Str::slug($project->title)
```

o:

```php
Str::slug($post->title)
```

Nunca utilizar el nombre original del archivo como fuente del slug.

Si el contexto no tiene un título adecuado, utilizar un identificador controlado por el servidor.

---

# 8. Estructura de almacenamiento

Utilizar una estructura clara.

Propuesta:

```text
storage/app/public/
├── projects/
│   ├── linea-vida-horizontal-industrial-a82f91.webp
│   └── punto-anclaje-certificado-b71d20.webp
│
└── blog/
    ├── normativa-trabajo-alturas-colombia-c82f91.webp
    └── content/
        ├── instalacion-linea-vida-a72f91.webp
        └── inspeccion-anclajes-b82d91.webp
```

Si el proyecto ya tiene una estructura razonable, no la cambies innecesariamente.

Mantener separados:

```text
projects/
blog/
blog/content/
```

para evitar mezclar responsabilidades.

---

# 9. Base de datos

La base de datos debe almacenar únicamente la referencia al archivo, no el contenido binario.

Ejemplo:

```text
image_path
cover_image
```

debe contener:

```text
projects/linea-vida-horizontal-industrial-a82f91.webp
```

No:

```text
/storage/app/public/projects/...
```

No:

```text
https://dominio.com/storage/...
```

salvo que la arquitectura actual tenga una razón explícita para almacenar URLs absolutas.

Preferir rutas relativas al disk de Laravel.

La URL pública debe resolverse mediante:

```php
Storage::disk('public')->url($path)
```

---

# 10. Seguridad contra ejecución de código

Este punto es crítico.

La aplicación no debe permitir que un archivo subido por un usuario pueda convertirse en código ejecutable.

Aunque la validación impida `.php`, debe existir una defensa adicional a nivel del servidor web.

Revisar el entorno real del proyecto.

Si utiliza Apache, evaluar una regla adecuada para impedir ejecución de:

```text
.php
.php5
.phtml
```

dentro del directorio público de uploads.

Si utiliza Nginx, verificar que los archivos bajo el directorio de uploads no sean tratados como scripts PHP.

No crear reglas específicas de Apache si el proyecto realmente utiliza Nginx.

No asumir que:

```text
storage/app/public
```

por sí solo garantiza que un archivo no pueda ejecutarse.

El agente debe inspeccionar la infraestructura/configuración existente y aplicar la defensa correspondiente sin romper `storage:link`.

---

# 11. Tiptap

El endpoint de imágenes utilizado por Tiptap debe utilizar exactamente la misma política de seguridad.

Actualmente el editor tiene:

```js
Image.configure({
    inline: false,
    allowBase64: false,
})
```

Mantenerlo.

El endpoint:

```text
blog.images.store
```

debe:

1. comprobar autenticación;
2. comprobar autorización;
3. validar el archivo;
4. limitar tamaño;
5. limitar dimensiones;
6. procesar la imagen;
7. convertirla a WebP;
8. generar un nombre controlado por servidor;
9. almacenar mediante Laravel Storage;
10. devolver únicamente la información necesaria para que Tiptap inserte la imagen.

La respuesta debe continuar siendo compatible con:

```js
payload.url
```

y:

```js
editor.chain()
    .focus()
    .setImage({
        src: payload.url,
        alt: "",
    })
    .run();
```

No romper la integración Tiptap existente.

---

# 12. Autorización

Revisar quién puede ejecutar cada endpoint de upload.

Las rutas administrativas deben estar protegidas por autenticación.

Como mínimo:

```php
Route::middleware([
    'auth',
    'verified',
])->group(function () {
    // admin routes
});
```

Pero si el proyecto ya tiene policies, gates, roles o middleware de autorización, utilizarlos.

Idealmente distinguir:

```text
admin
editor
```

si el sistema ya tiene infraestructura para roles.

No crear un sistema completo de roles si el proyecto todavía no lo necesita.

El requisito importante es:

> Un usuario público o no autorizado jamás debe poder utilizar el endpoint de upload administrativo.

Esto es especialmente importante para:

```text
/blog/images
```

porque Tiptap lo consume desde el CMS.

---

# 13. Controladores y Services

Antes de implementar, inspecciona si existe una arquitectura de servicios para uploads.

Si ya existe algo como:

```text
ImageService
MediaService
UploadService
```

reutilizarlo.

Si existen varios controladores duplicando lógica de:

```text
validate
store
generate filename
```

considera centralizar únicamente esa responsabilidad.

No crear una arquitectura excesivamente compleja.

El objetivo es evitar tener:

```text
ProjectController
    └── lógica propia de imágenes

PostController
    └── lógica propia de imágenes

TiptapController
    └── lógica propia de imágenes
```

Preferir una responsabilidad compartida para procesamiento/almacenamiento de imágenes.

---

# 14. Reemplazo y eliminación de imágenes

Revisar qué ocurre cuando:

- se reemplaza una portada;
- se elimina una portada;
- se actualiza un proyecto;
- se elimina un proyecto;
- se elimina un post;
- se reemplaza una imagen inline.

Evitar archivos huérfanos cuando sea razonablemente seguro hacerlo.

Pero no implementar un sistema complejo de garbage collection en esta tarea si no existe actualmente.

Especialmente para imágenes inline de Tiptap, no asumir que borrar una imagen del HTML significa automáticamente que el archivo debe borrarse, porque puede requerir un análisis de referencias.

Documenta cualquier limitación existente.

---

# 15. SEO

El nombre semántico del archivo es una mejora, pero NO tratarlo como el principal mecanismo SEO.

La implementación debe preservar también:

```html
<img
    src="..."
    alt="Descripción relevante"
>
```

El contexto de la imagen debe ser semántico.

Para blog y proyectos, revisar que exista capacidad de proporcionar:

- `alt`;
- título/contexto;
- slug;
- contenido relacionado.

No generar automáticamente un `alt` engañoso solamente a partir del nombre del archivo.

Si actualmente Tiptap inserta:

```js
alt: ""
```

no conviertas esto en una refactorización del editor salvo que sea necesario.

Simplemente deja documentada la posibilidad de mejorar el manejo de `alt` posteriormente.

---

# 16. Futura migración a S3/R2/Cloudinary

NO implementar almacenamiento externo ahora.

La arquitectura debe quedar preparada para poder cambiar:

```env
FILESYSTEM_DISK=public
```

por un filesystem externo en el futuro.

No acoplar:

```text
Storage
URL
DB
Controller
```

de forma que una futura migración requiera reescribir toda la aplicación.

Utilizar las APIs estándar de Laravel Storage.

---

# 17. Antivirus

No implementar ClamAV, VirusTotal ni servicios externos en esta fase.

Para este proyecto no se justifica la complejidad adicional.

La seguridad inicial debe basarse en:

```text
autorización
+
validación
+
limitación de tamaño
+
limitación de dimensiones
+
procesamiento
+
conversión WebP
+
nombres generados por servidor
+
storage adecuado
+
servidor web configurado para no ejecutar uploads
```

Documentar que un análisis antivirus puede añadirse posteriormente si el volumen o el riesgo del proyecto lo justifican.

---

# 18. Backups

No implementar un sistema de backups nuevo en esta tarea.

Pero verifica que la estrategia de almacenamiento utilizada pueda ser incluida posteriormente en los backups del proyecto.

---

# 19. Compatibilidad con la implementación existente

No romper:

- Livewire;
- Tiptap;
- Alpine;
- formularios actuales;
- validaciones existentes;
- rutas existentes;
- respuesta JSON del endpoint de Tiptap;
- URLs públicas actuales si pueden mantenerse compatibles.

Si actualmente existen archivos almacenados con el sistema anterior, NO eliminarlos automáticamente.

Si es necesaria una migración de archivos existentes, primero documentar qué migración sería necesaria y no ejecutarla destructivamente sin una estrategia explícita.

---

# 20. Tests

Después de implementar:

### Tests backend

Agregar o adaptar tests para comprobar como mínimo:

1. usuario no autenticado no puede subir;
2. usuario autenticado pero no autorizado no puede subir, si existe autorización;
3. upload válido funciona;
4. extensión/tipo no permitido falla;
5. archivo demasiado grande falla;
6. dimensiones excesivas fallan;
7. archivo que intenta hacerse pasar por imagen no válida;
8. la imagen final se almacena como WebP;
9. el nombre final no utiliza el nombre original del usuario;
10. la ruta pertenece al directorio esperado;
11. el endpoint de Tiptap devuelve una URL válida;
12. `allowBase64` continúa desactivado.

No es necesario implementar antivirus.

### Verificación manual

Comprobar:

```text
JPG → WebP
PNG → WebP
WebP → WebP
```

Comprobar también que:

```text
malicioso.php
malicioso.php.jpg
archivo con extensión manipulada
```

no puedan terminar como un script ejecutable.

No utilizar archivos realmente maliciosos durante los tests; utiliza fixtures inocuos con extensiones/tipos manipulados.

---

# 21. Configuración

Revisar:

```text
config/filesystems.php
.env
routes/
app/Http/Controllers/
app/Services/
app/Models/
database/migrations/
resources/views/
```

y cualquier configuración del servidor necesaria para proteger uploads.

No modificar configuraciones que no estén relacionadas con esta tarea.

---

# 22. Criterios de aceptación

La implementación se considera correcta cuando:

- [ ] Los uploads administrativos requieren autenticación/autorización.
- [ ] Las imágenes no se guardan directamente en `public/uploads`.
- [ ] Se utiliza Laravel Storage.
- [ ] Los archivos pasan validación de imagen.
- [ ] Existe límite de tamaño.
- [ ] Existe límite de dimensiones.
- [ ] Las imágenes se convierten a WebP.
- [ ] Los nombres son generados por el servidor.
- [ ] Los nombres pueden ser semánticos/SEO-friendly.
- [ ] Existe un identificador corto para evitar colisiones.
- [ ] No se utiliza el nombre original como nombre final.
- [ ] Las rutas de DB son relativas al filesystem.
- [ ] Tiptap utiliza el mismo sistema seguro de upload.
- [ ] No se permiten imágenes Base64.
- [ ] Los uploads no pueden ejecutar PHP/scripts.
- [ ] La solución utiliza la abstracción Storage de Laravel.
- [ ] No se incorpora S3/R2/Cloudinary todavía.
- [ ] Los archivos existentes no se eliminan de forma destructiva.
- [ ] Los tests existentes continúan pasando.
- [ ] Se agregan tests para los casos críticos de upload.

---

# Entrega final

Al terminar, proporciona:

1. Archivos modificados.
2. Dependencias nuevas, si las hay.
3. Cambios realizados por archivo.
4. Cómo se validan los uploads.
5. Cómo se generan los nombres.
6. Dónde se almacenan físicamente.
7. Cómo se generan las URLs públicas.
8. Cómo se protege el directorio contra ejecución.
9. Cómo quedó protegido el endpoint de Tiptap.
10. Tests ejecutados y resultado.
11. Cualquier problema de infraestructura que no haya podido resolverse automáticamente.
12. Cualquier migración pendiente para imágenes existentes.

Importante: **no amplíes el alcance hacia S3, Cloudinary, antivirus, CDN o un sistema complejo de media management**. La meta es dejar una implementación Laravel sólida, segura y preparada para crecer, pero adecuada al tamaño actual del proyecto.
