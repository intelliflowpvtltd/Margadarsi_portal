<?php

namespace App\Rules;

use App\Models\Role;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RoleAuthorityCheck implements ValidationRule
{
    protected $userRole;

    /**
     * Create a new rule instance.
     *
     * @param  \App\Models\Role|null  $userRole
     * @return void
     */
    public function __construct(?Role $userRole)
    {
        $this->userRole = $userRole;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Super admin can assign any role
        if ($this->userRole && $this->userRole->slug === 'super_admin') {
            return;
        }

        // If user has no role, deny
        if (!$this->userRole) {
            $fail('You do not have permission to assign roles.');
            return;
        }

        $targetRole = Role::find($value);

        if (!$targetRole) {
            $fail('The selected role does not exist.');
            return;
        }

        // Check hierarchy - can only assign roles at or below your level
        if ($targetRole->hierarchy_level < $this->userRole->hierarchy_level) {
            $fail("You cannot assign a role ({$targetRole->name}) with higher authority than your own ({$this->userRole->name}).");
            return;
        }

        // Check scope - can only assign roles within your scope
        if (isset($this->userRole->scope) && isset($targetRole->scope)) {
            // Project-scoped users cannot assign company-wide roles
            if ($this->userRole->scope === 'project' && $targetRole->scope === 'company') {
                $fail('You cannot assign company-wide roles from a project-scoped role.');
                return;
            }

            // Department-scoped users cannot assign company or project roles
            if ($this->userRole->scope === 'department' && in_array($targetRole->scope, ['company', 'project'])) {
                $fail('You cannot assign higher-scope roles from a department-scoped role.');
                return;
            }
        }
    }
}
