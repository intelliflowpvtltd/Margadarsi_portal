<?php

namespace App\Rules;

use App\Models\Department;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DepartmentBelongsToProject implements ValidationRule
{
    protected int $projectId;

    public function __construct(int $projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $department = Department::find($value);

        if (!$department) {
            $fail('The selected department does not exist.');
            return;
        }

        if ($department->project_id !== $this->projectId) {
            $fail('The selected department does not belong to the specified project.');
        }
    }
}
