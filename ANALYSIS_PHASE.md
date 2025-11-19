# Appointifi - Analysis Phase

## User Requirement Analysis

### 1. Roles to be Implemented

#### Primary Roles:
- **Customer**: A person who books appointments with businesses
- **Business Owner**: A person who manages a business and its appointments

#### Role Characteristics:
- **Customer**:
  - Can browse businesses and services
  - Can book appointments
  - Can view and manage their own appointments
  - Cannot access business management features

- **Business Owner**:
  - Can create and manage their business profile
  - Can manage services offered
  - Can manage appointments for their business
  - Can set business hours, holidays, and recurring blocked times
  - Cannot book appointments for their own business

### 2. User Flow for Different Roles

#### Customer User Flow:
1. **Registration/Login**: User registers with role 'customer' or logs in
2. **Business Search**: Browse or search for businesses using filters
3. **Service Selection**: View business details and select a service
4. **Availability Check**: Check available time slots for selected service
5. **Booking**: Select time slot and confirm appointment
6. **Appointment Management**: View personal bookings, cancel future appointments if needed
7. **Profile Management**: Update personal information

#### Business Owner User Flow:
1. **Registration/Login**: User registers with role 'owner' or logs in
2. **Business Setup**: Create business profile with name, description, location, and hours
3. **Service Management**: Create, edit, and delete services offered
4. **Appointment Management**: View, update status, and manage all appointments
5. **Schedule Management**: Set recurring blocked times and holidays
6. **Profile Management**: Update business information and location details

### 3. Use Cases for Different Roles

#### Customer Use Cases:
- **UC-001**: Browse available businesses
- **UC-002**: Search businesses by name or location
- **UC-003**: View business details and services
- **UC-004**: Check service availability for a specific date
- **UC-005**: Book an appointment with a service
- **UC-006**: View personal appointment history
- **UC-007**: Cancel a future appointment
- **UC-008**: Update personal profile information
- **UC-009**: Receive appointment confirmation notifications

#### Business Owner Use Cases:
- **UC-101**: Create business profile
- **UC-102**: Update business profile (name, description, location)
- **UC-103**: Set business opening hours for each day of the week
- **UC-104**: Create services (name, description, duration, price)
- **UC-105**: Update or delete services
- **UC-106**: View all appointments for the business
- **UC-107**: Update appointment status (confirmed → arrival → completed, or cancelled)
- **UC-108**: Add recurring blocked times (e.g., lunch breaks)
- **UC-109**: Add holidays to block appointments on specific dates
- **UC-110**: Receive booking notifications
- **UC-111**: View business dashboard with appointment statistics

### 4. System Flow

#### Registration and Authentication Flow:
1. User visits landing page
2. User chooses to register or login
3. For registration: user selects role (customer or owner)
4. User account is created with assigned role
5. User is authenticated and redirected based on role

#### Customer Booking Flow:
1. Customer browses/searches for businesses
2. Customer selects a business and views available services
3. Customer selects a service and checks availability on chosen date
4. System calculates available time slots considering:
   - Business opening hours
   - Existing appointments
   - Recurring blocked times
   - Holidays
5. Customer selects available time slot
6. System validates availability using pessimistic locking
7. Appointment is created with 'confirmed' status
8. Confirmation is sent to customer

#### Business Management Flow:
1. Business owner creates profile with business information
2. Owner sets location and opening hours
3. Owner creates services with duration and pricing
4. System manages appointment scheduling based on constraints
5. Owner can update appointment statuses through dashboard
6. Owner can set recurring blocks and holidays to manage availability

#### Data Flow:
1. User input is validated at the request level
2. Business logic is processed through service classes
3. Database operations are performed with appropriate constraints
4. Responses are formatted for the appropriate view
5. Events may be triggered for notifications or logging

### 5. Database Schema

#### Core Tables:
1. **users** - Stores user accounts
   - id (Primary Key)
   - name (string)
   - email (string, unique)
   - password (hashed string)
   - role (string: 'customer' or 'owner')
   - timestamps

2. **businesses** - Stores business information
   - id (Primary Key)
   - owner_id (Foreign Key to users)
   - name (string)
   - description (text, nullable)
   - timestamps

3. **locations** - Stores business location and hours
   - id (Primary Key)
   - business_id (Foreign Key to businesses)
   - address (string)
   - city (string)
   - latitude (decimal, nullable)
   - longitude (decimal, nullable)
   - opening_hours (JSON format)
   - timestamps

4. **services** - Stores services offered by businesses
   - id (Primary Key)
   - business_id (Foreign Key to businesses)
   - name (string)
   - description (text, nullable)
   - duration (integer in minutes)
   - price (decimal, nullable)
   - timestamps

5. **appointments** - Stores appointment bookings
   - id (Primary Key)
   - customer_id (Foreign Key to users)
   - business_id (Foreign Key to businesses)
   - service_id (Foreign Key to services)
   - start_time (datetime)
   - end_time (datetime)
   - status (enum: confirmed, arrival, completed, cancelled, no_show)
   - timestamps

6. **holidays** - Stores business holidays
   - id (Primary Key)
   - business_id (Foreign Key to businesses)
   - date (date)
   - name (string, nullable)
   - timestamps

7. **recurring_blocked_times** - Stores recurring unavailable times
   - id (Primary Key)
   - business_id (Foreign Key to businesses)
   - title (string, nullable)
   - start_time (time)
   - end_time (time)
   - days_of_week (JSON array)
   - timestamps

### 6. Relationships Between Tables

#### Direct Relationships:
- `users (1)` → `businesses (m)` via `owner_id` (One user owns one business - One-to-One)
- `users (1)` → `appointments (m)` via `customer_id` (One user has many appointments as customer - One-to-Many)
- `businesses (1)` → `locations (m)` via `business_id` (One business has many locations - One-to-Many)
- `businesses (1)` → `services (m)` via `business_id` (One business offers many services - One-to-Many)
- `businesses (1)` → `appointments (m)` via `business_id` (One business has many appointments - One-to-Many)
- `businesses (1)` → `holidays (m)` via `business_id` (One business has many holidays - One-to-Many)
- `businesses (1)` → `recurring_blocked_times (m)` via `business_id` (One business has many recurring blocks - One-to-Many)
- `services (1)` → `appointments (m)` via `service_id` (One service has many appointments - One-to-Many)
- `locations (1)` → `businesses (m)` via `business_id` (One business belongs to one location - Many-to-One)

#### Relationship Constraints:
- Foreign key constraints with cascading deletes where appropriate
- User deletion cascades their owned business (and all related data)
- Appointment customer relationship maintained separately from business ownership

### 7. UI/UX Design

#### User Interface Elements:
- **Responsive Design**: Mobile-first approach with hamburger menu for mobile navigation
- **Role-Based Navigation**: Different sidebar menus for customers vs business owners
- **Dark Mode Support**: Toggle switch with persistent user preference
- **Form Validation**: Real-time validation with clear error messaging
- **Loading States**: Visual feedback during data processing
- **Status Indicators**: Clear visual indicators for appointment statuses

#### User Experience Features:
- **Intuitive Navigation**: Consistent sidebar navigation based on user role
- **Search Functionality**: Business search with location-based filtering
- **Availability Display**: Clear time slot visualization with booking options
- **Appointment Management**: Tabbed interface for different appointment statuses
- **Profile Management**: Unified interface for business information editing
- **Accessibility**: Keyboard navigation and screen reader compatibility

#### Role-Specific UI:
- **Customer Dashboard**: Focused on appointment booking and management
- **Business Owner Dashboard**: Focused on operations and appointment management
- **Unified Profile Edit**: Combined interface for business information, hours, and scheduling constraints
- **Booking Flow**: Streamlined process from business selection to appointment confirmation

#### Design System:
- **Tailwind CSS**: For consistent styling and responsive layouts
- **Livewire Components**: For dynamic UI without full page reloads
- **Alpine.js**: For client-side interactivity where needed
- **Consistent Color Palette**: Primary colors that can be customized per branding