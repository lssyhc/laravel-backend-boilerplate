<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Arch Presets
|--------------------------------------------------------------------------
*/

arch()->preset()->php();
arch()->preset()->security();

/*
|--------------------------------------------------------------------------
| Global Rules
|--------------------------------------------------------------------------
*/

arch('no debugging statements in app code')
    ->expect('App')
    ->not->toUse(['dd', 'dump', 'ray', 'var_dump', 'print_r']);

arch('app code does not depend on tests')
    ->expect('App')
    ->not->toUse('Tests');

/*
|--------------------------------------------------------------------------
| Application Architecture
|--------------------------------------------------------------------------
*/

arch('models extend Authenticatable or Model')
    ->expect('App\Models')
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->ignoring('App\Models\User');

arch('controllers extend base Controller')
    ->expect('App\Http\Controllers')
    ->toExtend('App\Http\Controllers\Controller')
    ->ignoring('App\Http\Controllers\Controller');

arch('actions are final classes')
    ->expect('App\Actions')
    ->toBeFinal();

arch('DTOs are final readonly classes')
    ->expect('App\DTOs')
    ->toBeFinal()
    ->toBeReadonly();

arch('controllers are final classes')
    ->expect('App\Http\Controllers')
    ->toBeFinal()
    ->ignoring('App\Http\Controllers\Controller');

arch('form requests are final classes')
    ->expect('App\Http\Requests')
    ->toBeFinal();

arch('resources are final classes')
    ->expect('App\Http\Resources')
    ->toBeFinal();

arch('exceptions are final classes')
    ->expect('App\Exceptions')
    ->toBeFinal();

arch('policies are final classes')
    ->expect('App\Policies')
    ->toBeFinal();

arch('middleware are final classes')
    ->expect('App\Http\Middleware')
    ->toBeFinal();

arch('enums are enums')
    ->expect('App\Enums')
    ->toBeEnums();

arch('requests extend FormRequest')
    ->expect('App\Http\Requests')
    ->toExtend('Illuminate\Foundation\Http\FormRequest');

arch('exceptions extend Exception')
    ->expect('App\Exceptions')
    ->toExtend('Exception');

arch('traits are not classes')
    ->expect('App\Support')
    ->toBeTraits();
