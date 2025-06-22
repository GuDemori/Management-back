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
use App\Repositories\EstablishmentTypeRepository;
use App\Domain\EstablishmentType\Services\EstablishmentTypeService;
use App\Domain\EstablishmentType\Interfaces\EstablishmentTypeServiceInterface;

use App\Domain\ProductCategory\Interfaces\ProductCategoryRepositoryInterface;
use App\Domain\ProductCategory\Interfaces\ProductCategoryServiceInterface;
use App\Domain\ProductCategory\Services\ProductCategoryService;
use App\Repositories\ProductCategoryRepository;

use App\Repositories\ProductNicknameRepository;
use App\Domain\Product\Interfaces\ProductNicknameRepositoryInterface;
use App\Domain\Product\Interfaces\ProductNicknameServiceInterface;
use App\Domain\Product\Services\ProductNicknameService;

use App\Repositories\ProductStockRepository;
use Domain\ProductStock\Interfaces\ProductStockRepositoryInterface;
use Domain\ProductStock\Interfaces\ProductStockServiceInterface;
use Domain\ProductStock\Services\ProductStockService;

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

        $this->app->bind(ProductCategoryRepositoryInterface::class, ProductCategoryRepository::class);
        $this->app->bind(ProductCategoryServiceInterface::class, ProductCategoryService::class);

        $this->app->bind(ProductStockRepositoryInterface::class, ProductStockRepository::class);
        $this->app->bind(ProductStockServiceInterface::class, ProductStockService::class);

        $this->app->bind(ProductNicknameRepositoryInterface::class, ProductNicknameRepository::class);
        $this->app->bind(ProductNicknameServiceInterface::class, ProductNicknameService::class);
    }

    public function boot(): void {}
}
