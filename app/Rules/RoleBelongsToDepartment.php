<?php

namespace App\Rules;

use App\Models\Role;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RoleBelongsToDepartment implements ValidationRule
{
    protected int $departmentId;

    public function __construct(int $departmentId)
    {
        $this->departmentId = $departmentId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $role = Role::find($value);

        if (!$role) {
            $fail('The selected role does not exist.');
            return;
        }

        // Global roles (company-wide) are allowed
        if ($role->isGlobal()) {
            return;
        }

        if ($role->department_id !== $this->departmentId) {
            $fail('The selected role does not belong to the specified department.');
        }
    }
}
