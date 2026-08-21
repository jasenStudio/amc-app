---
paths:
  - app/Models/Post.php
---

# Models

## Cover de posts se maneja vía relación polimórfica Image
Desde la refactorización, posts.cover_image / cover_image_thumb ya no existen. El cover vive en la tabla `images` vía `Post::coverImage()`. Las vistas deben usar los accessors `$post->cover_image_url` / `$post->cover_image_thumb_url` (o `$post->coverImage->thumb_path` / `full_path` con `ImageUrl::public()`), nunca columnas legacy. Siempre eager-load `coverImage` en listados para evitar N+1.
