<?php


namespace App\Providers;


use App\Infra\Classes\Common\PhoneValidator;
use App\Infra\Interfaces\Repositories\CustomerRepositoryInterface;
use App\Infra\Interfaces\Resources\ResourceInterface;
use App\Infra\Interfaces\Validators\PhoneValidatorInterface;
use App\Infra\Interfaces\Validators\ValidatorInterface;
use App\Infra\Repositories\CustomerRepository;
use App\Infra\Resources\RequestResource;
use App\Infra\Validators\Validator;
use Illuminate\Support\ServiceProvider;

class InterfacesBindingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ResourceInterface::class, RequestResource::class);
        $this->app->bind(ValidatorInterface::class, Validator::class);
        $this->app->bind(PhoneValidatorInterface::class, PhoneValidator::class);

        //repositories
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
    }

    public function boot()
    {

    }
}
