---
paths:
  - resources/js/alpine/tiptap-editor.js
---

# Alpine

## Extensiones Tiptap: implementar por tandas, no todo junto
PRs chicos por grupo de extensiones (1-3, luego 4-6) para facilitar bisect y rollback. Cada tanda: npm install → implementar → npm run build → test manual → PR. Verificar sanitización del body en backend especialmente con tablas, code blocks e iframes de YouTube antes de merge.
