<?php

namespace App\Traits;

trait HasTranslations
{
    /**
     * Récupère la valeur traduite pour la langue courante
     */
    public function getTranslation(string $attribute, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $value = $this->getAttributes()[$attribute] ?? null;

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return $value[$locale] ?? $value['fr'] ?? $value[array_key_first($value ?? [])] ?? '';
    }

    /**
     * Accès magique : $model->nom (retourne la traduction courante)
     */
    public function getAttribute($key)
    {
        if (in_array($key, $this->translatable ?? [])) {
            return $this->getTranslation($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * Accès à toutes les traductions : $model->getTranslations('nom')
     */
    public function getTranslations(string $attribute): array
    {
        $value = $this->getAttributes()[$attribute] ?? null;

        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }

        return $value ?? [];
    }

    /**
     * Définit une traduction : $model->setTranslation('nom', 'fr', 'Pizza')
     */
    public function setTranslation(string $attribute, string $locale, string $value): self
    {
        $current = $this->getTranslations($attribute);
        $current[$locale] = $value;
        $this->attributes[$attribute] = json_encode($current, JSON_UNESCAPED_UNICODE);

        return $this;
    }
}
