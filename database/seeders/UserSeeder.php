<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
     * Seed system roles for the company using Role::SYSTEM_ROLES.
     */
    private function seedSystemRoles(Company $company): array
    {
        $roles = [];
        foreach (Role::SYSTEM_ROLES as $roleData) {
            $roles[$roleData['slug']] = Role::updateOrCreate(
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
                ]
            );
        }

        return $roles;
    }

    /**
     * Assign permissions to roles based on Permission::ROLE_PERMISSION_MATRIX.
     */
    private function assignPermissionsToRoles(array $roles): void
    {
        $permissions = Permission::all()->keyBy('name');

        foreach ($roles as $slug => $role) {
            $permissionNames = Permission::ROLE_PERMISSION_MATRIX[$slug] ?? [];
            
            if (empty($permissionNames)) {
                continue;
            }

            $permissionIds = $permissions->whereIn('name', $permissionNames)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }

    /**
     * Create users for each role with reporting hierarchy.
     */
    private function createUsers(Company $company, array $roles): array
    {
        $usersData = [
            [
                'role' => 'super_admin',
                'first_name' => 'Ashish Kr.',
                'last_name' => 'Yadav',
                'email' => 'intelliflowpvtltd@gmail.com',
                'phone' => '9876543210',
                'employee_code' => 'EMP001',
                'designation' => 'System Administrator',
                'department' => 'IT',
                'reports_to' => null,
            ],
            [
                'role' => 'admin',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@margadarsi.com',
                'phone' => '9876543211',
                'employee_code' => 'EMP002',
                'designation' => 'Company Administrator',
                'department' => 'Administration',
                'reports_to' => 'super_admin',
            ],
            [
                'role' => 'sales_director',
                'first_name' => 'Vikram',
                'last_name' => 'Singh',
                'email' => 'vikram.singh@margadarsi.com',
                'phone' => '9876543212',
                'employee_code' => 'EMP003',
                'designation' => 'Sales Director',
                'department' => 'Sales',
                'reports_to' => 'admin',
            ],
            [
                'role' => 'sales_manager',
                'first_name' => 'Rajesh',
                'last_name' => 'Kumar',
                'email' => 'rajesh.kumar@margadarsi.com',
                'phone' => '9876543213',
                'employee_code' => 'EMP004',
                'designation' => 'Sales Manager',
                'department' => 'Sales',
                'reports_to' => 'sales_director',
            ],
            [
                'role' => 'project_manager',
                'first_name' => 'Priya',
                'last_name' => 'Sharma',
                'email' => 'priya.sharma@margadarsi.com',
                'phone' => '9876543214',
                'employee_code' => 'EMP005',
                'designation' => 'Project Manager',
                'department' => 'Projects',
                'reports_to' => 'sales_director',
            ],
            [
                'role' => 'team_lead',
                'first_name' => 'Sneha',
                'last_name' => 'Reddy',
                'email' => 'sneha.reddy@margadarsi.com',
                'phone' => '9876543215',
                'employee_code' => 'EMP006',
                'designation' => 'Team Lead',
                'department' => 'Sales',
                'reports_to' => 'sales_manager',
            ],
            [
                'role' => 'telecaller',
                'first_name' => 'Rahul',
                'last_name' => 'Verma',
                'email' => 'rahul.verma@margadarsi.com',
                'phone' => '9876543216',
                'employee_code' => 'EMP007',
                'designation' => 'Telecaller',
                'department' => 'Sales',
                'reports_to' => 'team_lead',
            ],
            [
                'role' => 'channel_partner',
                'first_name' => 'Amit',
                'last_name' => 'Patel',
                'email' => 'amit.patel@margadarsi.com',
                'phone' => '9876543217',
                'employee_code' => 'CP001',
                'designation' => 'Channel Partner',
                'department' => 'External',
                'reports_to' => 'sales_manager',
            ],
        ];

        $users = [];
        $reportsToMapping = [];
        
        // First pass: create all users without reports_to
        foreach ($usersData as $userData) {
            $roleSlug = $userData['role'];
            $reportsToSlug = $userData['reports_to'];
            unset($userData['role'], $userData['reports_to']);

            $users[$roleSlug] = User::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'email' => $userData['email']
                ],
                array_merge($userData, [
                    'role_id' => $roles[$roleSlug]->id,
                    'password' => $roleSlug === 'super_admin' ? Hash::make('Ashish@7890') : Hash::make('password123'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ])
            );
            
            // Store reports_to slug for second pass
            if ($reportsToSlug) {
                $reportsToMapping[$roleSlug] = $reportsToSlug;
            }
        }

        // Second pass: set up reporting hierarchy
        foreach ($reportsToMapping as $slug => $managerSlug) {
            if (isset($users[$slug]) && isset($users[$managerSlug])) {
                $users[$slug]->reports_to = $users[$managerSlug]->id;
                $users[$slug]->save();
            }
        }

        return $users;
    }

    /**
     * Create sample projects and assign users with access levels.
     */
    private function createProjectsAndAssignUsers(Company $company, array $users): void
    {
        // Create 3 sample projects
        $project1 = Project::updateOrCreate(
            [
                'slug' => 'margadarsi-heights',
            ],
            [
                'company_id' => $company->id,
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

        $project2 = Project::updateOrCreate(
            [
                'slug' => 'margadarsi-tech-park',
            ],
            [
                'company_id' => $company->id,
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

        $project3 = Project::updateOrCreate(
            [
                'slug' => 'margadarsi-villas',
            ],
            [
                'company_id' => $company->id,
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

        // Assign users to projects with access levels
        // Super Admin & Admin - All projects (manager level)
        foreach (['super_admin', 'admin'] as $role) {
            $users[$role]->projects()->syncWithoutDetaching([
                $project1->id => ['access_level' => 'manager', 'assigned_at' => now()],
                $project2->id => ['access_level' => 'manager', 'assigned_at' => now()],
                $project3->id => ['access_level' => 'manager', 'assigned_at' => now()],
            ]);
        }

        // Sales Director - All projects (viewer)
        $users['sales_director']->projects()->syncWithoutDetaching([
            $project1->id => ['access_level' => 'viewer', 'assigned_at' => now(), 'assigned_by' => $users['admin']->id],
            $project2->id => ['access_level' => 'viewer', 'assigned_at' => now(), 'assigned_by' => $users['admin']->id],
            $project3->id => ['access_level' => 'viewer', 'assigned_at' => now(), 'assigned_by' => $users['admin']->id],
        ]);

        // Sales Manager - Project 1 & 2 (manager)
        $users['sales_manager']->projects()->syncWithoutDetaching([
            $project1->id => ['access_level' => 'manager', 'assigned_at' => now(), 'assigned_by' => $users['sales_director']->id],
            $project2->id => ['access_level' => 'manager', 'assigned_at' => now(), 'assigned_by' => $users['sales_director']->id],
        ]);

        // Project Manager - Project 1 (manager)
        $users['project_manager']->projects()->syncWithoutDetaching([
            $project1->id => ['access_level' => 'manager', 'assigned_at' => now(), 'assigned_by' => $users['sales_director']->id],
        ]);

        // Team Lead - Project 1 & 2 (member)
        $users['team_lead']->projects()->syncWithoutDetaching([
            $project1->id => ['access_level' => 'member', 'assigned_at' => now(), 'assigned_by' => $users['sales_manager']->id],
            $project2->id => ['access_level' => 'member', 'assigned_at' => now(), 'assigned_by' => $users['sales_manager']->id],
        ]);

        // Telecaller - Project 1 (member)
        $users['telecaller']->projects()->syncWithoutDetaching([
            $project1->id => ['access_level' => 'member', 'assigned_at' => now(), 'assigned_by' => $users['team_lead']->id],
        ]);

        // Channel Partner - Project 1 (viewer)
        $users['channel_partner']->projects()->syncWithoutDetaching([
            $project1->id => ['access_level' => 'viewer', 'assigned_at' => now(), 'assigned_by' => $users['sales_manager']->id],
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
                ['Super Admin', 'intelliflowpvtltd@gmail.com', 'Ashish@7890', '3 (All)'],
                ['Admin', 'admin@margadarsi.com', 'password123', '3 (All)'],
                ['Sales Director', 'vikram.singh@margadarsi.com', 'password123', '3 (All)'],
                ['Sales Manager', 'rajesh.kumar@margadarsi.com', 'password123', '2 (Heights, Tech Park)'],
                ['Project Manager', 'priya.sharma@margadarsi.com', 'password123', '1 (Heights)'],
                ['Team Lead', 'sneha.reddy@margadarsi.com', 'password123', '2 (Heights, Tech Park)'],
                ['Telecaller', 'rahul.verma@margadarsi.com', 'password123', '1 (Heights)'],
                ['Channel Partner', 'amit.patel@margadarsi.com', 'password123', '1 (Heights)'],
            ]
        );

        $this->command->newLine();
        $this->command->warn('⚠️  Default password for all users: password123');
        $this->command->info('💡 Users can reset their password using the forgot-password flow');
    }
}
