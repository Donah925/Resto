<?php

namespace App\Services\Search;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MeilisearchService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.meilisearch.base_url', 'http://localhost:7700');
        $this->apiKey = config('services.meilisearch.api_key');
    }

    /**
     * Rechercher des documents dans un index
     *
     * @param string $index Nom de l'index
     * @param string $query Requête de recherche
     * @param array $options Options de recherche
     * @return array Résultats de la recherche
     */
    public function search(string $index, string $query, array $options = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/indexes/' . $index . '/search', array_merge([
                'q' => $query,
                'limit' => 20,
            ], $options));

            if ($response->successful()) {
                return [
                    'success' => true,
                    'hits' => $response->json('hits'),
                    'total' => $response->json('estimatedTotalHits', 0),
                    'query' => $response->json('query'),
                ];
            }

            return [
                'success' => false,
                'error' => 'Erreur de recherche',
            ];
        } catch (Exception $e) {
            Log::error('Erreur recherche Meilisearch', [
                'index' => $index,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Meilisearch',
            ];
        }
    }

    /**
     * Ajouter ou mettre à jour un document
     *
     * @param string $index Nom de l'index
     * @param array $document Document à indexer
     * @return array Résultat de l'opération
     */
    public function addDocument(string $index, array $document): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/indexes/' . $index . '/documents', [$document]);

            if ($response->successful()) {
                Log::info('Document ajouté à Meilisearch', [
                    'index' => $index,
                    'id' => $document['id'] ?? 'unknown',
                ]);

                return [
                    'success' => true,
                    'task_id' => $response->json('taskUid'),
                ];
            }

            return [
                'success' => false,
                'error' => 'Échec de l\'ajout du document',
            ];
        } catch (Exception $e) {
            Log::error('Erreur ajout document Meilisearch', [
                'index' => $index,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Meilisearch',
            ];
        }
    }

    /**
     * Ajouter ou mettre à jour plusieurs documents
     *
     * @param string $index Nom de l'index
     * @param array $documents Documents à indexer
     * @return array Résultat de l'opération
     */
    public function addDocuments(string $index, array $documents): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/indexes/' . $index . '/documents', $documents);

            if ($response->successful()) {
                Log::info('Documents ajoutés à Meilisearch', [
                    'index' => $index,
                    'count' => count($documents),
                ]);

                return [
                    'success' => true,
                    'task_id' => $response->json('taskUid'),
                ];
            }

            return [
                'success' => false,
                'error' => 'Échec de l\'ajout des documents',
            ];
        } catch (Exception $e) {
            Log::error('Erreur ajout documents Meilisearch', [
                'index' => $index,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Meilisearch',
            ];
        }
    }

    /**
     * Supprimer un document
     *
     * @param string $index Nom de l'index
     * @param string|int $documentId ID du document
     * @return array Résultat de l'opération
     */
    public function deleteDocument(string $index, string|int $documentId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->delete($this->baseUrl . '/indexes/' . $index . '/documents/' . $documentId);

            if ($response->successful()) {
                Log::info('Document supprimé de Meilisearch', [
                    'index' => $index,
                    'id' => $documentId,
                ]);

                return [
                    'success' => true,
                    'task_id' => $response->json('taskUid'),
                ];
            }

            return [
                'success' => false,
                'error' => 'Échec de la suppression',
            ];
        } catch (Exception $e) {
            Log::error('Erreur suppression document Meilisearch', [
                'index' => $index,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Meilisearch',
            ];
        }
    }

    /**
     * Créer un index
     *
     * @param string $index Nom de l'index
     * @param array $options Options de création
     * @return array Résultat de l'opération
     */
    public function createIndex(string $index, array $options = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/indexes', [
                'uid' => $index,
                'primaryKey' => $options['primary_key'] ?? 'id',
            ]);

            if ($response->successful()) {
                Log::info('Index Meilisearch créé', ['index' => $index]);

                return [
                    'success' => true,
                    'task_id' => $response->json('taskUid'),
                ];
            }

            return [
                'success' => false,
                'error' => 'Échec de la création de l\'index',
            ];
        } catch (Exception $e) {
            Log::error('Erreur création index Meilisearch', [
                'index' => $index,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Meilisearch',
            ];
        }
    }

    /**
     * Configurer les paramètres d'un index
     *
     * @param string $index Nom de l'index
     * @param array $settings Paramètres de configuration
     * @return array Résultat de l'opération
     */
    public function updateSettings(string $index, array $settings): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->patch($this->baseUrl . '/indexes/' . $index . '/settings', $settings);

            if ($response->successful()) {
                Log::info('Paramètres Meilisearch mis à jour', ['index' => $index]);

                return [
                    'success' => true,
                    'task_id' => $response->json('taskUid'),
                ];
            }

            return [
                'success' => false,
                'error' => 'Échec de la mise à jour des paramètres',
            ];
        } catch (Exception $e) {
            Log::error('Erreur mise à jour paramètres Meilisearch', [
                'index' => $index,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Erreur de communication avec Meilisearch',
            ];
        }
    }
}
