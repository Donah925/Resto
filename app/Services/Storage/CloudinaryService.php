<?php

namespace App\Services\Storage;

use Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Exception;

class CloudinaryService
{
    protected string $cloudName;
    protected string $apiKey;
    protected string $apiSecret;

    public function __construct()
    {
        $this->cloudName = config('services.cloudinary.cloud_name');
        $this->apiKey = config('services.cloudinary.api_key');
        $this->apiSecret = config('services.cloudinary.api_secret');

        Cloudinary::config([
            'cloud_name' => $this->cloudName,
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret,
        ]);
    }

    /**
     * Uploader une image
     *
     * @param UploadedFile $file Le fichier à uploader
     * @param array $options Options d'upload
     * @return array Informations sur le fichier uploadé
     */
    public function uploadImage(UploadedFile $file, array $options = []): array
    {
        try {
            $result = Cloudinary::uploader()->upload($file->getRealPath(), array_merge([
                'folder' => $options['folder'] ?? 'images',
                'transformation' => [
                    ['width' => $options['width'] ?? 1920, 'height' => $options['height'] ?? 1080, 'crop' => 'limit'],
                    ['quality' => 'auto:good'],
                ],
            ], $options));

            Log::info('Image uploadée sur Cloudinary', [
                'public_id' => $result['public_id'],
                'url' => $result['secure_url'],
            ]);

            return [
                'success' => true,
                'public_id' => $result['public_id'],
                'url' => $result['secure_url'],
                'width' => $result['width'],
                'height' => $result['height'],
                'format' => $result['format'],
            ];
        } catch (Exception $e) {
            Log::error('Erreur upload image Cloudinary', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible d\'uploader l\'image',
            ];
        }
    }

    /**
     * Uploader un document
     *
     * @param UploadedFile $file Le fichier à uploader
     * @param array $options Options d'upload
     * @return array Informations sur le fichier uploadé
     */
    public function uploadDocument(UploadedFile $file, array $options = []): array
    {
        try {
            $result = Cloudinary::uploader()->upload($file->getRealPath(), array_merge([
                'folder' => $options['folder'] ?? 'documents',
                'resource_type' => 'raw',
            ], $options));

            Log::info('Document uploadé sur Cloudinary', [
                'public_id' => $result['public_id'],
                'url' => $result['secure_url'],
            ]);

            return [
                'success' => true,
                'public_id' => $result['public_id'],
                'url' => $result['secure_url'],
                'format' => $result['format'],
            ];
        } catch (Exception $e) {
            Log::error('Erreur upload document Cloudinary', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible d\'uploader le document',
            ];
        }
    }

    /**
     * Générer une URL avec transformation
     *
     * @param string $publicId L'identifiant public du fichier
     * @param array $transformations Transformations à appliquer
     * @return string URL transformée
     */
    public function getTransformedUrl(string $publicId, array $transformations = []): string
    {
        return Cloudinary::url($publicId, [
            'transformation' => $transformations,
        ]);
    }

    /**
     * Supprimer un fichier
     *
     * @param string $publicId L'identifiant public du fichier
     * @return array Résultat de la suppression
     */
    public function delete(string $publicId): array
    {
        try {
            $result = Cloudinary::uploader()->destroy($publicId);

            if ($result['result'] === 'ok') {
                Log::info('Fichier supprimé de Cloudinary', ['public_id' => $publicId]);

                return [
                    'success' => true,
                    'message' => 'Fichier supprimé avec succès',
                ];
            }

            return [
                'success' => false,
                'error' => 'Échec de la suppression',
            ];
        } catch (Exception $e) {
            Log::error('Erreur suppression Cloudinary', [
                'public_id' => $publicId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de supprimer le fichier',
            ];
        }
    }

    /**
     * Générer une miniature
     *
     * @param string $publicId L'identifiant public de l'image
     * @param int $width Largeur de la miniature
     * @param int $height Hauteur de la miniature
     * @return string URL de la miniature
     */
    public function getThumbnail(string $publicId, int $width = 200, int $height = 200): string
    {
        return Cloudinary::url($publicId, [
            'transformation' => [
                ['width' => $width, 'height' => $height, 'crop' => 'thumb'],
            ],
        ]);
    }
}
