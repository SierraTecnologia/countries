<?php

declare(strict_types=1);

namespace Country\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class CountryServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function boot(): void
    {
        // Add country validation rule
        Validator::extend(
            'country', function ($attribute, $value) {
                return array_key_exists(mb_strtolower($value), countries());
            }, 'Country MUST be valid!'
        );
    }
}
