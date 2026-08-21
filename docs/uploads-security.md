# Uploads & Image Security

## Policy

All image uploads in this application go through a centralised pipeline:

```
User → Validation → Processing → WebP Conversion → Storage → DB (relative path only)
```

### Validation (defence in depth)

| Layer | What it checks |
|-------|---------------|
| Laravel rules | `image`, `mimes:jpg,jpeg,png,webp`, `max:2048`, `dimensions:max_width=3000,max_height=3000` |
| `UploadImageAction` | Size, real MIME via `getMimeType()`, real dimensions via `getimagesize()` |
| Intervention Image | Decodes the file — rejects corrupt/non-image data |

### Naming

Filenames are generated server-side. The original client name is **never** used.

- **Cover images**: `<post-slug>-<random8>.webp`
- **Inline Tiptap images**: `inline-<random8>.webp`

### Storage

- Disk: `public` (Laravel Storage abstraction)
- Physical path: `storage/app/public/blog/webp/{thumbs,full}/`
- Symlink: `public/storage → storage/app/public`
- URLs: `Storage::disk('public')->url($relativePath)`

### Limits

| Limit | Value |
|-------|-------|
| File size | 2 MB |
| Dimensions | 3000 × 3000 px |
| Accepted formats | JPG, JPEG, PNG, WebP |
| Output format | WebP (quality 82) |
| Thumb width | 640 px |
| Full width | 1600 px |

---

## Server-side protection against script execution

### Apache

The file `storage/app/public/.htaccess` blocks execution of PHP and other
server-side scripts inside the uploads directory. It is automatically picked
up by Apache when serving files through the `public/storage` symlink.

### Nginx

Nginx does not read `.htaccess` files. Add the following block to your
server configuration (inside the `server {}` block):

```nginx
location ~* /storage/.+\.(php|phtml|php[3-8]?|phar|pht|phps|cgi|pl|py|sh|asp|aspx|jsp)$ {
    deny all;
}
```

This prevents any PHP file under `storage/` from being executed, even if
someone manages to upload a file with a `.php` extension.

---

## Tiptap inline images

The endpoint `POST /dashboard/blog/images` (name: `blog.images.store`) is
protected by:

- `auth` + `verified` middleware
- `can:manage-posts` gate (admin or editor role)
- `throttle:blog-inline-images` (30 requests/minute per user)
- Same validation pipeline as cover images

Tiptap is configured with `allowBase64: false` — only server-uploaded URLs
are accepted.

---

## Known limitations

### Orphaned inline images

When an inline image is removed from the Tiptap editor HTML, the file is
**not** automatically deleted from storage. Analysing the HTML to detect
removed images would require parsing every body update and cross-referencing
stored files — complexity not justified at the current scale.

Files can be cleaned up manually or with a future maintenance command.

### Existing ULID-named files

Files uploaded before this security hardening use ULID names
(e.g. `01M0751386R1P82RTT4BR6GBTW.webp`). These are **not** renamed or
moved. The database paths remain valid.

### No antivirus

ClamAV / VirusTotal integration is not implemented. The security posture
relies on:

1. Authentication + authorisation
2. Strict file validation (type, size, dimensions)
3. Image processing (decoding strips most payloads)
4. WebP conversion (output is always a valid image)
5. Server-level script execution blocking
6. Server-controlled filenames

### No S3/R2/Cloudinary

The architecture uses `Storage::disk()` abstraction throughout. Switching to
S3 in the future requires only changing `FILESYSTEM_DISK=s3` in `.env` and
configuring credentials — no code changes needed.

---

## Manual verification checklist

- [ ] Upload JPG → stored as WebP
- [ ] Upload PNG → stored as WebP
- [ ] Upload WebP → stored as WebP
- [ ] Upload GIF → rejected (422)
- [ ] Upload PDF disguised as PNG → rejected (422)
- [ ] Upload file > 2 MB → rejected (422)
- [ ] Upload 4000×4000 image → rejected (422)
- [ ] Upload 3000×3000 image → accepted
- [ ] Upload 3001×3000 image → rejected (422)
- [ ] Filename does not contain original client name
- [ ] `allowBase64: false` in Tiptap config
- [ ] Guest cannot POST to `/dashboard/blog/images`
- [ ] User without role cannot POST to `/dashboard/blog/images`
- [ ] `.htaccess` present in `storage/app/public/`
- [ ] `FILESYSTEM_DISK=public` in `.env.example`
