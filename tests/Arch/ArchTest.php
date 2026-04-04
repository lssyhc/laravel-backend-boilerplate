<?php

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

arch('enums are enums')
    ->expect('App\Enums')
    ->toBeEnums();

arch('requests extend FormRequest')
    ->expect('App\Http\Requests')
    ->toExtend('Illuminate\Foundation\Http\FormRequest');

arch('exceptions extend Exception')
    ->expect('App\Exceptions')
    ->toExtend('Exception');
