<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateStorageLinkWithoutSymlink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:link-shared-hosting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create storage link using .htaccess for shared hosting (when symlink is disabled)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $target = storage_path('app/public');
        $link = public_path('storage');

        // Check if target exists
        if (!File::exists($target)) {
            File::makeDirectory($target, 0755, true);
            $this->info('Created storage/app/public directory.');
        }

        // Check if already set up
        $htaccessPath = $link . '/.htaccess';
        $indexPath = $link . '/index.php';
        if (File::exists($htaccessPath) && File::exists($indexPath)) {
            $this->info('Storage link already created using shared hosting method.');
            return 0;
        }

        // Remove existing link if it's a symlink
        if (File::exists($link) && is_link($link)) {
            unlink($link);
        }

        // Create the storage directory in public if it doesn't exist
        if (!File::exists($link)) {
            File::makeDirectory($link, 0755, true);
        }

        // Create .htaccess file to route requests through index.php
        $htaccessContent = <<<'HTACCESS'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?file=$1 [L,QSA]
</IfModule>
HTACCESS;

        File::put($link . '/.htaccess', $htaccessContent);
        
        // Create index.php that serves files from storage/app/public
        $indexContent = <<<'PHP'
<?php
/**
 * Storage Link for Shared Hosting (without symlink)
 * This file serves files from storage/app/public directory
 */

$file = $_GET['file'] ?? '';

if (empty($file)) {
    http_response_code(404);
    exit('File not found');
}

// Prevent directory traversal
$file = str_replace(['../', '..\\'], '', $file);
$file = ltrim($file, '/\\');

// Get the storage path
$storagePath = realpath(__DIR__ . '/../../storage/app/public');
$filePath = $storagePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);

// Check if file exists and is within storage path
if (!$filePath || !file_exists($filePath) || strpos(realpath($filePath), $storagePath) !== 0) {
    http_response_code(404);
    exit('File not found');
}

// Get MIME type
$mimeType = mime_content_type($filePath);
if (!$mimeType) {
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    $mimeType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
}

// Set headers
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: public, max-age=31536000');

// Output file
readfile($filePath);
exit;
PHP;

        File::put($link . '/index.php', $indexContent);

        $this->info('Storage link created successfully using .htaccess method!');
        $this->info('The public/storage directory will now serve files from storage/app/public');
        
        return 0;
    }
}
