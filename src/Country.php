<?php

declare(strict_types=1);

namespace Country;

use Exception;

class Country
{
    /**
     * The attributes array.
     *
     * @var array
     */
    protected $attributes;

    /**
     * Create a new Country instance.
     *
     * @param array $attributes
     *
     * @throws \Exception
     */
    public function __construct($attributes)
    {
        // Set the attributes
        $this->setAttributes($attributes);

        // Check required mandatory attributes
        if (empty($this->getName()) || empty($this->getOfficialName())
            || empty($this->getNativeName()) || empty($this->getNativeOfficialName())
            || empty($this->getIsoAlpha2()) || empty($this->getIsoAlpha3())
        ) {
            throw new Exception('Missing mandatory country attributes!');
        }
    }

    /**
     * Set the attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get an item from attributes array using "dot" notation.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $array = $this->attributes;

        if (is_null($key)) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * Get the common name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->get('name.common') ?: $this->get('name');
    }

    /**
     * Get the official name.
     *
     * @return string|null
     */
    public function getOfficialName(): ?string
    {
        return $this->get('name.official') ?: $this->get('official_name');
    }

    /**
     * Get the given native name or fallback to first native name.
     *
     * @param string|null $languageCode
     *
     * @return string|null
     */
    public function getNativeName($languageCode = null): ?string
    {
        $languageCode = $languageCode ? mb_strtolower($languageCode) : null;

        return $this->get("name.native.{$languageCode}.common")
            ?: (current($this->get('name.native', []))['common'] ?: $this->get('native_name'));
    }

    /**
     * Get the given native official name or fallback to first native official name.
     *
     * @param string|null $languageCode
     *
     * @return string|null
     */
    public function getNativeOfficialName($languageCode = null): ?string
    {
        $languageCode = $languageCode ? mb_strtolower($languageCode) : null;

        return $this->get("name.native.{$languageCode}.official")
            ?: (current($this->get('name.native', []))['official'] ?: $this->get('native_official_name'));
    }

    /**
     * Get the native names.
     *
     * @return array|null
     */
    public function getNativeNames(): ?array
    {
        return $this->get('name.native');
    }

    /**
     * Get the ISO 3166-1 alpha2.
     *
     * @return string|null
     */
    public function getIsoAlpha2()
    {
        return $this->get('iso_3166_1_alpha2');
    }

    /**
     * Get the ISO 3166-1 alpha3.
     *
     * @return string|null
     */
    public function getIsoAlpha3()
    {
        return $this->get('iso_3166_1_alpha3');
    }

    /**
     * Get the translations.
     *
     * @return array
     */
    public function getTranslations(): array
    {
        // Get english name
        $name = [
            'eng' => [
                'common' => $this->getName(),
                'official' => $this->getOfficialName(),
            ],
        ];

        // Get native names
        $natives = $this->getNativeNames() ?: [];

        // Get other translations
        $file = __DIR__.'/../resources/translations/'.mb_strtolower($this->getIsoAlpha2()).'.json';
        $translations = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

        // Merge all names together
        $result = array_merge($translations, $natives, $name);

        // Sort alphabetically
        ksort($result);

        return $result;
    }

    /**
     * Get the divisions.
     *
     * @return array|null
     */
    public function getDivisions(): ?array
    {
        if (! ($code = $this->getIsoAlpha2())) {
            return null;
        }

        return file_exists($file = __DIR__.'/../resources/divisions/'.mb_strtolower($code).'.json') ? json_decode(file_get_contents($file), true) : null;
    }
}
