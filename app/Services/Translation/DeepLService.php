<?php

namespace App\Services\Translation;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class DeepLService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.deepl.api_key');
        $this->baseUrl = config('services.deepl.environment', 'free') === 'free' 
            ? 'https://api-free.deepl.com/v2' 
            : 'https://api.deepl.com/v2';
    }

    /**
     * Traduire un texte
     *
     * @param string $text Le texte à traduire
     * @param string $targetLang La langue cible (ex: FR, EN, DE)
     * @param string|null $sourceLang La langue source (optionnel, auto-détecté si null)
     * @return array Résultat de la traduction
     */
    public function translate(string $text, string $targetLang, ?string $sourceLang = null): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'DeepL-Auth-Key ' . $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->post($this->baseUrl . '/translate', [
                'text' => $text,
                'target_lang' => strtoupper($targetLang),
                'source_lang' => $sourceLang ? strtoupper($sourceLang) : null,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                Log::info('Traduction DeepL effectuée', [
                    'source_lang' => $result['translations.0.detected_source_language'] ?? 'auto',
                    'target_lang' => $targetLang,
                ]);

                return [
                    'success' => true,
                    'translated_text' => $result['translations.0.text'] ?? '',
                    'detected_source_language' => $result['translations.0.detected_source_language'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => 'Échec de la traduction',
            ];
        } catch (Exception $e) {
            Log::error('Erreur traduction DeepL', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec DeepL',
            ];
        }
    }

    /**
     * Traduire plusieurs textes en une seule requête
     *
     * @param array $texts Les textes à traduire
     * @param string $targetLang La langue cible
     * @param string|null $sourceLang La langue source
     * @return array Résultats des traductions
     */
    public function translateMultiple(array $texts, string $targetLang, ?string $sourceLang = null): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'DeepL-Auth-Key ' . $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->post($this->baseUrl . '/translate', [
                'text' => $texts,
                'target_lang' => strtoupper($targetLang),
                'source_lang' => $sourceLang ? strtoupper($sourceLang) : null,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $translations = array_column($result['translations'] ?? [], 'text');

                return [
                    'success' => true,
                    'translated_texts' => $translations,
                ];
            }

            return [
                'success' => false,
                'error' => 'Échec de la traduction multiple',
            ];
        } catch (Exception $e) {
            Log::error('Erreur traduction multiple DeepL', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec DeepL',
            ];
        }
    }

    /**
     * Vérifier l'utilisation de l'API
     *
     * @return array Informations sur l'utilisation
     */
    public function getUsage(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'DeepL-Auth-Key ' . $this->apiKey,
            ])->get($this->baseUrl . '/usage');

            if ($response->successful()) {
                $result = $response->json();

                return [
                    'success' => true,
                    'character_count' => $result['character_count'] ?? 0,
                    'character_limit' => $result['character_limit'] ?? 0,
                ];
            }

            return [
                'success' => false,
                'error' => 'Impossible de récupérer l\'utilisation',
            ];
        } catch (Exception $e) {
            Log::error('Erreur récupération usage DeepL', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec DeepL',
            ];
        }
    }

    /**
     * Détecter la langue d'un texte
     *
     * @param string $text Le texte à analyser
     * @return array Langue détectée
     */
    public function detectLanguage(string $text): array
    {
        // DeepL ne propose pas d'endpoint dédié pour la détection
        // On utilise la traduction sans source_lang pour obtenir la langue détectée
        $result = $this->translate($text, 'EN');

        if ($result['success']) {
            return [
                'success' => true,
                'language' => $result['detected_source_language'],
            ];
        }

        return $result;
    }
}
