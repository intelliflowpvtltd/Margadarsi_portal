# API Testing Script - Systematic Order

## Prerequisites
```bash
# 1. Start development server
php artisan serve
# Server: http://localhost:8000

# 2. Ensure database is seeded
php artisan migrate:fresh --seed --seeder=UserSeeder
```

---

## Phase 1: Authentication ✅

### 1.1 Login (Super Admin)
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "superadmin@margadarsi.com",
    "password": "password123"
  }'
```

**Expected Response (200):**
```json
{
  "message": "Login successful.",
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "first_name": "Super",
    "last_name": "Admin",
    "email": "superadmin@margadarsi.com"
  },
  "permissions": [27 permissions array]
}
```

**Copy Token for subsequent requests:**
```bash
export TOKEN="paste_token_here"
```

### 1.2 Get Current User
```bash
curl -X GET http://localhost:8000/api/v1/auth/me \
  -H "Authorization: Bearer $TOKEN"
```

### 1.3 Forgot Password Flow

**Step 1: Request OTP**
```bash
curl -X POST http://localhost:8000/api/v1/auth/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email": "superadmin@margadarsi.com"}'
```

**Step 2: Verify OTP (check email for OTP)**
```bash
curl -X POST http://localhost:8000/api/v1/auth/verify-otp \
  -H "Content-Type: application/json" \
  -d '{
    "email": "superadmin@margadarsi.com",
    "otp": "123456"
  }'
```

**Step 3: Reset Password**
```bash
curl -X POST http://localhost:8000/api/v1/auth/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "reset_token": "token_from_step_2",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
  }'
```

---

## Phase 2: Companies CRUD ✅

### 2.1 List Companies
```bash
curl -X GET http://localhost:8000/api/v1/companies \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 2.2 Create Company
```bash
curl -X POST http://localhost:8000/api/v1/companies \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Company Ltd",
    "legal_name": "Test Company Private Limited",
    "pan_number": "AABCT1234B",
    "gstin": "36AABCT1234B1Z5",
    "email": "test@company.com",
    "phone": "9876543210",
    "registered_city": "Mumbai",
    "registered_state": "Maharashtra",
    "registered_pincode": "400001",
    "is_active": true
  }'
```

### 2.3 Show Company
```bash
curl -X GET http://localhost:8000/api/v1/companies/2 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 2.4 Update Company
```bash
curl -X PUT http://localhost:8000/api/v1/companies/2 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Test Company Ltd",
    "phone": "9999999999"
  }'
```

### 2.5 Soft Delete Company
```bash
curl -X DELETE http://localhost:8000/api/v1/companies/2 \
  -H "Authorization: Bearer $TOKEN"
```

### 2.6 Restore Company
```bash
curl -X POST http://localhost:8000/api/v1/companies/2/restore \
  -H "Authorization: Bearer $TOKEN"
```

### 2.7 Force Delete Company
```bash
curl -X DELETE http://localhost:8000/api/v1/companies/2/force \
  -H "Authorization: Bearer $TOKEN"
```

---

## Phase 3: Projects CRUD ✅

### 3.1 List Projects
```bash
curl -X GET http://localhost:8000/api/v1/projects \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 3.2 Create Project
```bash
curl -X POST http://localhost:8000/api/v1/projects \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "company_id": 1,
    "name": "Test Towers",
    "slug": "test-towers",
    "type": "residential",
    "status": "upcoming",
    "city": "Hyderabad",
    "state": "Telangana",
    "pincode": "500001",
    "description": "Premium residential project",
    "is_featured": true,
    "is_active": true
  }'
```

### 3.3 Show Project
```bash
curl -X GET http://localhost:8000/api/v1/projects/4 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 3.4 Update Project
```bash
curl -X PUT http://localhost:8000/api/v1/projects/4 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Test Towers",
    "status": "ongoing"
  }'
```

### 3.5 Update Project Specifications
```bash
curl -X PUT http://localhost:8000/api/v1/projects/4/specifications \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "specifications": {
      "total_units": "500",
      "total_towers": "5",
      "floors_per_tower": "20"
    }
  }'
```

### 3.6 Soft Delete Project
```bash
curl -X DELETE http://localhost:8000/api/v1/projects/4 \
  -H "Authorization: Bearer $TOKEN"
```

### 3.7 Restore Project
```bash
curl -X POST http://localhost:8000/api/v1/projects/4/restore \
  -H "Authorization: Bearer $TOKEN"
```

### 3.8 Force Delete Project
```bash
curl -X DELETE http://localhost:8000/api/v1/projects/4/force \
  -H "Authorization: Bearer $TOKEN"
```

---

## Phase 4: Roles CRUD ✅

### 4.1 List Roles
```bash
curl -X GET http://localhost:8000/api/v1/roles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 4.2 Create Custom Role
```bash
curl -X POST http://localhost:8000/api/v1/roles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "company_id": 1,
    "name": "Customer Support",
    "slug": "customer-support",
    "hierarchy_level": 50,
    "description": "Handles customer queries and support tickets",
    "is_system": false,
    "is_active": true
  }'
```

### 4.3 Show Role
```bash
curl -X GET http://localhost:8000/api/v1/roles/8 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 4.4 Update Role
```bash
curl -X PUT http://localhost:8000/api/v1/roles/8 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Senior Customer Support",
    "hierarchy_level": 45
  }'
```

### 4.5 Soft Delete Role
```bash
curl -X DELETE http://localhost:8000/api/v1/roles/8 \
  -H "Authorization: Bearer $TOKEN"
```

### 4.6 Restore Role
```bash
curl -X POST http://localhost:8000/api/v1/roles/8/restore \
  -H "Authorization: Bearer $TOKEN"
```

### 4.7 Force Delete Role
```bash
curl -X DELETE http://localhost:8000/api/v1/roles/8/force \
  -H "Authorization: Bearer $TOKEN"
```

### 4.8 Seed System Roles (for new company)
```bash
curl -X POST http://localhost:8000/api/v1/roles/seed-system \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"company_id": 1}'
```

---

## Phase 5: Users CRUD ✅

### 5.1 List Users
```bash
curl -X GET http://localhost:8000/api/v1/users \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 5.2 Create User
```bash
curl -X POST http://localhost:8000/api/v1/users \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "company_id": 1,
    "role_id": 5,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@margadarsi.com",
    "phone": "9876543220",
    "password": "password123",
    "password_confirmation": "password123",
    "is_active": true
  }'
```

### 5.3 Show User
```bash
curl -X GET http://localhost:8000/api/v1/users/8 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 5.4 Update User
```bash
curl -X PUT http://localhost:8000/api/v1/users/8 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "Jonathan",
    "phone": "9876543221"
  }'
```

### 5.5 Assign User to Projects
```bash
curl -X POST http://localhost:8000/api/v1/users/8/projects \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "project_ids": [1, 2]
  }'
```

### 5.6 List User Projects
```bash
curl -X GET http://localhost:8000/api/v1/users/8/projects \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 5.7 Remove User from Project
```bash
curl -X DELETE http://localhost:8000/api/v1/users/8/projects/2 \
  -H "Authorization: Bearer $TOKEN"
```

### 5.8 Soft Delete User
```bash
curl -X DELETE http://localhost:8000/api/v1/users/8 \
  -H "Authorization: Bearer $TOKEN"
```

### 5.9 Restore User
```bash
curl -X POST http://localhost:8000/api/v1/users/8/restore \
  -H "Authorization: Bearer $TOKEN"
```

### 5.10 Force Delete User
```bash
curl -X DELETE http://localhost:8000/api/v1/users/8/force \
  -H "Authorization: Bearer $TOKEN"
```

---

## Phase 6: Logout ✅

### 6.1 Logout
```bash
curl -X POST http://localhost:8000/api/v1/auth/logout \
  -H "Authorization: Bearer $TOKEN"
```

---

## Permission Testing ✅

### Test with Different Roles

**Login as Sales Manager:**
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "rajesh.kumar@margadarsi.com",
    "password": "password123"
  }'
```

**Try to create company (should fail with 403):**
```bash
curl -X POST http://localhost:8000/api/v1/companies \
  -H "Authorization: Bearer $SALES_MANAGER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test",
    "registered_city": "Test",
    "registered_state": "Test"
  }'
```

**Expected:** 403 Forbidden
```json
{
  "message": "Unauthorized.",
  "required_permission": "companies.create"
}
```

---

## Checklist

- [ ] Authentication - Login
- [ ] Authentication - Get Me
- [ ] Authentication - Forgot Password Flow
- [ ] Companies - List
- [ ] Companies - Create
- [ ] Companies - Show
- [ ] Companies - Update
- [ ] Companies - Delete
- [ ] Companies - Restore
- [ ] Companies - Force Delete
- [ ] Projects - List
- [ ] Projects - Create
- [ ] Projects - Show
- [ ] Projects - Update
- [ ] Projects - Update Specifications
- [ ] Projects - Delete
- [ ] Projects - Restore
- [ ] Projects - Force Delete
- [ ] Roles - List
- [ ] Roles - Create
- [ ] Roles - Show
- [ ] Roles - Update
- [ ] Roles - Delete
- [ ] Roles - Restore
- [ ] Roles - Force Delete
- [ ] Roles - Seed System
- [ ] Users - List
- [ ] Users - Create
- [ ] Users - Show
- [ ] Users - Update
- [ ] Users - Assign to Projects
- [ ] Users - List Projects
- [ ] Users - Remove from Project
- [ ] Users - Delete
- [ ] Users - Restore
- [ ] Users - Force Delete
- [ ] Authentication - Logout
- [ ] Permission Tests (403 checks)
