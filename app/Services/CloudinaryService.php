<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary(
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
                'url' => [
                    'secure' => true,
                ],
            ])
        );
    }

    public function subirImagen(string $rutaArchivo, string $carpeta = 'surify'): string
    {
        $resultado = $this->cloudinary->uploadApi()->upload($rutaArchivo, [
            'folder' => $carpeta,
        ]);

        return $resultado['secure_url'];
    }

    public function eliminarImagen(string $publicId): void
    {
        $this->cloudinary->uploadApi()->destroy($publicId);
    }
}