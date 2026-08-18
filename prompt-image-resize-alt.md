# Prompt: resize nativo de imagen + edición de alt text

## Contexto

Proyecto Laravel 14 + Livewire 3 + Flux + Tailwind CSS v4. Editor de
posts con Tiptap montado vía Alpine.js, composable en
`resources/js/tiptap-editor.js`. La instancia de `Editor` vive en una
variable de closure (`let editor = null`), NO como propiedad reactiva
de Alpine — no cambiar ese patrón. Toolbar en
`post-form.blade.php` con botones `data-cmd="..."` y un switch en
`runCommand()` que los despacha.

## Restricciones

- No convertir `editor` en propiedad reactiva de Alpine.
- Mantener `mousedown` con `preventDefault()` en la toolbar.
- El botón nuevo debe usar las mismas clases Tailwind que los
  existentes (mismo tamaño, mismo estilo hover/dark mode).
- No tocar `wire:ignore` / `wire:key` del contenedor del editor.

## 1. Activar resize nativo en la extensión de imagen existente

- Confirmar la versión instalada de `@tiptap/extension-image`
  (`npm ls @tiptap/extension-image`). Si no trae la opción `resize` en
  su configuración, correr `npm update @tiptap/extension-image` — no
  hace falta cambiar de paquete.
- Actualizar la configuración existente de `Image.configure({...})`
  en `tiptap-editor.js` para incluir:

  ```js
  Image.configure({
      inline: false,
      allowBase64: false,
      resize: {
          enabled: true,
          directions: ['left', 'right', 'bottom-right', 'bottom-left'],
          minWidth: 50,
          minHeight: 50,
          alwaysPreserveAspectRatio: true,
      },
  }),
  ```

- Verificar visualmente: insertar una imagen, click sobre ella,
  confirmar que aparecen handles de resize en los bordes/esquinas
  configurados y que arrastrar cambia el tamaño manteniendo la
  proporción.

## 2. Botón para editar el `alt` de la imagen seleccionada

- Agregar botón en la toolbar del Blade, junto al botón de imagen
  existente:

  ```html
  <button type="button" data-cmd="image-alt" class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700" aria-label="Editar alt de imagen">Alt</button>
  ```

- En `tiptap-editor.js`, agregar el case en `runCommand()`:

  ```js
  case "image-alt":
      this.promptImageAlt(editor);
      break;
  ```

- Implementar el método `promptImageAlt(ed)` (mismo patrón que
  `promptLink`):

  ```js
  promptImageAlt(ed) {
      if (! ed.isActive('image')) {
          window.alert(window.__t.selectImageFirst ?? 'Seleccioná una imagen primero.');
          return;
      }
      const prevAlt = ed.getAttributes('image').alt ?? '';
      const alt = window.prompt(window.__t.imageAlt ?? 'Texto alternativo de la imagen', prevAlt);
      if (alt === null) {
          return;
      }
      ed.chain().focus().updateAttributes('image', { alt }).run();
  },
  ```

- Agregar las claves de traducción usadas (`selectImageFirst`,
  `imageAlt`) al objeto `window.__t` donde ya están definidas
  `url` e `imageUploadFailed`, tanto en inglés como en el idioma
  principal del sitio.

- El botón debe deshabilitarse visualmente (opacidad reducida,
  `pointer-events: none` o similar) cuando no hay una imagen
  seleccionada — usar `editor.isActive('image')` en un `x-effect` o
  callback de `onSelectionUpdate` del editor para togglear una clase.

## Verificación

- `npm run build` (o confirmar que `npm run dev` esté sirviendo el
  bundle actualizado).
- Insertar imagen, redimensionarla arrastrando un handle, guardar el
  post, recargar la página en modo edición y confirmar que el tamaño
  persistió en el HTML guardado (`width`/`height` en el `<img>`).
- Seleccionar una imagen, click en "Alt", cambiar el texto, confirmar
  que el atributo `alt` del `<img>` en el HTML resultante se actualizó.
- Confirmar que no aparece `RangeError: Applying a mismatched
  transaction` en consola al usar ninguno de los dos flujos nuevos.
