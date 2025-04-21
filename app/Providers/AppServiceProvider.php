<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Domain\Stock\Interfaces\StockServiceInterface;
use Domain\Stock\Interfaces\StockRepositoryInterface;
use Domain\Stock\Services\StockService;
use App\Repositories\StockRepository;

use Domain\Supplier\Interfaces\SupplierServiceInterface;
use Domain\Supplier\Interfaces\SupplierRepositoryInterface;
use App\Repositories\SupplierRepository;
use Domain\Supplier\Services\SupplierService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StockServiceInterface::class, StockService::class);
        $this->app->bind(StockRepositoryInterface::class, StockRepository::class);

        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(SupplierServiceInterface::class, SupplierService::class);
    }

    public function boot(): void
    {
        
    }
}
