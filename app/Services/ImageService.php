<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    /**
     * Tailles d'images prédéfinies
     */
    const TAILLES = [
        'thumbnail' => [150, 150],
        'small' => [300, 300],
        'medium' => [600, 600],
        'large' => [1200, 1200],
        'original' => null,
    ];

    /**
     * Upload et redimensionnement d'une image
     */
    public function uploadImage($file, string $dossier, ?string $nom = null, array $tailles = []): array
    {
        $nomFichier = $nom ?? Str::uuid()->toString();
        $extension = $file->getClientOriginalExtension();
        $nomsFichiers = [];

        // Taille originale
        $cheminOriginal = $this->sauvegarderImage($file, $dossier, "{$nomFichier}.{$extension}");
        $nomsFichiers['original'] = $cheminOriginal;

        // Générer les versions redimensionnées
        $tailles = empty($tailles) ? ['thumbnail', 'small', 'medium'] : $tailles;

        foreach ($tailles as $taille) {
            $dimensions = self::TAILLES[$taille] ?? null;
            
            if ($dimensions) {
                $image = Image::read($file->getRealPath());
                $image->resize($dimensions[0], $dimensions[1], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $nomRedimensionne = "{$nomFichier}_{$taille}.{$extension}";
                $cheminRedimensionne = $this->sauvegarderImageFromResource(
                    $image,
                    $dossier,
                    $nomRedimensionne,
                    $extension
                );

                $nomsFichiers[$taille] = $cheminRedimensionne;
            }
        }

        return $nomsFichiers;
    }

    /**
     * Sauvegarder une image brute
     */
    private function sauvegarderImage($file, string $dossier, string $nom): string
    {
        $chemin = $file->storeAs($dossier, $nom, 'public');
        return Storage::url($chemin);
    }

    /**
     * Sauvegarder une image depuis une ressource Intervention Image
     */
    private function sauvegarderImageFromResource($image, string $dossier, string $nom, string $extension): string
    {
        $contenu = $image->encodeByExtension($extension);
        $cheminComplet = "{$dossier}/{$nom}";
        
        Storage::disk('public')->put($cheminComplet, $contenu);
        
        return Storage::url($cheminComplet);
    }

    /**
     * Supprimer une image et ses versions
     */
    public function supprimerImage(array $chemins): void
    {
        foreach ($chemins as $chemin) {
            if ($chemin) {
                $fichier = str_replace(Storage::url(''), '', $chemin);
                if (Storage::disk('public')->exists($fichier)) {
                    Storage::disk('public')->delete($fichier);
                }
            }
        }
    }

    /**
     * Redimensionner une image existante
     */
    public function redimensionnerImage(string $cheminSource, int $largeur, int $hauteur, ?string $cheminDestination = null): string
    {
        $image = Image::read(Storage::path($cheminSource));
        
        $image->resize($largeur, $hauteur, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $cheminDestination = $cheminDestination ?? $cheminSource;
        $contenu = $image->encodeByExtension(pathinfo($cheminSource, PATHINFO_EXTENSION));
        
        Storage::put($cheminDestination, $contenu);

        return Storage::url($cheminDestination);
    }

    /**
     * Créer une vignette (thumbnail)
     */
    public function creerVignette(string $cheminSource, int $taille = 150): string
    {
        return $this->redimensionnerImage($cheminSource, $taille, $taille);
    }

    /**
     * Recadrer une image
     */
    public function recadrerImage(string $cheminSource, int $x, int $y, int $largeur, int $hauteur, ?string $cheminDestination = null): string
    {
        $image = Image::read(Storage::path($cheminSource));
        $image->crop($largeur, $hauteur, $x, $y);

        $cheminDestination = $cheminDestination ?? $cheminSource;
        $contenu = $image->encodeByExtension(pathinfo($cheminSource, PATHINFO_EXTENSION));
        
        Storage::put($cheminDestination, $contenu);

        return Storage::url($cheminDestination);
    }

    /**
     * Appliquer un filigrane (watermark)
     */
    public function appliquerFiligrane(string $cheminImage, string $cheminFiligrane, string $position = 'bottom-right'): string
    {
        $image = Image::read(Storage::path($cheminImage));
        $watermark = Image::read(Storage::path($cheminFiligrane));

        $positions = [
            'top-left' => [10, 10],
            'top-right' => [null, 10],
            'bottom-left' => [10, null],
            'bottom-right' => [null, null],
            'center' => ['center', 'center'],
        ];

        $coords = $positions[$position] ?? $positions['bottom-right'];

        $image->insert($watermark, $position);

        $contenu = $image->encodeByExtension(pathinfo($cheminImage, PATHINFO_EXTENSION));
        Storage::put($cheminImage, $contenu);

        return Storage::url($cheminImage);
    }

    /**
     * Optimiser une image pour le web
     */
    public function optimiserImage(string $cheminSource, int $qualite = 80): string
    {
        $image = Image::read(Storage::path($cheminSource));
        $extension = pathinfo($cheminSource, PATHINFO_EXTENSION);

        $contenu = match ($extension) {
            'jpg', 'jpeg' => $image->toJpeg($qualite),
            'png' => $image->toPng(),
            'webp' => $image->toWebp($qualite),
            default => $image->encodeByExtension($extension, $qualite),
        };

        Storage::put($cheminSource, $contenu);

        return Storage::url($cheminSource);
    }

    /**
     * Extraire les métadonnées EXIF d'une image
     */
    public function getMetadonneesEXIF(string $cheminImage): array
    {
        $image = Image::read(Storage::path($cheminImage));
        
        return [
            'largeur' => $image->width(),
            'hauteur' => $image->height(),
            'format' => $image->origin()->format(),
            'taille_fichier' => Storage::size($cheminImage),
        ];
    }

    /**
     * Vérifier si un fichier est une image valide
     */
    public function estImageValide($file): bool
    {
        $mimeTypesAutorises = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        return in_array($file->getMimeType(), $mimeTypesAutorises);
    }

    /**
     * Convertir une image vers un autre format
     */
    public function convertirFormat(string $cheminSource, string $nouveauFormat, ?string $cheminDestination = null): string
    {
        $image = Image::read(Storage::path($cheminSource));

        $contenu = match (strtolower($nouveauFormat)) {
            'jpg', 'jpeg' => $image->toJpeg(90),
            'png' => $image->toPng(),
            'webp' => $image->toWebp(85),
            'gif' => $image->toGif(),
            default => throw new \InvalidArgumentException("Format {$nouveauFormat} non supporté"),
        };

        $cheminDestination = $cheminDestination ?? 
            pathinfo($cheminSource, PATHINFO_DIRNAME) . '/' . 
            pathinfo($cheminSource, PATHINFO_FILENAME) . '.' . $nouveauFormat;

        Storage::put($cheminDestination, $contenu);

        return Storage::url($cheminDestination);
    }

    /**
     * Générer une image placeholder
     */
    public function genererPlaceholder(string $texte = '', int $largeur = 400, int $hauteur = 300, string $couleur = '#cccccc'): string
    {
        $image = Image::canvas($largeur, $hauteur, $couleur);

        if (!empty($texte)) {
            $image->text($texte, $largeur / 2, $hauteur / 2, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(24);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('middle');
            });
        }

        $nomFichier = 'placeholders/' . Str::uuid() . '.png';
        $contenu = $image->toPng();
        Storage::disk('public')->put($nomFichier, $contenu);

        return Storage::disk('public')->url($nomFichier);
    }
}
