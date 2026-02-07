<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslations
{
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->getTranslatableAttributes())) {
            if (is_array($value)) {
                $locale = App::getLocale();
                // Špeciálny prípad pre ukrajinčinu (ua), kde chceme použiť ruštinu (ru) podľa požiadavky
                $targetLocale = ($locale === 'ua') ? 'ru' : $locale;

                return $value[$targetLocale] ??
                       $value[config('app.fallback_locale')] ??
                       ($value['sk'] ?? (is_array($value) && count($value) > 0 ? reset($value) : $value));
            }
        }

        return $value;
    }

    public function getTranslatableAttributes(): array
    {
        return property_exists($this, 'translatable') ? $this->translatable : [];
    }

    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();
        foreach ($this->getTranslatableAttributes() as $field) {
            $attributes[$field] = $this->getAttribute($field);
        }
        return $attributes;
    }

    public function getTranslations($key)
    {
        return parent::getAttribute($key) ?: [];
    }
}
