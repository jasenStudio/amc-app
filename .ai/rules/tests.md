---
paths:
  - 'tests/**'
---

# Tests

## Tests que ejecutan comandos destructivos deben usar Storage::fake
El comando `blog:cleanup-orphan-images` borra archivos del disco real. Un test que lo invoque con la DB de test (vacía) puede borrar los assets reales de desarrollo. Todo test que pruebe comandos de limpieza/borrado de storage debe usar `Storage::fake('public')` en setUp.
