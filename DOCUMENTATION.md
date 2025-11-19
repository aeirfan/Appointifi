# Appointifi - Appointment Booking System Documentation

## Table of Contents
1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Database Schema](#database-schema)
4. [User Roles and Permissions](#user-roles-and-permissions)
5. [Core Features](#core-features)
6. [API Endpoints](#api-endpoints)
7. [User Flows](#user-flows)
8. [Business Logic](#business-logic)
9. [UI/UX Design](#uix-design)
10. [Installation](#installation)

## Overview

Appointifi is a Laravel-based appointment booking system that connects customers with businesses offering services. The system allows customers to browse businesses, check service availability, and book appointments. Business owners can manage their profiles, services, appointments, and scheduling constraints.

### Key Features
- Role-based access control (customers vs business owners)
- Availability calculation considering business hours, holidays, and recurring blocks
- Appointment booking with confirmation and cancellation
- Location-based business search
- Responsive UI with dark mode support

## Architecture

### Tech Stack
- **Backend**: Laravel 12
- **Frontend**: Blade templates, Tailwind CSS, Alpine.js
- **Livewire**: For dynamic UI components
- **Database**: MySQL/PostgreSQL/SQLite
- **Asset Bundling**: Vite

### Project Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── Booking/          # Customer-facing booking features
│   │   └── Business/         # Business owner features
│   ├── Middleware/
├── Models/                   # Eloquent models
├── Services/                 # Business logic services
├── Events/                   # Domain events
├── Listeners/                # Event listeners
└── Livewire/                 # Livewire components
```

### Service Layer
The system uses service classes to encapsulate complex business logic:
- `AvailabilityService` - Calculates available time slots
- `BookingActionService` - Handles booking creation with pessimistic locking
- `SearchService` - Handles business search and geolocation
- `GeocodingService` - Converts addresses to coordinates
- `OpeningHoursBuilder` - Manages business opening hours

## Database Schema

### Users Table
Stores user accounts for both customers and business owners.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (PK) | Primary key |
| name | VARCHAR(255) | User's display name |
| email | VARCHAR(255) | User's email (unique) |
| password | VARCHAR(255) | Hashed password |
| role | VARCHAR(20) | 'customer' or 'owner' |
| email_verified_at | TIMESTAMP | Email verification timestamp |
| remember_token | VARCHAR(100) | Remember token for "Remember Me" |
| created_at | TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | Record update time |

### Businesses Table
Stores business information.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (PK) | Primary key |
| owner_id | BIGINT (FK) | Foreign key to users table |
| name | VARCHAR(255) | Business name |
| description | TEXT | Business description |
| created_at | TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | Record update time |

### Locations Table
Stores business locations and opening hours.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (PK) | Primary key |
| business_id | BIGINT (FK) | Foreign key to businesses table |
| address | VARCHAR(255) | Business address |
| city | VARCHAR(100) | Business city |
| latitude | DECIMAL(10,7) | Latitude for geolocation (nullable) |
| longitude | DECIMAL(10,7) | Longitude for geolocation (nullable) |
| opening_hours | JSON | Business hours per weekday |
| created_at | TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | Record update time |

### Services Table
Stores services offered by businesses.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (PK) | Primary key |
| business_id | BIGINT (FK) | Foreign key to businesses table |
| name | VARCHAR(255) | Service name |
| description | TEXT | Service description |
| duration | INT | Service duration in minutes |
| price | DECIMAL(8,2) | Service price (nullable) |
| created_at | TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | Record update time |

### Appointments Table
Stores appointment bookings.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (PK) | Primary key |
| customer_id | BIGINT (FK) | Foreign key to users table (customer) |
| business_id | BIGINT (FK) | Foreign key to businesses table |
| service_id | BIGINT (FK) | Foreign key to services table |
| start_time | DATETIME | Appointment start time |
| end_time | DATETIME | Appointment end time |
| status | ENUM | 'confirmed', 'arrival', 'completed', 'cancelled', 'no_show' |
| created_at | TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | Record update time |

### Holidays Table
Stores business holidays when operation is closed.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (PK) | Primary key |
| business_id | BIGINT (FK) | Foreign key to businesses table |
| date | DATE | Holiday date |
| name | VARCHAR(255) | Holiday name (nullable) |
| created_at | TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | Record update time |

### Recurring Blocked Times Table
Stores recurring time blocks when business is unavailable.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (PK) | Primary key |
| business_id | BIGINT (FK) | Foreign key to businesses table |
| title | VARCHAR(255) | Block title (nullable) |
| start_time | TIME | Start time of the block |
| end_time | TIME | End time of the block |
| days_of_week | JSON | Days of the week as array (e.g., ["monday", "tuesday"]) |
| created_at | TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | Record update time |

## User Roles and Permissions

### Customer Role
- **Role Value**: `customer`
- **Access**:
  - Browse and search businesses
  - Check service availability
  - Book appointments
  - View/cancel personal appointments
  - Update personal profile
- **Restricted Access**:
  - Cannot access business management features

### Business Owner Role
- **Role Value**: `owner`
- **Access**:
  - Create and manage business profile
  - Manage services
  - View and update appointments
  - Set business hours
  - Add recurring blocked times and holidays
  - Access business dashboard
- **Restricted Access**:
  - Cannot book appointments for their own business

### Middleware
- `CheckRoleOwner` - Ensures only users with role 'owner' can access business routes
- Laravel's built-in `auth` middleware for authentication

## Core Features

### 1. Business Discovery
- Search businesses by name or location
- Location-based search with geocoding
- Sorting options (name, distance)

### 2. Availability Management
- Calculate available slots considering:
  - Business opening hours
  - Existing appointments
  - Recurring blocked times
  - Holidays
- Minimum booking time (30 minutes from now)
- Service duration consideration

### 3. Booking System
- Pessimistic locking to prevent double-bookings
- Status tracking (confirmed, arrival, completed, cancelled, no_show)
- Cancellation for future appointments only

### 4. Business Management
- Profile creation and editing
- Service management (CRUD)
- Appointment management
- Schedule management (hours, recurring blocks, holidays)

## API Endpoints

### Authentication Routes
```
GET  /login              - Login form
POST /login              - Authenticate user
GET  /register           - Registration form
POST /register           - Create new user
POST /logout             - Log out user
```

### Customer Routes (auth middleware)
```
GET    /businesses                           - Browse businesses
GET    /businesses/{business}                - View business details
GET    /businesses/{business}/services/{service}/availability - Check availability
POST   /bookings                             - Create booking
GET    /my-bookings                          - View personal bookings
PATCH  /bookings/{appointment}/cancel        - Cancel appointment
GET    /customer/dashboard                   - Customer dashboard
```

### Business Owner Routes (auth + owner middleware)
```
GET    /business/dashboard                           - Business dashboard
GET    /business/profile/create                      - Create business profile form
POST   /business/profile                           - Create business profile
GET    /business/profile/edit                      - Edit business profile form
PATCH  /business/profile                           - Update business profile
GET    /business/location/edit                     - Edit location form
PATCH  /business/location                          - Update location
POST   /business/recurring-blocks                  - Add recurring block
DELETE /business/recurring-blocks/{id}             - Delete recurring block
POST   /business/holidays                          - Add holiday
DELETE /business/holidays/{id}                     - Delete holiday
GET    /business/appointments                      - View appointments
PATCH  /business/appointments/{appointment}/status - Update appointment status
GET    /business/services                          - View services
POST   /business/services                          - Create service
GET    /business/services/create                   - Create service form
PUT    /business/services/{service}                - Update service
DELETE /business/services/{service}                - Delete service
```

### Profile Routes
```
GET    /profile        - Edit profile form
PATCH  /profile        - Update profile
DELETE /profile        - Delete account
```

## User Flows

### Customer Flow
1. **Registration**: User registers with role 'customer'
2. **Business Discovery**: Browse/search businesses
3. **Service Selection**: Select business → Choose service → Check availability
4. **Booking**: Select time slot → Confirm booking
5. **Management**: View/cancel appointments in 'my-bookings'

### Business Owner Flow
1. **Registration**: User registers with role 'owner'
2. **Profile Setup**: Create business profile with location and hours
3. **Service Setup**: Create services offered by the business
4. **Operations**: Manage appointments and schedule constraints

## Business Logic

### Availability Calculation
The `AvailabilityService` calculates available time slots by:
1. Checking business opening hours for the day
2. Identifying all booked appointments on the date
3. Identifying recurring blocked times for the day of week
4. Checking if the date is a holiday
5. Generating time slots in intervals based on service duration
6. Filtering out slots that conflict with existing bookings or blocks
7. Excluding slots that are not at least 30 minutes in the future

### Booking Validation
The `BookingActionService` ensures:
1. Slot is available (no overlapping appointments)
2. Business is open on the selected date
3. Time is in the future
4. Pessimistic locking to prevent race conditions between concurrent bookings

### Appointment Status Flow
- **confirmed**: Initial state when booking is made
- **arrival**: Customer has arrived (set by business owner)
- **completed**: Appointment completed (set by business owner)
- **cancelled**: Appointment cancelled by customer
- **no_show**: Customer didn't show up (set by business owner)

## UI/UX Design

### Responsive Design
- Mobile-first approach with hamburger menu for navigation
- Desktop sidebar navigation for better access to features
- Responsive forms and tables

### Dark Mode
- Toggle switch in the sidebar
- Persists across sessions using localStorage
- Built with Tailwind's dark mode support

### Role-Based UI
- Different sidebar navigation based on user role
- Owner: 5 main navigation items (Home, Dashboard, Appointments, Services, Profile)
- Customer: 3 main navigation items (Home, My Appointments, Book New)

### Accessibility
- Proper contrast ratios for text
- Semantic HTML structure
- Keyboard navigation support
- Screen reader compatibility

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js and npm
- Database (MySQL, PostgreSQL, or SQLite)

### Steps
1. Clone the repository
2. Install PHP dependencies: `composer install`
3. Install Node.js dependencies: `npm install`
4. Create environment file: `cp .env.example .env`
5. Generate application key: `php artisan key:generate`
6. Configure database settings in .env
7. Run database migrations: `php artisan migrate`
8. Build assets: `npm run build` (or `npm run dev` for development)
9. Start the server: `php artisan serve`

### Development Setup
For development, run `npm run dev` in one terminal to watch and rebuild assets, and `php artisan serve` in another terminal to run the Laravel server.

### Testing
Run tests with: `php artisan test`

### Environment Variables
Key environment variables include:
- `APP_NAME` - Application name
- `APP_ENV` - Environment (local, production, etc.)
- `APP_KEY` - Encryption key
- `DB_*` - Database connection settings
- `MAIL_*` - Email configuration (for notifications)