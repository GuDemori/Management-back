<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RemoveOrphanImagesCommand extends Command
{

    protected $signature = 'images:cleanup-orphans';
    protected $description = 'Remove imagens órfãs do bucket S3';

    public function handle(): int
    {
        $allProductImages = Product::pluck('image_url')->map(function ($url) {
            return ltrim(parse_url($url, PHP_URL_PATH), '/');
        })->toArray();

        $allImagesInS3 = Storage::disk('s3')->files('products');

        $orphans = array_diff($allImagesInS3, $allProductImages);

        foreach ($orphans as $imagePath) {
            Storage::disk('s3')->delete($imagePath);
            Log::info("Imagem órfã removida: {$imagePath}");
        }

        $this->info('Imagens órfãs removidas com sucesso.');
        return Command::SUCCESS;
    }
}