<?php
if (!function_exists('client_caretaker_skill_parts')) {
    /**
     * Split caregiver qualifications into display lines (pipe-separated or double-space blocks).
     *
     * @return list<string>
     */
    function client_caretaker_skill_parts(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }
        if (str_contains($raw, '|')) {
            $parts = array_map('trim', explode('|', $raw));

            return array_values(array_filter($parts, static fn ($p) => $p !== ''));
        }
        $split = preg_split('/\s{2,}/u', $raw, -1, PREG_SPLIT_NO_EMPTY);

        return $split !== false && $split !== [] ? array_values($split) : [$raw];
    }
}
