@props(['body', 'slug'])

<div wire:ignore wire:key="post-body-editor" class="space-y-2" x-data="tiptapEditor({
    endpoint: '{{ route('blog.images.store') }}',
    csrf: document.querySelector('meta[name=\'csrf-token\']').content,
    initial: @js($body),
    slug: @js($slug ?? ''),
})"
    x-init="mount($el, $wire)">
    <flux:label>{{ __('Body') }}</flux:label>

    <div data-tiptap-target="toolbar"
        class="flex flex-wrap gap-1 rounded-t-lg border border-b-0 border-zinc-200 bg-zinc-50 p-1 dark:border-zinc-700 dark:bg-zinc-800">
        <button type="button" data-cmd="bold" :class="{ 'is-active': isBold }" :aria-pressed="isBold"
            class="rounded px-2 py-1 text-sm font-bold hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Bold">B</button>
        <button type="button" data-cmd="italic" :class="{ 'is-active': isItalic }"
            :aria-pressed="isItalic"
            class="rounded px-2 py-1 text-sm italic hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Italic">I</button>
        <button type="button" data-cmd="underline" :class="{ 'is-active': isUnderline }"
            :aria-pressed="isUnderline"
            class="rounded px-2 py-1 text-sm underline hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Underline">U</button>
        <button type="button" data-cmd="h2" :class="{ 'is-active': isH2 }" :aria-pressed="isH2"
            class="rounded px-2 py-1 text-xs font-semibold hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Heading 2">H2</button>
        <button type="button" data-cmd="h3" :class="{ 'is-active': isH3 }" :aria-pressed="isH3"
            class="rounded px-2 py-1 text-xs font-semibold hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Heading 3">H3</button>
        <button type="button" data-cmd="ul" :class="{ 'is-active': isBulletList }"
            :aria-pressed="isBulletList"
            class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Bullet list">•≡</button>
        <button type="button" data-cmd="ol" :class="{ 'is-active': isOrderedList }"
            :aria-pressed="isOrderedList"
            class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Numbered list">1.</button>
        <button type="button" data-cmd="quote" :class="{ 'is-active': isBlockquote }"
            :aria-pressed="isBlockquote"
            class="rounded px-2 py-1 text-sm italic hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Quote">&ldquo;</button>
        <button type="button" data-cmd="link" :class="{ 'is-active': isLink }" :aria-pressed="isLink"
            class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Link">⌘L</button>

        {{-- Image controls --}}
        <span class="mx-1 w-px self-stretch bg-zinc-200 dark:bg-zinc-700"></span>
        <button type="button" data-cmd="image"
            class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Insertar imagen">🖼</button>
        <button type="button" data-cmd="image-alt"
            :class="{ 'opacity-50 pointer-events-none': !isImage }"
            class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Editar alt de imagen">Alt</button>
        <span x-show="isImage"
            class="mx-0.5 inline-flex items-center gap-0.5 rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">
            <button type="button" data-cmd="set-image-align-left"
                :class="{ 'is-active': imageAlign === 'left' }"
                :aria-pressed="imageAlign === 'left'"
                class="rounded px-1.5 py-0.5 text-xs hover:bg-white dark:hover:bg-zinc-600"
                aria-label="Alinear imagen a la izquierda">◀</button>
            <button type="button" data-cmd="set-image-align-center"
                :class="{ 'is-active': imageAlign === 'center' }"
                :aria-pressed="imageAlign === 'center'"
                class="rounded px-1.5 py-0.5 text-xs hover:bg-white dark:hover:bg-zinc-600"
                aria-label="Centrar imagen">◆</button>
            <button type="button" data-cmd="set-image-align-right"
                :class="{ 'is-active': imageAlign === 'right' }"
                :aria-pressed="imageAlign === 'right'"
                class="rounded px-1.5 py-0.5 text-xs hover:bg-white dark:hover:bg-zinc-600"
                aria-label="Alinear imagen a la derecha">▶</button>
        </span>

        {{-- Text alignment --}}
        <span class="mx-1 w-px self-stretch bg-zinc-200 dark:bg-zinc-700"></span>
        <button type="button" data-cmd="align-left" :class="{ 'is-active': textAlign === 'left' }"
            :aria-pressed="textAlign === 'left'"
            class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Align left">⫷ </button>
        <button type="button" data-cmd="align-center"
            :class="{ 'is-active': textAlign === 'center' }"
            :aria-pressed="textAlign === 'center'"
            class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Align center">⫶</button>
        <button type="button" data-cmd="align-right"
            :class="{ 'is-active': textAlign === 'right' }"
            :aria-pressed="textAlign === 'right'"
            class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Align right">⫸</button>
        <button type="button" data-cmd="clear-float" :class="{ 'is-active': isClearFloat }"
            :aria-pressed="isClearFloat"
            class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Cortar flotado de imagen">⤓</button>
        <button type="button" data-cmd="highlight" :class="{ 'is-active': isHighlight }"
            :aria-pressed="isHighlight"
            class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700 bg-yellow-200 dark:bg-yellow-600"
            aria-label="Resaltar">H</button>
        <button type="button" data-cmd="table"
            class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Insertar tabla">⊞</button>
        <button type="button" data-cmd="youtube"
            class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700"
            aria-label="Insertar video de YouTube">▶</button>
    </div>

    <div data-tiptap-target="editor"
        class="rounded-b-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
    </div>

    <p class="text-xs text-zinc-500 dark:text-zinc-400 text-right"><span x-text="charCount"></span>
        {{ __('actions.characters') }}</p>

    @error('body')
        <p class="text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>
