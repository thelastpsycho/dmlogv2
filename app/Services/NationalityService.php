<?php

namespace App\Services;

class NationalityService
{
    /**
     * Normalization map for nationality variations.
     * Maps common variations to canonical names.
     */
    protected array $normalizationMap = [
        // North America
        'usa' => 'American',
        'u.s.a.' => 'American',
        'u.s.a' => 'American',
        'united states' => 'American',
        'united states of america' => 'American',
        'us' => 'American',
        'u.s.' => 'American',
        'u.s' => 'American',
        'united stated of america' => 'American',
        'united states of america (usa)' => 'American',
        'united states america' => 'American',

        'canada' => 'Canadian',

        'mexico' => 'Mexican',

        // Europe
        'uk' => 'British',
        'u.k.' => 'British',
        'u.k' => 'British',
        'gb' => 'British',
        'united kingdom' => 'British',
        'england' => 'British',
        'scotland' => 'British',
        'wales' => 'British',
        'northern ireland' => 'British',
        'great britain' => 'British',
        'united kingdom (uk)' => 'British',
        'united of kingdom' => 'British',

        'france' => 'French',

        'germany' => 'German',
        'deutschland' => 'German',
        'de' => 'German',

        'italy' => 'Italian',
        'italiano' => 'Italian',
        'ltalia' => 'Italian',

        'spain' => 'Spanish',
        'españa' => 'Spanish',

        'netherlands' => 'Dutch',
        'holland' => 'Dutch',

        'belgium' => 'Belgian',

        'switzerland' => 'Swiss',

        'austria' => 'Austrian',

        'portugal' => 'Portuguese',

        'greece' => 'Greek',

        'sweden' => 'Swedish',

        'norway' => 'Norwegian',

        'denmark' => 'Danish',

        'finland' => 'Finnish',

        'poland' => 'Polish',
        'polandia' => 'Polish',

        'russia' => 'Russian',
        'russian federation' => 'Russian',
        'rusia' => 'Russian',

        'ukraine' => 'Ukrainian',

        'czech republic' => 'Czech',

        'ireland, republic of' => 'Irish',

        'asia' => 'Asian',

        // Asia
        'china' => 'Chinese',
        "people's republic of china" => 'Chinese',

        'japan' => 'Japanese',
        'jp' => 'Japanese',

        'south korea' => 'South Korean',
        'korea' => 'South Korean',

        'india' => 'Indian',

        'thailand' => 'Thai',

        'vietnam' => 'Vietnamese',

        'singapore' => 'Singaporean',

        'malaysia' => 'Malaysian',

        'indonesia' => 'Indonesian',
        'id' => 'Indonesian',
        'ndonesian' => 'Indonesian',

        'philippines' => 'Filipino',
        'philipines' => 'Filipino',

        'hong kong' => 'Hong Konger',
        'hongkong' => 'Hong Konger',

        'taiwan' => 'Taiwanese',

        // Middle East
        'uae' => 'Emirati',
        'united arab emirates' => 'Emirati',
        'uea' => 'Emirati',

        'saudi arabia' => 'Saudi',

        'qatar' => 'Qatari',

        'israel' => 'Israeli',

        'turkey' => 'Turkish',

        'iran' => 'Iranian',

        'iraq' => 'Iraqi',

        // Oceania
        'australia' => 'Australian',
        'aussie' => 'Australian',
        'aus' => 'Australian',

        'new zealand' => 'New Zealander',
        'kiwi' => 'New Zealander',

        // South America
        'brazil' => 'Brazilian',
        'nbr' => 'Brazilian',

        'argentina' => 'Argentinian',

        'chile' => 'Chilean',

        'colombia' => 'Colombian',

        'peru' => 'Peruvian',

        // Africa
        'south africa' => 'South African',

        'egypt' => 'Egyptian',

        'morocco' => 'Moroccan',

        'kenya' => 'Kenyan',

        'nigeria' => 'Nigerian',

        'sri lanka' => 'Sri Lankan',

        'pakistan' => 'Pakistani',
        'pakistanese' => 'Pakistani',

        'kazakhstan' => 'Kazakhstani',
        'kazachtan' => 'Kazakhstani',

        'lithuania' => 'Lithuanian',

        // Common abbreviations and variations
    ];

    /**
     * Normalize a nationality value to its canonical form.
     */
    public function normalize(?string $nationality): string
    {
        if (empty($nationality)) {
            return 'Not Specified';
        }

        // Trim whitespace
        $normalized = trim($nationality);

        // Convert to lowercase for lookup
        $lowercase = strtolower($normalized);

        // Check if we have a mapping for this variation
        if (isset($this->normalizationMap[$lowercase])) {
            return $this->normalizationMap[$lowercase];
        }

        // If no mapping found, return the original value with proper capitalization
        return $this->capitalizeWords($normalized);
    }

    /**
     * Normalize an array/collection of nationalities.
     */
    public function normalizeArray(array $nationalities): array
    {
        return array_map([$this, 'normalize'], $nationalities);
    }

    /**
     * Capitalize each word in a string.
     */
    protected function capitalizeWords(string $string): string
    {
        return ucwords(strtolower($string));
    }

    /**
     * Get all known nationality mappings.
     */
    public function getNormalizationMap(): array
    {
        return $this->normalizationMap;
    }

    /**
     * Add or update a nationality mapping.
     */
    public function addMapping(string $from, string $to): void
    {
        $this->normalizationMap[strtolower(trim($from))] = $to;
    }

    /**
     * Get statistics about nationality variations in the database.
     * Useful for identifying new mappings that need to be added.
     */
    public function analyzeVariations(): array
    {
        $variations = [];

        foreach ($this->normalizationMap as $variation => $canonical) {
            if (!isset($variations[$canonical])) {
                $variations[$canonical] = [];
            }
            $variations[$canonical][] = $variation;
        }

        return $variations;
    }
}
