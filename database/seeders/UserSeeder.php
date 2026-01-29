<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting User Seeding Process...');

        // Step 1: Get or create demo company
        $company = Company::firstOrCreate(
            ['email' => 'admin@margadarsi.com'],
            [
                'name' => 'Margadarsi Infra Pvt Ltd',
                'legal_name' => 'Margadarsi Infrastructure Private Limited',
                'pan_number' => 'AABCM1234A',
                'gstin' => '36AABCM1234A1Z5',
                'cin' => 'U70100TG2010PTC068123',
                'rera_number' => 'P52100012345',
                'phone' => '9876543210',
                'email' => 'admin@margadarsi.com',
                'website' => 'https://margadarsi.com',
                'registered_address_line1' => 'Plot No 123, KPHB Colony',
                'registered_city' => 'Hyderabad',
                'registered_state' => 'Telangana',
                'registered_pincode' => '500072',
                'registered_country' => 'India',
                'is_active' => true,
            ]
        );

        $this->command->info("✅ Company: {$company->name}");

        // Step 2: Seed permissions if not already seeded
        if (Permission::count() === 0) {
            Permission::seedPermissions();
            $this->command->info('✅ Permissions seeded (25 permissions)');
        } else {
            $this->command->info('✅ Permissions already exist (25 permissions)');
        }

        // Step 3: Seed system roles for company
        $roles = $this->seedSystemRoles($company);
        $this->command->info('✅ System roles created (7 roles)');

        // Step 4: Assign permissions to roles
        $this->assignPermissionsToRoles($roles);
        $this->command->info('✅ Permissions assigned to roles');

        // Step 5: Create users for each role
        $users = $this->createUsers($company, $roles);
        $this->command->info('✅ Users created (7 users)');

        // Step 6: Create sample projects and assign users
        $this->createProjectsAndAssignUsers($company, $users);
        $this->command->info('✅ Projects created and users assigned');

        $this->command->newLine();
        $this->command->info('🎉 User seeding completed successfully!');
        $this->command->newLine();
        $this->printLoginCredentials();
    }

    /**
     * Seed system roles for the company.
     */
    private function seedSystemRoles(Company $company): array
    {
        $systemRoles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'hierarchy_level' => 1, 'is_system' => true],
            ['name' => 'Admin', 'slug' => 'admin', 'hierarchy_level' => 2, 'is_system' => true],
            ['name' => 'Sales Manager', 'slug' => 'sales-manager', 'hierarchy_level' => 3, 'is_system' => true],
            ['name' => 'Senior Sales Executive', 'slug' => 'senior-sales-executive', 'hierarchy_level' => 4, 'is_system' => true],
            ['name' => 'Sales Executive', 'slug' => 'sales-executive', 'hierarchy_level' => 5, 'is_system' => true],
            ['name' => 'Team Leader', 'slug' => 'team-leader', 'hierarchy_level' => 6, 'is_system' => true],
            ['name' => 'Tele Caller', 'slug' => 'tele-caller', 'hierarchy_level' => 7, 'is_system' => true],
        ];

        $roles = [];
        foreach ($systemRoles as $roleData) {
            $roles[$roleData['slug']] = Role::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'slug' => $roleData['slug'],
                ],
                [
                    'name' => $roleData['name'],
                    'hierarchy_level' => $roleData['hierarchy_level'],
                    'is_system' => $roleData['is_system'],
                ]
            );
        }

        return $roles;
    }

    /**
     * Assign permissions to roles based on the permission matrix.
     */
    private function assignPermissionsToRoles(array $roles): void
    {
        $permissions = Permission::all()->keyBy('name');

        // Super Admin - ALL permissions
        $roles['super-admin']->permissions()->sync($permissions->pluck('id'));

        // Admin - All except force-delete
        $adminPermissions = $permissions->reject(function ($perm) {
            return str_ends_with($perm->name, '.force-delete');
        });
        $roles['admin']->permissions()->sync($adminPermissions->pluck('id'));

        // Sales Manager - Project-focused
        $salesManagerPerms = [
            'companies.view',
            'projects.view',
            'projects.create',
            'projects.update',
            'projects.delete',
            'projects.restore',
            'projects.manage-specifications',
            'roles.view',
            'users.view',
            'users.create',
            'users.update',
            'users.assign-projects',
        ];
        $roles['sales-manager']->permissions()->sync(
            $permissions->whereIn('name', $salesManagerPerms)->pluck('id')
        );

        // Senior Sales Executive - Limited updates
        $srSalesPerms = [
            'companies.view',
            'projects.view',
            'projects.update',
            'projects.manage-specifications',
            'roles.view',
            'users.view',
        ];
        $roles['senior-sales-executive']->permissions()->sync(
            $permissions->whereIn('name', $srSalesPerms)->pluck('id')
        );

        // Sales Executive, Team Leader, Tele Caller - View only
        $viewOnlyPerms = ['companies.view', 'projects.view', 'roles.view', 'users.view'];
        foreach (['sales-executive', 'team-leader', 'tele-caller'] as $slug) {
            $roles[$slug]->permissions()->sync(
                $permissions->whereIn('name', $viewOnlyPerms)->pluck('id')
            );
        }
    }

    /**
     * Create users for each role.
     */
    private function createUsers(Company $company, array $roles): array
    {
        $usersData = [
            [
                'role' => 'super-admin',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'superadmin@margadarsi.com',
                'phone' => '9876543210',
            ],
            [
                'role' => 'admin',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@margadarsi.com',
                'phone' => '9876543211',
            ],
            [
                'role' => 'sales-manager',
                'first_name' => 'Rajesh',
                'last_name' => 'Kumar',
                'email' => 'rajesh.kumar@margadarsi.com',
                'phone' => '9876543212',
            ],
            [
                'role' => 'senior-sales-executive',
                'first_name' => 'Priya',
                'last_name' => 'Sharma',
                'email' => 'priya.sharma@margadarsi.com',
                'phone' => '9876543213',
            ],
            [
                'role' => 'sales-executive',
                'first_name' => 'Amit',
                'last_name' => 'Patel',
                'email' => 'amit.patel@margadarsi.com',
                'phone' => '9876543214',
            ],
            [
                'role' => 'team-leader',
                'first_name' => 'Sneha',
                'last_name' => 'Reddy',
                'email' => 'sneha.reddy@margadarsi.com',
                'phone' => '9876543215',
            ],
            [
                'role' => 'tele-caller',
                'first_name' => 'Rahul',
                'last_name' => 'Verma',
                'email' => 'rahul.verma@margadarsi.com',
                'phone' => '9876543216',
            ],
        ];

        $users = [];
        foreach ($usersData as $userData) {
            $roleSlug = $userData['role'];
            unset($userData['role']);

            $users[$roleSlug] = User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'company_id' => $company->id,
                    'role_id' => $roles[$roleSlug]->id,
                    'password' => 'password123', // Will be hashed by model
                    'is_active' => true,
                    'email_verified_at' => now(),
                ])
            );
        }

        return $users;
    }

    /**
     * Create sample projects and assign users.
     */
    private function createProjectsAndAssignUsers(Company $company, array $users): void
    {
        // Create 3 sample projects
        $project1 = Project::firstOrCreate(
            [
                'company_id' => $company->id,
                'slug' => 'margadarsi-heights',
            ],
            [
                'name' => 'Margadarsi Heights',
                'type' => 'residential',
                'status' => 'ongoing',
                'description' => 'Premium residential apartments in the heart of Hyderabad',
                'address_line1' => 'Survey No. 45, Gachibowli',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
                'pincode' => '500081',
                'is_featured' => true,
            ]
        );

        $project2 = Project::firstOrCreate(
            [
                'company_id' => $company->id,
                'slug' => 'margadarsi-tech-park',
            ],
            [
                'name' => 'Margadarsi Tech Park',
                'type' => 'commercial',
                'status' => 'upcoming',
                'description' => 'Modern commercial spaces for IT companies',
                'address_line1' => 'Plot 123, Hitech City Road',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
                'pincode' => '500032',
                'is_featured' => false,
            ]
        );

        $project3 = Project::firstOrCreate(
            [
                'company_id' => $company->id,
                'slug' => 'margadarsi-villas',
            ],
            [
                'name' => 'Margadarsi Villas',
                'type' => 'villa',
                'status' => 'completed',
                'description' => 'Luxury villas with modern amenities',
                'address_line1' => 'Shankarpally Road',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
                'pincode' => '500084',
                'is_featured' => true,
            ]
        );

        // Assign users to projects
        // Super Admin & Admin - All projects
        foreach (['super-admin', 'admin'] as $role) {
            $users[$role]->projects()->syncWithoutDetaching([
                $project1->id => ['assigned_at' => now()],
                $project2->id => ['assigned_at' => now()],
                $project3->id => ['assigned_at' => now()],
            ]);
        }

        // Sales Manager - Project 1 & 2
        $users['sales-manager']->projects()->syncWithoutDetaching([
            $project1->id => ['assigned_at' => now(), 'assigned_by' => $users['admin']->id],
            $project2->id => ['assigned_at' => now(), 'assigned_by' => $users['admin']->id],
        ]);

        // Sr Sales Executive - Project 1
        $users['senior-sales-executive']->projects()->syncWithoutDetaching([
            $project1->id => ['assigned_at' => now(), 'assigned_by' => $users['sales-manager']->id],
        ]);

        // Sales Executive - Project 1
        $users['sales-executive']->projects()->syncWithoutDetaching([
            $project1->id => ['assigned_at' => now(), 'assigned_by' => $users['sales-manager']->id],
        ]);

        // Team Leader - Project 2
        $users['team-leader']->projects()->syncWithoutDetaching([
            $project2->id => ['assigned_at' => now(), 'assigned_by' => $users['sales-manager']->id],
        ]);

        // Tele Caller - Project 1
        $users['tele-caller']->projects()->syncWithoutDetaching([
            $project1->id => ['assigned_at' => now(), 'assigned_by' => $users['team-leader']->id],
        ]);
    }

    /**
     * Print login credentials for all users.
     */
    private function printLoginCredentials(): void
    {
        $this->command->table(
            ['Role', 'Email', 'Password', 'Projects'],
            [
                ['Super Admin', 'superadmin@margadarsi.com', 'password123', '3 (All)'],
                ['Admin', 'admin@margadarsi.com', 'password123', '3 (All)'],
                ['Sales Manager', 'rajesh.kumar@margadarsi.com', 'password123', '2 (Heights, Tech Park)'],
                ['Sr Sales Executive', 'priya.sharma@margadarsi.com', 'password123', '1 (Heights)'],
                ['Sales Executive', 'amit.patel@margadarsi.com', 'password123', '1 (Heights)'],
                ['Team Leader', 'sneha.reddy@margadarsi.com', 'password123', '1 (Tech Park)'],
                ['Tele Caller', 'rahul.verma@margadarsi.com', 'password123', '1 (Heights)'],
            ]
        );

        $this->command->newLine();
        $this->command->warn('⚠️  Default password for all users: password123');
        $this->command->info('💡 Users can reset their password using the forgot-password flow');
    }
}
