<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

class HtmlSanitizer
{
    /**
     * Map of allowed attributes per tag.
     *
     * @var array<string, array<int, string>>
     */
    private const ALLOWED_ATTRS = [
        'a' => ['href', 'title'],
        'img' => ['src', 'alt', 'title'],
    ];

    /**
     * Schemes allowed in URL attributes. Any other scheme (e.g. `javascript:`,
     * `data:`) causes the attribute to be removed.
     *
     * @var array<int, string>
     */
    private const ALLOWED_SCHEMES = ['http', 'https', 'mailto', 'tel'];

    public function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $dom = $this->parse($html);

        $body = $dom->getElementsByTagName('body')->item(0);
        if ($body === null) {
            return '';
        }

        $this->cleanNode($body);

        $clean = $dom->saveHTML($body);
        if ($clean === false) {
            return '';
        }

        return $this->stripWrappingBodyTags($clean);
    }

    private function parse(string $html): DOMDocument
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);

        // Load with a wrapper so all body content lives under a single root.
        $dom->loadHTML(
            '<?xml encoding="UTF-8"?>'
            .'<html><body>'
            .$html
            .'</body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $dom;
    }

    private function cleanNode(DOMNode $node): void
    {
        if (! $node->hasChildNodes()) {
            return;
        }

        // Walk the children of `$node` in document order. After every
        // operation the next sibling is re-read from the live tree.
        $child = $node->firstChild;
        while ($child !== null) {
            $next = $child->nextSibling;

            if ($child instanceof DOMText) {
                $child = $next;

                continue;
            }

            if (! $child instanceof DOMElement) {
                if ($child->parentNode === $node) {
                    $node->removeChild($child);
                }
                $child = $next;

                continue;
            }

            $tag = strtolower($child->nodeName);

            if (! $this->isTagallowed($tag)) {
                // Unwrap: move children up into the parent before dropping
                // the wrapping element. The first moved child now sits at
                // the position `$child` previously held, so we restart the
                // walk from there (not from $next).
                $first = null;
                while ($child->firstChild) {
                    $moved = $child->firstChild;
                    $node->insertBefore($moved, $child);
                    $first ??= $moved;
                }
                if ($child->parentNode === $node) {
                    $node->removeChild($child);
                }
                // Continue processing from the first moved child, not $next.
                $child = $first ?? $next;

                continue;
            }

            $this->filterAttributes($child, $tag);
            $this->cleanNode($child);
            $child = $next;
        }
    }

    private function isTagallowed(string $tag): bool
    {
        $allowed = [
            'p', 'br', 'h2', 'h3', 'h4',
            'ul', 'ol', 'li',
            'strong', 'em', 'u', 's',
            'code', 'pre', 'blockquote',
            'a', 'img', 'hr',
        ];

        return in_array($tag, $allowed, true);
    }

    private function filterAttributes(DOMElement $element, string $tag): void
    {
        $allowed = self::ALLOWED_ATTRS[$tag] ?? [];

        // Snapshot attributes because we'll mutate the list.
        $attrs = [];
        foreach ($element->attributes as $attr) {
            $attrs[] = $attr;
        }

        foreach ($attrs as $attr) {
            $name = strtolower($attr->nodeName);
            $value = (string) $attr->nodeValue;

            if (! in_array($name, $allowed, true)) {
                $element->removeAttributeNode($attr);

                continue;
            }

            if (in_array($name, ['href', 'src'], true)) {
                if (! $this->isSafeUrl($value)) {
                    $element->removeAttributeNode($attr);
                }
            }
        }

        // Drop the element entirely if it ended up with no content and is
        // a self-closing/safe-to-drop element like `<a>` without href.
        if ($tag === 'a' && ! $element->hasAttribute('href')) {
            // Drop the link: keep text inside.
            while ($element->firstChild) {
                $element->parentNode?->insertBefore($element->firstChild, $element);
            }
            $element->parentNode?->removeChild($element);
        }
    }

    private function isSafeUrl(string $value): bool
    {
        $value = trim($value);
        if ($value === '') {
            return false;
        }

        // Allow in-site relative URLs.
        if (str_starts_with($value, '/') || str_starts_with($value, '#')) {
            return true;
        }

        $scheme = parse_url($value, PHP_URL_SCHEME);
        if (! is_string($scheme)) {
            return false;
        }

        return in_array(strtolower($scheme), self::ALLOWED_SCHEMES, true);
    }

    private function stripWrappingBodyTags(string $html): string
    {
        // The wrapper html/body/head we added during parsing; strip them.
        $html = preg_replace('/^\s*<\?xml[^?]+\?>\s*/i', '', $html) ?? $html;
        $html = preg_replace('/^\s*<!DOCTYPE[^>]+>\s*/i', '', $html) ?? $html;

        if (preg_match('/<body[^>]*>(.*)<\/body>\s*$/is', $html, $matches) === 1) {
            $html = $matches[1];
        }

        return trim($html);
    }
}
