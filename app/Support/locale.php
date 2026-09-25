<?php

if (! function_exists('t')) {
    function t(?string $text, array $replace = []): string
    {
        $text ??= '';
        if ($text === '') {
            return '';
        }

        if (app()->getLocale() !== 'bn') {
            return strtr($text, $replace);
        }

        static $map = null;
        $map ??= require lang_path('bn/ui.php');

        $normalized = str_replace(["\r\n", "\r", '’', '‘', '“', '”'], ["\n", "\n", "'", "'", '"', '"'], $text);
        $translated = $map[$text] ?? $map[$normalized] ?? null;

        if ($translated === null && str_contains($normalized, "\n")) {
            $translated = implode("\n", array_map(
                fn (string $line) => $map[$line] ?? $line,
                explode("\n", $normalized)
            ));
        }

        if ($translated === null && preg_match('/^Best for clients looking for (.+) support with personalized guidance\.$/u', $normalized, $match)) {
            $service = $map[$match[1]] ?? $match[1];
            $template = $map['Best for clients looking for :service support with personalized guidance.'] ?? null;
            $translated = $template ? strtr($template, [':service' => $service]) : $text;
        }

        $translated ??= $text;

        return $replace ? strtr($translated, $replace) : $translated;
    }
}
