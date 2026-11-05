<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Genera un backup de la base de datos y lo sube a Cloudinary';

    public function handle()
    {
        $this->info('Iniciando backup de la base de datos...');

        // Formato custom (-F c): se restaura con pg_restore
        $nombreArchivo = 'backup_' . now('America/Argentina/Buenos_Aires')->format('Y-m-d_H-i-s') . '.dump';
        $carpeta       = storage_path('app/backups');
        $rutaTemporal  = $carpeta . '/' . $nombreArchivo;

        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        // Armar el comando de pg_dump
        $pgDump = env('PG_DUMP_PATH', 'pg_dump');
        $dbUrl  = env('DB_URL');

        if ($dbUrl) {
            // Railway: la conexión viene completa en DB_URL
            $this->info('Usando conexión por DB_URL.');
            $comando = "\"{$pgDump}\" --dbname=\"{$dbUrl}\" -F c -b -f \"{$rutaTemporal}\"";
        } else {
            // Local: datos sueltos del .env
            $this->info('Usando conexión local (.env).');
            putenv('PGPASSWORD=' . env('DB_PASSWORD'));
            $comando = "\"{$pgDump}\""
                     . ' -h ' . env('DB_HOST', '127.0.0.1')
                     . ' -p ' . env('DB_PORT', 5432)
                     . ' -U ' . env('DB_USERNAME')
                     . " -F c -b -f \"{$rutaTemporal}\" "
                     . env('DB_DATABASE');
        }

        // 2>&1 para capturar el mensaje de error de pg_dump si falla
        exec($comando . ' 2>&1', $output, $codigo);

        if ($codigo !== 0) {
            $this->error('Error al generar el backup con pg_dump:');
            $this->error(implode("\n", $output));
            if (file_exists($rutaTemporal)) {
                unlink($rutaTemporal);
            }
            return Command::FAILURE;
        }

        $tamanoKb = round(filesize($rutaTemporal) / 1024, 1);
        $this->info("Backup generado ({$tamanoKb} KB). Subiendo a Cloudinary...");

        // Subir a Cloudinary
        try {
            $cloudinary = new Cloudinary(
                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key'    => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                ])
            );

            $resultado = $cloudinary->uploadApi()->upload($rutaTemporal, [
                'folder'        => 'surify/backups',
                'resource_type' => 'raw',
                'public_id'     => $nombreArchivo,
            ]);
        } catch (\Throwable $e) {
            $this->error('Error al subir el backup a Cloudinary: ' . $e->getMessage());
            unlink($rutaTemporal);
            return Command::FAILURE;
        } 

        // Eliminar archivo temporal
        unlink($rutaTemporal);

        $this->info('✅ Backup generado y subido correctamente.');
        $this->info('URL: ' . $resultado['secure_url']);

        return Command::SUCCESS;
    }
}