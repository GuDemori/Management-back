<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Domain\Product\Interfaces\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Domain\Product\Interfaces\ProductServiceInterface;
use App\Domain\Product\Services\ProductService;

use Domain\Stock\Interfaces\StockServiceInterface;
use Domain\Stock\Interfaces\StockRepositoryInterface;
use Domain\Stock\Services\StockService;
use App\Repositories\StockRepository;

use Domain\Supplier\Interfaces\SupplierServiceInterface;
use Domain\Supplier\Interfaces\SupplierRepositoryInterface;
use App\Repositories\SupplierRepository;
use Domain\Supplier\Services\SupplierService;

use Domain\User\Interfaces\UserServiceInterface;
use Domain\User\Services\UserService;
use Domain\User\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;

use App\Domain\EstablishmentType\Interfaces\EstablishmentTypeRepositoryInterface;
use App\Domain\EstablishmentType\Interfaces\EstablishmentTypeServiceInterface;
use App\Repositories\EstablishmentTypeRepository;
use App\Domain\EstablishmentType\Services\EstablishmentTypeService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StockServiceInterface::class, StockService::class);
        $this->app->bind(StockRepositoryInterface::class, StockRepository::class);

        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(SupplierServiceInterface::class, SupplierService::class);

        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);

        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        $this->app->bind(EstablishmentTypeRepositoryInterface::class, EstablishmentTypeRepository::class);
        $this->app->bind(EstablishmentTypeServiceInterface::class, EstablishmentTypeService::class);
    }

    public function boot(): void
    {

    }
}
