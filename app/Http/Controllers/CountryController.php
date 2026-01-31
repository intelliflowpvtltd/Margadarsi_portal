<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends BaseMasterController
{
    protected string $modelClass = Country::class;
    protected string $viewPath = 'masters.countries';

    protected array $validationRules = [
        'name' => 'required|string|max:100',
        'code' => 'required|string|max:3|unique:countries,code',
        'slug' => 'nullable|string|max:100',
        'icon' => 'nullable|string|max:50',
        'phone_code' => 'nullable|string|max:10',
        'currency_code' => 'nullable|string|max:3',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected array $updateValidationRules = [
        'name' => 'required|string|max:100',
        'code' => 'required|string|max:3',
        'slug' => 'nullable|string|max:100',
        'icon' => 'nullable|string|max:50',
        'phone_code' => 'nullable|string|max:10',
        'currency_code' => 'nullable|string|max:3',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected function loadRelationships($item)
    {
        return $item->load('states');
    }
}
