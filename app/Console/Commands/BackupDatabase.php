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

        $nombreArchivo = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $rutaTemporal  = storage_path('app/backups/' . $nombreArchivo);

        // Crear carpeta si no existe
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        // Generar el backup con pg_dump
        $host     = env('DB_HOST');
        $port     = env('DB_PORT', 5432);
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        putenv("PGPASSWORD={$password}");

        $pgDump = env('PG_DUMP_PATH', 'pg_dump');
        $comando = "\"{$pgDump}\" -h {$host} -p {$port} -U {$username} -F c -b -v -f \"{$rutaTemporal}\" {$database}";
        exec($comando, $output, $codigo);

        if ($codigo !== 0) {
            $this->error('Error al generar el backup con pg_dump.');
            return Command::FAILURE;
        }

        // Subir a Cloudinary
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

        // Eliminar archivo temporal
        unlink($rutaTemporal);

        $this->info('✅ Backup generado y subido correctamente.');
        $this->info('URL: ' . $resultado['secure_url']);

        return Command::SUCCESS;
    }
}
