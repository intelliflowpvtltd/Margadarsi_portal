<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Organization Seeding...');

        // Step 1: Create Company
        $company = $this->createCompany();
        $this->command->info("✅ Company: {$company->name}");

        // Step 2: Seed Permissions
        $this->seedPermissions();
        $this->command->info('✅ Permissions seeded (25 permissions)');

        // Step 3: Create Projects
        $projects = $this->createProjects($company);
        $this->command->info("✅ Projects created (" . count($projects) . " projects)");

        // Step 4: Create Departments for each project
        foreach ($projects as $project) {
            $departments = $this->createDepartments($project);
            $this->command->info("✅ Departments created for {$project->name} (3 departments)");

            // Step 5: Create Roles for each department
            foreach ($departments as $departmentType => $department) {
                $roles = $this->createRolesForDepartment($company, $project, $department, $departmentType);
                $this->command->info("  ✅ Roles created for {$department->name} (" . count($roles) . " roles)");

                // Step 6: Assign permissions to roles
                $this->assignPermissionsToRoles($roles, $departmentType);

                // Step 7: Create users for each role
                $users = $this->createUsersForDepartment($company, $project, $department, $roles);
                $this->command->info("  ✅ Users created for {$department->name} (" . count($users) . " users)");
            }
        }

        $this->command->newLine();
        $this->command->info('🎉 Organization seeding completed!');
        $this->command->newLine();
        $this->printLoginCredentials();
    }

    private function createCompany(): Company
    {
        return Company::firstOrCreate(
            ['email' => 'admin@margadarsi.in'],
            [
                'name' => 'Margadarsi Infra Pvt Ltd',
                'legal_name' => 'Margadarsi Infrastructure Private Limited',
                'pan_number' => 'AABCM1234A',
                'gstin' => '36AABCM1234A1Z5',
                'cin' => 'U70100TG2010PTC068123',
                'rera_number' => 'P52100012345',
                'phone' => '9876543210',
                'email' => 'admin@margadarsi.in',
                'website' => 'https://margadarsi.in',
                'registered_address_line1' => 'Plot No 123, KPHB Colony',
                'registered_city' => 'Hyderabad',
                'registered_state' => 'Telangana',
                'registered_pincode' => '500072',
                'registered_country' => 'India',
                'is_active' => true,
            ]
        );
    }

    private function seedPermissions(): void
    {
        if (Permission::count() === 0) {
            Permission::seedPermissions();
        }
    }

    private function createProjects(Company $company): array
    {
        $projectsData = [
            [
                'name' => 'Margadarsi Heights',
                'slug' => 'margadarsi-heights',
                'type' => 'residential',
                'status' => 'ongoing',
                'description' => 'Premium residential apartments in Hyderabad',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
            ],
            [
                'name' => 'Margadarsi Tech Park',
                'slug' => 'margadarsi-tech-park',
                'type' => 'commercial',
                'status' => 'upcoming',
                'description' => 'Modern IT office spaces',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
            ],
        ];

        $projects = [];
        foreach ($projectsData as $data) {
            $projects[] = Project::firstOrCreate(
                ['company_id' => $company->id, 'slug' => $data['slug']],
                array_merge($data, [
                    'company_id' => $company->id,
                    'is_active' => true,
                    'is_featured' => true,
                ])
            );
        }

        return $projects;
    }

    private function createDepartments(Project $project): array
    {
        $departmentTypes = [
            'management' => [
                'name' => 'Management',
                'description' => 'Executive management and administration',
            ],
            'sales' => [
                'name' => 'Sales',
                'description' => 'Sales and business development',
            ],
            'pre_sales' => [
                'name' => 'Pre-Sales',
                'description' => 'Lead generation and qualification',
            ],
        ];

        $departments = [];
        foreach ($departmentTypes as $slug => $data) {
            $departments[$slug] = Department::firstOrCreate(
                ['project_id' => $project->id, 'slug' => $slug],
                array_merge($data, [
                    'project_id' => $project->id,
                    'slug' => $slug,
                    'is_active' => true,
                ])
            );
        }

        return $departments;
    }

    private function createRolesForDepartment(Company $company, Project $project, Department $department, string $departmentType): array
    {
        $roleDefinitions = Role::DEPARTMENT_ROLES[$departmentType] ?? [];
        $roles = [];

        foreach ($roleDefinitions as $roleData) {
            // Create roles at company-level only (not per-project) to avoid duplicates
            // Roles are shared across all projects in a company
            $roles[$roleData['slug']] = Role::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'slug' => $roleData['slug'],
                ],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'hierarchy_level' => $roleData['hierarchy_level'],
                    'is_system' => true,
                    'is_active' => true,
                    // Set project_id and department_id to null for company-level roles
                    'project_id' => null,
                    'department_id' => null,
                ]
            );
        }

        return $roles;
    }

    private function assignPermissionsToRoles(array $roles, string $departmentType): void
    {
        $permissions = Permission::all()->keyBy('name');

        // Define permission sets per department type
        $permissionMatrix = [
            'management' => [
                'super_admin' => $permissions->pluck('id')->toArray(), // All permissions
                'admin' => $permissions->whereNotIn('name', ['companies.delete'])->pluck('id')->toArray(),
            ],
            'sales' => [
                'project_manager' => $permissions->whereIn('name', [
                    'projects.view', 'projects.update',
                    'roles.view', 'roles.create', 'roles.update',
                    'users.view', 'users.create', 'users.update',
                    'leads.view', 'leads.create', 'leads.update', 'leads.delete',
                ])->pluck('id')->toArray(),
                'senior_sales_executive' => $permissions->whereIn('name', [
                    'leads.view', 'leads.create', 'leads.update',
                    'users.view',
                ])->pluck('id')->toArray(),
                'sales_executive' => $permissions->whereIn('name', [
                    'leads.view', 'leads.create', 'leads.update',
                ])->pluck('id')->toArray(),
            ],
            'pre_sales' => [
                'team_lead' => $permissions->whereIn('name', [
                    'leads.view', 'leads.create', 'leads.update',
                    'users.view',
                ])->pluck('id')->toArray(),
                'telecaller' => $permissions->whereIn('name', [
                    'leads.view', 'leads.create', 'leads.update',
                ])->pluck('id')->toArray(),
            ],
        ];

        $departmentPermissions = $permissionMatrix[$departmentType] ?? [];

        foreach ($roles as $slug => $role) {
            if (isset($departmentPermissions[$slug])) {
                $role->permissions()->sync($departmentPermissions[$slug]);
            }
        }
    }

    private function createUsersForDepartment(Company $company, Project $project, Department $department, array $roles): array
    {
        $users = [];
        $defaultPassword = Hash::make('password123');

        // Only create users for the first project (to avoid duplicates)
        if ($project->id !== Project::first()->id) {
            return $users;
        }

        foreach ($roles as $slug => $role) {
            $userData = $this->getUserDataForRole($slug, $company, $role, $department);
            
            if ($userData) {
                $users[$slug] = User::firstOrCreate(
                    ['company_id' => $company->id, 'email' => $userData['email']],
                    array_merge($userData, [
                        'password' => $defaultPassword,
                        'email_verified_at' => now(),
                        'is_active' => true,
                    ])
                );
            }
        }

        return $users;
    }

    private function getUserDataForRole(string $slug, Company $company, Role $role, Department $department): ?array
    {
        $usersMap = [
            'super_admin' => [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'superadmin@margadarsi.in',
                'phone' => '9876543210',
                'employee_code' => 'EMP001',
                'designation' => 'Chief Executive Officer',
                'department' => 'Management',
            ],
            'admin' => [
                'first_name' => 'Company',
                'last_name' => 'Admin',
                'email' => 'admin@margadarsi.in',
                'phone' => '9876543211',
                'employee_code' => 'EMP002',
                'designation' => 'Managing Director',
                'department' => 'Management',
            ],
            'project_manager' => [
                'first_name' => 'Vikram',
                'last_name' => 'Singh',
                'email' => 'vikram.singh@margadarsi.in',
                'phone' => '9876543212',
                'employee_code' => 'EMP003',
                'designation' => 'Project Manager',
                'department' => 'Sales',
            ],
            'senior_sales_executive' => [
                'first_name' => 'Priya',
                'last_name' => 'Sharma',
                'email' => 'priya.sharma@margadarsi.in',
                'phone' => '9876543213',
                'employee_code' => 'EMP004',
                'designation' => 'Senior Sales Executive',
                'department' => 'Sales',
            ],
            'sales_executive' => [
                'first_name' => 'Rajesh',
                'last_name' => 'Kumar',
                'email' => 'rajesh.kumar@margadarsi.in',
                'phone' => '9876543214',
                'employee_code' => 'EMP005',
                'designation' => 'Sales Executive',
                'department' => 'Sales',
            ],
            'team_lead' => [
                'first_name' => 'Sneha',
                'last_name' => 'Reddy',
                'email' => 'sneha.reddy@margadarsi.in',
                'phone' => '9876543215',
                'employee_code' => 'EMP006',
                'designation' => 'Team Lead',
                'department' => 'Pre-Sales',
            ],
            'telecaller' => [
                'first_name' => 'Rahul',
                'last_name' => 'Verma',
                'email' => 'rahul.verma@margadarsi.in',
                'phone' => '9876543216',
                'employee_code' => 'EMP007',
                'designation' => 'Telecaller',
                'department' => 'Pre-Sales',
            ],
        ];

        $userData = $usersMap[$slug] ?? null;
        
        if ($userData) {
            $userData['company_id'] = $company->id;
            $userData['role_id'] = $role->id;
            $userData['department_id'] = $department->id;
        }

        return $userData;
    }

    private function printLoginCredentials(): void
    {
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin', 'superadmin@margadarsi.in', 'password123'],
                ['Admin', 'admin@margadarsi.in', 'password123'],
                ['Project Manager', 'vikram.singh@margadarsi.in', 'password123'],
                ['Sr. Sales Exec', 'priya.sharma@margadarsi.in', 'password123'],
                ['Sales Executive', 'rajesh.kumar@margadarsi.in', 'password123'],
                ['Team Lead', 'sneha.reddy@margadarsi.in', 'password123'],
                ['Telecaller', 'rahul.verma@margadarsi.in', 'password123'],
            ]
        );

        $this->command->newLine();
        $this->command->warn('⚠️  Default password for all users: password123');
    }
}
