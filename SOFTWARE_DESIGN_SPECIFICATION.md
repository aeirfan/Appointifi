# Appointifi - Software Design Specification (SDS)

## Document Control

| Field              | Value                                 |
| ------------------ | ------------------------------------- |
| **Document Title** | Software Design Specification         |
| **Project**        | Appointifi Appointment Booking System |
| **Version**        | 1.0                                   |
| **Date**           | November 20, 2025                     |
| **Status**         | Final                                 |
| **Authors**        | Development Team                      |

---

## Table of Contents

1. [Introduction](#1-introduction)
2. [System Overview](#2-system-overview)
3. [Functional Requirements](#3-functional-requirements)
4. [Non-Functional Requirements](#4-non-functional-requirements)
5. [System Design](#5-system-design)
6. [Database Design](#6-database-design)
7. [Interface Design](#7-interface-design)
8. [Component Design](#8-component-design)
9. [Algorithm Design](#9-algorithm-design)
10. [Security Design](#10-security-design)
11. [Testing Strategy](#11-testing-strategy)
12. [Deployment Design](#12-deployment-design)

---

## 1. Introduction

### 1.1 Purpose

This Software Design Specification (SDS) document describes the design of the Appointifi appointment booking system. It provides a detailed blueprint for developers, testers, and stakeholders to understand the system's architecture, components, and implementation details.

### 1.2 Scope

Appointifi is a web-based platform enabling:

-   **Customers**: Browse businesses, check availability, and book appointments
-   **Business Owners**: Manage profiles, services, schedules, and appointments

### 1.3 Definitions and Acronyms

| Term     | Definition                           |
| -------- | ------------------------------------ |
| **MVP**  | Minimum Viable Product               |
| **RBAC** | Role-Based Access Control            |
| **CRUD** | Create, Read, Update, Delete         |
| **ORM**  | Object-Relational Mapping (Eloquent) |
| **SPA**  | Single Page Application              |
| **SSR**  | Server-Side Rendering                |
| **CSRF** | Cross-Site Request Forgery           |

### 1.4 References

-   Laravel 12 Documentation: https://laravel.com/docs/12.x
-   Tailwind CSS Documentation: https://tailwindcss.com/docs
-   Livewire Documentation: https://livewire.laravel.com/docs
-   OpenStreetMap Nominatim API: https://nominatim.org/release-docs/latest/api/Search/

---

## 2. System Overview

### 2.1 System Context

Appointifi operates as a web application with the following external dependencies:

-   **Database Server**: MySQL/PostgreSQL for data persistence
-   **Geocoding Service**: OpenStreetMap Nominatim for address resolution
-   **Email Service**: SMTP/Mailtrap for notifications
-   **Queue System**: Database/Redis for asynchronous job processing

### 2.2 Design Constraints

-   **Framework**: Laravel 12 (PHP 8.3+)
-   **Browser Support**: Modern browsers (Chrome, Firefox, Safari, Edge)
-   **Responsive Design**: Mobile-first approach
-   **Accessibility**: WCAG 2.1 Level AA compliance (future goal)
-   **No Native Mobile Apps**: Web-only MVP

### 2.3 Assumptions and Dependencies

-   Single location per business (multi-location out of scope)
-   Business hours defined per weekday (no time zone support yet)
-   Email as primary notification channel (SMS excluded)
-   English language only (i18n excluded)

---

## 3. Functional Requirements

### 3.1 Customer Features

#### FR-C1: Business Search

-   **Description**: Search for businesses by name or location
-   **Inputs**: Query string, location string (optional), sort preference
-   **Outputs**: List of businesses with distance (if location provided)
-   **Processing**:
    1. Geocode user location (if provided)
    2. Search businesses by name/description (LIKE query)
    3. Calculate distance using Haversine formula
    4. Sort by name or distance
-   **Validation**: None (open search)

#### FR-C2: View Business Details

-   **Description**: View business profile and available services
-   **Inputs**: Business ID
-   **Outputs**: Business info, location, opening hours, service list
-   **Processing**: Load business with eager-loaded relationships
-   **Validation**: Business must exist

#### FR-C3: Check Availability

-   **Description**: View available time slots for a service on a specific date
-   **Inputs**: Business ID, Service ID, Date
-   **Outputs**: List of available time slots or unavailability reason
-   **Processing**: AvailabilityService algorithm (see Section 9.1)
-   **Validation**:
    -   Service belongs to business
    -   Date is future date

#### FR-C4: Create Booking

-   **Description**: Book an appointment with a business for a service
-   **Inputs**: Business ID, Service ID, Start Time
-   **Outputs**: Confirmation message, email notification
-   **Processing**: BookingActionService with pessimistic locking (see Section 9.2)
-   **Validation**:
    -   Start time is in future
    -   Slot is available
    -   User is authenticated

#### FR-C5: View My Bookings

-   **Description**: View personal booking history segmented by status
-   **Inputs**: Authenticated user
-   **Outputs**: Three collections (Upcoming, Completed, Cancelled)
-   **Processing**: Query appointments with status filters
-   **Validation**: User owns appointments

#### FR-C6: Cancel Booking

-   **Description**: Cancel a future appointment
-   **Inputs**: Appointment ID
-   **Outputs**: Cancellation confirmation, email notification
-   **Processing**: Update status to 'cancelled', fire event
-   **Validation**:
    -   User is appointment owner
    -   Appointment is in future
    -   Status is 'confirmed' or 'arrival'

### 3.2 Business Owner Features

#### FR-B1: Create Business Profile

-   **Description**: Register business with location and opening hours
-   **Inputs**: Name, description, address, city, opening hours (7 days)
-   **Outputs**: Business profile created, geocoded coordinates
-   **Processing**:
    1. Create business record
    2. Geocode address via Nominatim
    3. Create location with opening_hours JSON
-   **Validation**:
    -   Name required
    -   Address and city required
    -   Opening hours valid time format (HH:MM)

#### FR-B2: Update Business Profile

-   **Description**: Edit business details, location, opening hours
-   **Inputs**: Name, description, address, city, opening hours
-   **Outputs**: Updated profile, re-geocoded coordinates
-   **Processing**: Transaction-wrapped update of business + location
-   **Validation**: Same as FR-B1

#### FR-B3: Manage Services (CRUD)

-   **Description**: Create, update, delete services
-   **Inputs**: Name, description, duration (minutes), price
-   **Outputs**: Service list
-   **Processing**: Standard CRUD operations
-   **Validation**:
    -   Name required
    -   Duration >= 5 minutes
    -   Price >= 0
    -   Cannot delete service with future appointments

#### FR-B4: View Appointments

-   **Description**: View all business appointments (upcoming/past)
-   **Inputs**: Authenticated owner
-   **Outputs**: Two lists (Upcoming, Past)
-   **Processing**: Query appointments with time and status filters
-   **Validation**: Owner owns business

#### FR-B5: Update Appointment Status

-   **Description**: Change appointment status (arrival, completed, no_show)
-   **Inputs**: Appointment ID, New Status
-   **Outputs**: Updated appointment
-   **Processing**: Simple status update
-   **Validation**:
    -   Status in allowed values
    -   Owner owns business

#### FR-B6: Manage Holidays

-   **Description**: Add/remove business holidays
-   **Inputs**: Date, Name (optional)
-   **Outputs**: Updated holiday list
-   **Processing**: Create/delete holiday records
-   **Validation**:
    -   Date >= today
    -   Owner owns business

#### FR-B7: Manage Recurring Blocks

-   **Description**: Add/remove recurring blocked times
-   **Inputs**: Title, Start Time, End Time, Days of Week (array)
-   **Outputs**: Updated recurring block list
-   **Processing**: Create/delete recurring block records
-   **Validation**:
    -   End time > start time
    -   Days of week valid (monday-sunday)
    -   Owner owns business

---

## 4. Non-Functional Requirements

### 4.1 Performance Requirements

| Requirement                  | Target      | Measurement               |
| ---------------------------- | ----------- | ------------------------- |
| **Page Load Time**           | < 2 seconds | Time to interactive       |
| **Availability Calculation** | < 500ms     | Server-side processing    |
| **Database Query Response**  | < 100ms     | Average query time        |
| **Concurrent Users**         | 100 users   | Without degradation       |
| **Booking Transaction Time** | < 1 second  | Lock acquisition + commit |

### 4.2 Reliability Requirements

-   **Uptime**: 99.5% availability (MVP target)
-   **Data Backup**: Daily automated backups
-   **Error Recovery**: Graceful failure handling with user-friendly messages
-   **Transaction Integrity**: Zero data loss on booking commits

### 4.3 Security Requirements

-   **Authentication**: Session-based with CSRF protection
-   **Password Storage**: Bcrypt hashing (Laravel default)
-   **Authorization**: Role-based access control (RBAC)
-   **Data Validation**: Server-side validation for all inputs
-   **SQL Injection**: Prevention via Eloquent ORM
-   **XSS Protection**: Blade template auto-escaping

### 4.4 Usability Requirements

-   **Responsive Design**: Mobile, tablet, desktop support
-   **Dark Mode**: User-preference based theme switching
-   **Accessibility**: Keyboard navigation, ARIA labels (future)
-   **Error Messages**: Clear, actionable feedback
-   **Loading States**: Visual indicators for async operations

### 4.5 Maintainability Requirements

-   **Code Documentation**: PHPDoc comments on all public methods
-   **Code Standards**: PSR-12 compliance via Laravel Pint
-   **Version Control**: Git with semantic versioning
-   **Modularity**: Service layer for reusable business logic
-   **Testing**: Unit and feature test coverage (future)

---

## 5. System Design

### 5.1 Architecture Pattern

**Layered MVC with Service Layer**

```
┌───────────────────────────────────────┐
│   Presentation Layer                  │
│   (Blade, Livewire, Alpine.js)        │
└───────────────────────────────────────┘
              │
              ▼
┌───────────────────────────────────────┐
│   Application Layer                   │
│   (Routes, Controllers, Middleware)   │
└───────────────────────────────────────┘
              │
              ▼
┌───────────────────────────────────────┐
│   Service Layer                       │
│   (Business Logic Services)           │
└───────────────────────────────────────┘
              │
              ▼
┌───────────────────────────────────────┐
│   Domain Layer                        │
│   (Eloquent Models, Events)           │
└───────────────────────────────────────┘
              │
              ▼
┌───────────────────────────────────────┐
│   Infrastructure Layer                │
│   (Database, Queue, Mail, HTTP)       │
└───────────────────────────────────────┘
```

### 5.2 Module Design

#### Booking Module

-   **Namespace**: `App\Http\Controllers\Booking\`
-   **Responsibilities**: Customer-facing booking workflows
-   **Components**:
    -   `BusinessSearchController`: Business discovery
    -   `AvailabilityController`: Slot availability display
    -   `BookingController`: Booking CRUD

#### Business Module

-   **Namespace**: `App\Http\Controllers\Business\`
-   **Responsibilities**: Owner-facing management workflows
-   **Components**:
    -   `DashboardController`: Overview display
    -   `ProfileController`: Business profile management
    -   `LocationController`: Location/hours management
    -   `AppointmentController`: Appointment operations
    -   `HolidayController`: Holiday management
    -   `RecurringBlockController`: Recurring block management

#### Service Module

-   **Namespace**: `App\Services\`
-   **Responsibilities**: Reusable business logic
-   **Components**:
    -   `AvailabilityService`: Slot calculation
    -   `BookingActionService`: Booking transactions
    -   `SearchService`: Business search + geocoding
    -   `GeocodingService`: Address resolution
    -   `OpeningHoursBuilder`: Hours JSON construction

---

## 6. Database Design

### 6.1 Schema Diagram

```
┌──────────────┐
│    users     │
├──────────────┤
│ id (PK)      │
│ name         │
│ email (UQ)   │
│ password     │
│ role         │◀─────────────┐
│ timestamps   │              │
└──────┬───────┘              │
       │                      │ owner_id (FK)
       │ 1                    │
       │                      │
       ▼ *           ┌────────┴──────┐
┌──────────────┐    │   businesses  │
│ appointments │    ├───────────────┤
├──────────────┤    │ id (PK)       │
│ id (PK)      │    │ owner_id (FK) │
│customer_id(FK)│◀──┤ name          │
│business_id(FK)│──▶│ description   │
│service_id (FK)│   │ timestamps    │
│ start_time   │   └───────┬───────┘
│ end_time     │           │ 1
│ status       │           │
│ timestamps   │           ▼ *
└──────┬───────┘   ┌───────────────┐
       │           │   locations   │
       │           ├───────────────┤
       │           │ id (PK)       │
       │           │business_id(FK)│
       │           │ address       │
       │           │ city          │
       │           │ latitude      │
       │           │ longitude     │
       │           │opening_hours  │ (JSON)
       │           │ timestamps    │
       │           └───────────────┘
       │
       │           ┌───────────────┐
       │           │   services    │
       │           ├───────────────┤
       │           │ id (PK)       │
       └──────────▶│business_id(FK)│
                   │ name          │
                   │ description   │
                   │ duration      │
                   │ price         │
                   │ timestamps    │
                   └───────────────┘

       ┌───────────────────────┐   ┌─────────────────────────┐
       │     holidays          │   │ recurring_blocked_times │
       ├───────────────────────┤   ├─────────────────────────┤
       │ id (PK)               │   │ id (PK)                 │
       │ business_id (FK)      │   │ business_id (FK)        │
       │ date                  │   │ title                   │
       │ name                  │   │ start_time              │
       │ timestamps            │   │ end_time                │
       └───────────────────────┘   │ days_of_week            │ (JSON)
                                   │ timestamps              │
                                   └─────────────────────────┘
```

### 6.2 Table Specifications

#### users

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL, -- 'customer' or 'owner'
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### businesses

```sql
CREATE TABLE businesses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### locations

```sql
CREATE TABLE locations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id BIGINT UNSIGNED NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    opening_hours JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
);
```

#### services

```sql
CREATE TABLE services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    duration INT NOT NULL, -- in minutes
    price DECIMAL(8,2) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
);
```

#### appointments

```sql
CREATE TABLE appointments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NOT NULL,
    business_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    status ENUM('confirmed','arrival','completed','cancelled','no_show') DEFAULT 'confirmed',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    INDEX idx_start_time (start_time),
    INDEX idx_customer (customer_id),
    INDEX idx_business (business_id)
);
```

#### holidays

```sql
CREATE TABLE holidays (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    name VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
);
```

#### recurring_blocked_times

```sql
CREATE TABLE recurring_blocked_times (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    days_of_week JSON NOT NULL, -- ["monday", "friday"]
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
);
```

### 6.3 JSON Field Structures

#### opening_hours (locations table)

```json
{
    "monday": { "open": "09:00", "close": "17:00" },
    "tuesday": { "open": "09:00", "close": "17:00" },
    "wednesday": null,
    "thursday": { "open": "10:00", "close": "18:00" },
    "friday": { "open": "09:00", "close": "17:00" },
    "saturday": { "open": "10:00", "close": "14:00" },
    "sunday": null
}
```

**Note**: `null` indicates closed day.

#### days_of_week (recurring_blocked_times table)

```json
["monday", "tuesday", "wednesday", "thursday", "friday"]
```

---

## 7. Interface Design

### 7.1 User Interface Components

#### Customer Interface

1. **Landing Page** (`/`)

    - Hero section with search CTA
    - Featured businesses (optional)

2. **Business Search** (`/businesses`)

    - Search form (query, location, sort)
    - Business cards grid
    - Filtering/sorting controls

3. **Business Details** (`/businesses/{id}`)

    - Business info header
    - Services list
    - Book button per service

4. **Availability View** (`/businesses/{business}/services/{service}/availability`)

    - Date picker
    - Available time slots grid
    - Book button per slot

5. **My Bookings** (`/my-bookings`)
    - Tabbed interface (Upcoming, Completed, Cancelled)
    - Appointment cards with cancel button

#### Business Owner Interface

1. **Dashboard** (`/business/dashboard`)

    - Stats cards (total services, recent appointments)
    - Quick actions menu
    - Recent appointments list

2. **Profile Edit** (`/business/profile/edit`)

    - Business details form
    - Location form with opening hours
    - Holiday management section
    - Recurring blocks section

3. **Services Management** (`/business/services`)

    - Services list table
    - Create/edit service forms

4. **Appointments** (`/business/appointments`)
    - Tabbed interface (Upcoming, Past)
    - Status update dropdown per appointment

### 7.2 API Endpoints (Route List)

#### Authentication Routes

```
GET  /login               - Login form
POST /login               - Authenticate
GET  /register            - Registration form
POST /register            - Create user
POST /logout              - Logout
```

#### Customer Routes

```
GET  /businesses                                          - Search businesses
GET  /businesses/{business}                               - View business
GET  /businesses/{business}/services/{service}/availability - Check availability
POST /bookings                                            - Create booking
GET  /my-bookings                                         - View bookings
PATCH /bookings/{appointment}/cancel                      - Cancel booking
```

#### Business Owner Routes

```
GET   /business/dashboard                                 - Dashboard
GET   /business/profile/create                            - Create profile form
POST  /business/profile                                   - Create profile
GET   /business/profile/edit                              - Edit profile form
PATCH /business/profile                                   - Update profile
GET   /business/location/edit                             - Edit location form
PATCH /business/location                                  - Update location
POST  /business/holidays                                  - Add holiday
DELETE /business/holidays/{id}                            - Remove holiday
POST  /business/recurring-blocks                          - Add recurring block
DELETE /business/recurring-blocks/{id}                    - Remove recurring block
GET   /business/appointments                              - View appointments
PATCH /business/appointments/{appointment}/status         - Update status
GET   /business/services                                  - List services
POST  /business/services                                  - Create service
GET   /business/services/{service}/edit                   - Edit service form
PUT   /business/services/{service}                        - Update service
DELETE /business/services/{service}                       - Delete service
```

---

## 8. Component Design

### 8.1 Controller Design

#### BookingController (Booking Namespace)

```php
class BookingController extends Controller
{
    protected BookingActionService $bookingActionService;

    // POST /bookings
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate input
        // 2. Delegate to BookingActionService
        // 3. Handle success/failure result
        // 4. Redirect with message
    }

    // GET /my-bookings
    public function index(): View
    {
        // 1. Fetch user appointments
        // 2. Segment by status (upcoming/completed/cancelled)
        // 3. Return view with collections
    }

    // PATCH /bookings/{appointment}/cancel
    public function cancel(Appointment $appointment): RedirectResponse
    {
        // 1. Authorize (user owns appointment)
        // 2. Validate (future appointment)
        // 3. Update status
        // 4. Fire BookingCancelled event
        // 5. Redirect with confirmation
    }
}
```

#### ProfileController (Business Namespace)

```php
class ProfileController extends Controller
{
    protected GeocodingService $geocodingService;
    protected OpeningHoursBuilder $hoursBuilder;

    // POST /business/profile
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate input
        // 2. Build opening hours JSON
        // 3. Create business
        // 4. Geocode address
        // 5. Create location with coordinates
        // 6. Redirect with message
    }

    // PATCH /business/profile
    public function updateProfile(Request $request): RedirectResponse
    {
        // 1. Validate input
        // 2. Begin transaction
        // 3. Update business details
        // 4. Update location and re-geocode
        // 5. Commit transaction
        // 6. Redirect with confirmation
    }
}
```

### 8.2 Service Class Design

#### AvailabilityService

```php
class AvailabilityService
{
    /**
     * Get available time slots for a service on a date.
     *
     * @param Business $business
     * @param Service $service
     * @param Carbon $date
     * @return Collection<Carbon> Collection of start times
     */
    public function getAvailableSlots(Business $business, Service $service, Carbon $date): Collection
    {
        // 1. Get opening hours for day of week
        // 2. Check if holiday
        // 3. Generate candidate slots
        // 4. Filter by existing appointments
        // 5. Filter by recurring blocks
        // 6. Filter by minimum advance time (30 min)
        // 7. Return available slots
    }

    /**
     * Check if a specific time slot is available.
     *
     * @param Business $business
     * @param Service $service
     * @param Carbon $startTime
     * @return bool
     */
    public function isSlotAvailable(Business $business, Service $service, Carbon $startTime): bool
    {
        // 1. Calculate end time
        // 2. Check for overlapping appointments
        // 3. Check for recurring blocks
        // 4. Check if within opening hours
        // 5. Return availability status
    }
}
```

#### BookingActionService

```php
class BookingActionService
{
    protected AvailabilityService $availabilityService;

    /**
     * Create a booking with pessimistic locking.
     *
     * @param int $customerId
     * @param int $businessId
     * @param int $serviceId
     * @param string $startTime
     * @return array ['success' => bool, 'appointment' => ?Appointment, 'error' => ?string]
     */
    public function createBooking(int $customerId, int $businessId, int $serviceId, string $startTime): array
    {
        // 1. Load business and service
        // 2. Begin transaction
        // 3. Lock business row (lockForUpdate)
        // 4. Re-check availability
        // 5. Create appointment
        // 6. Commit transaction
        // 7. Fire BookingCreated event
        // 8. Return result array
    }
}
```

### 8.3 Event/Listener Design

#### BookingCreated Event

```php
class BookingCreated
{
    public Appointment $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }
}
```

#### SendBookingCreatedNotification Listener

```php
class SendBookingCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(BookingCreated $event): void
    {
        $appointment = $event->appointment->load(['customer', 'business.owner', 'service']);

        // Send to customer
        Mail::to($appointment->customer->email)
            ->send(new BookingCreatedCustomer($appointment));

        // Send to owner
        Mail::to($appointment->business->owner->email)
            ->send(new BookingCreatedOwner($appointment));
    }
}
```

---

## 9. Algorithm Design

### 9.1 Availability Calculation Algorithm

```
ALGORITHM: GetAvailableSlots(business, service, date)

INPUT:
  - business: Business entity
  - service: Service entity
  - date: Carbon date instance

OUTPUT:
  - Collection of available Carbon datetime instances

STEPS:
1. dayOfWeek = date.format('l').toLowerCase()
2. location = business.locations.first()
3. openingHours = location.opening_hours[dayOfWeek]

4. IF openingHours IS NULL THEN
     RETURN empty collection (closed)
   END IF

5. IF business.holidays contains date THEN
     RETURN empty collection (holiday)
   END IF

6. openTime = Carbon.createFromFormat('H:i', openingHours.open, date)
7. closeTime = Carbon.createFromFormat('H:i', openingHours.close, date)
8. duration = service.duration
9. slots = []

10. currentSlot = openTime.copy()
11. WHILE currentSlot.addMinutes(duration) <= closeTime DO
      slots.push(currentSlot.copy())
      currentSlot.addMinutes(duration)
    END WHILE

12. existingAppointments = Appointment
      .where('business_id', business.id)
      .whereDate('start_time', date)
      .get()

13. recurringBlocks = business.recurringBlockedTimes
      .where('days_of_week', CONTAINS dayOfWeek)
      .get()

14. availableSlots = []
15. FOR EACH slot IN slots DO
      slotEnd = slot.copy().addMinutes(duration)
      isAvailable = TRUE

      // Check appointments
      FOR EACH appt IN existingAppointments DO
        IF slot < appt.end_time AND slotEnd > appt.start_time THEN
          isAvailable = FALSE
          BREAK
        END IF
      END FOR

      // Check recurring blocks
      FOR EACH block IN recurringBlocks DO
        blockStart = Carbon.createFromFormat('H:i', block.start_time, date)
        blockEnd = Carbon.createFromFormat('H:i', block.end_time, date)
        IF slot < blockEnd AND slotEnd > blockStart THEN
          isAvailable = FALSE
          BREAK
        END IF
      END FOR

      // Check minimum advance time (30 minutes from now)
      IF slot < Carbon.now().addMinutes(30) THEN
        isAvailable = FALSE
      END IF

      IF isAvailable THEN
        availableSlots.push(slot)
      END IF
    END FOR

16. RETURN availableSlots
```

**Time Complexity**: O(n \* (m + k)) where:

-   n = number of time slots
-   m = number of existing appointments
-   k = number of recurring blocks

**Space Complexity**: O(n) for slots array

### 9.2 Booking Creation with Locking Algorithm

```
ALGORITHM: CreateBooking(customerId, businessId, serviceId, startTime)

INPUT:
  - customerId: User ID
  - businessId: Business ID
  - serviceId: Service ID
  - startTime: Datetime string

OUTPUT:
  - Result array: {success: bool, appointment: ?Appointment, error: ?string}

STEPS:
1. business = Business.findOrFail(businessId)
2. service = Service.findOrFail(serviceId)
3. startTime = Carbon.parse(startTime)
4. endTime = startTime.copy().addMinutes(service.duration)

5. DB.beginTransaction()
6. TRY
     // Pessimistic lock
     business = Business.where('id', businessId).lockForUpdate().first()

     // Re-check availability within transaction
     isAvailable = AvailabilityService.isSlotAvailable(business, service, startTime)

     IF NOT isAvailable THEN
       DB.rollBack()
       RETURN {
         success: FALSE,
         appointment: NULL,
         error: 'Time slot no longer available'
       }
     END IF

     // Create appointment
     appointment = Appointment.create({
       customer_id: customerId,
       business_id: businessId,
       service_id: serviceId,
       start_time: startTime,
       end_time: endTime,
       status: 'confirmed'
     })

     DB.commit()

     // Fire event (outside transaction for async queue)
     Event.dispatch(new BookingCreated(appointment))

     RETURN {
       success: TRUE,
       appointment: appointment,
       error: NULL
     }

   CATCH Exception e
     DB.rollBack()
     RETURN {
       success: FALSE,
       appointment: NULL,
       error: 'An error occurred. Please try again.'
     }
   END TRY
```

**Lock Scope**: Row-level lock on `businesses` table
**Lock Duration**: Duration of transaction (~100-200ms)
**Concurrency Guarantee**: Serializes bookings per business ID

---

## 10. Security Design

### 10.1 Authentication Flow

```
1. User visits /login
2. User submits credentials (email, password)
3. LoginRequest validates input
4. Attempt authentication via Auth facade
5. IF authenticated:
     - Regenerate session ID (prevent fixation)
     - Redirect to role-based dashboard
   ELSE:
     - Return with validation error
   END IF
```

### 10.2 Authorization Checks

#### Middleware-Level

```php
// routes/web.php
Route::middleware(['auth', 'owner'])->prefix('business')->group(function () {
    // Business owner routes
});
```

#### Controller-Level

```php
// ServiceController
public function edit(Service $service)
{
    if ($service->business_id !== Auth::user()->business->id) {
        abort(403, 'Unauthorized action.');
    }
    // Continue
}
```

### 10.3 Input Validation Design

#### Form Request Example

```php
class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->role === 'owner' && Auth::user()->business;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:5|max:480',
            'price' => 'nullable|numeric|min:0|max:99999.99',
        ];
    }
}
```

### 10.4 CSRF Protection

-   **Token Generation**: Automatic on session start
-   **Validation**: `VerifyCsrfToken` middleware on POST/PUT/PATCH/DELETE
-   **Blade Integration**: `@csrf` directive in forms

### 10.5 Password Security

```php
// Registration
$user->password = Hash::make($request->password);

// Login
if (Hash::check($request->password, $user->password)) {
    // Authenticated
}
```

---

## 11. Testing Strategy

### 11.1 Unit Testing

#### Target Components

-   Service classes (AvailabilityService, BookingActionService)
-   Model methods (scopes, accessors, mutators)
-   Helper functions (OpeningHoursBuilder)

#### Example Test Case

```php
public function test_availability_service_excludes_past_slots()
{
    $business = Business::factory()->create();
    $service = Service::factory()->create(['business_id' => $business->id]);
    $date = Carbon::tomorrow();

    $slots = $this->availabilityService->getAvailableSlots($business, $service, $date);

    foreach ($slots as $slot) {
        $this->assertGreaterThan(Carbon::now()->addMinutes(30), $slot);
    }
}
```

### 11.2 Feature Testing

#### Target Workflows

-   Customer booking flow (search → view → availability → book)
-   Owner profile creation flow
-   Appointment cancellation flow
-   Service CRUD operations

#### Example Test Case

```php
public function test_customer_can_book_available_slot()
{
    $customer = User::factory()->customer()->create();
    $business = Business::factory()->create();
    $service = Service::factory()->create(['business_id' => $business->id]);
    $slot = Carbon::tomorrow()->setTime(10, 0);

    $response = $this->actingAs($customer)->post('/bookings', [
        'business_id' => $business->id,
        'service_id' => $service->id,
        'start_time' => $slot->toDateTimeString(),
    ]);

    $response->assertRedirect('/my-bookings');
    $this->assertDatabaseHas('appointments', [
        'customer_id' => $customer->id,
        'service_id' => $service->id,
        'status' => 'confirmed',
    ]);
}
```

### 11.3 Integration Testing

#### External Service Mocking

```php
// Mock Nominatim geocoding API
Http::fake([
    'nominatim.openstreetmap.org/*' => Http::response([
        ['lat' => '40.7128', 'lon' => '-74.0060']
    ], 200)
]);
```

### 11.4 Browser Testing (Future)

-   **Tool**: Laravel Dusk
-   **Scenarios**: Full user journeys with JavaScript interactions
-   **Coverage**: Dark mode toggle, sidebar collapse, tab switching

---

## 12. Deployment Design

### 12.1 Environment Configuration

#### Production `.env`

```env
APP_NAME=Appointifi
APP_ENV=production
APP_DEBUG=false
APP_URL=https://appointifi.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=appointifi
DB_USERNAME=appointifi_user
DB_PASSWORD=strong_password_here

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525

QUEUE_CONNECTION=redis
CACHE_STORE=redis

SESSION_DRIVER=redis
SESSION_LIFETIME=120
```

### 12.2 Web Server Configuration

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name appointifi.com www.appointifi.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name appointifi.com www.appointifi.com;
    root /var/www/appointifi/public;

    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 12.3 Queue Worker Supervisor Configuration

#### `/etc/supervisor/conf.d/appointifi-worker.conf`

```ini
[program:appointifi-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/appointifi/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/appointifi/storage/logs/worker.log
stopwaitsecs=3600
```

### 12.4 Database Backup Strategy

```bash
# Daily backup cron job
0 2 * * * /usr/bin/mysqldump -u backup_user -p'password' appointifi > /backups/appointifi_$(date +\%Y\%m\%d).sql
```

### 12.5 Deployment Checklist

-   [ ] Environment variables configured
-   [ ] Database migrated (`php artisan migrate --force`)
-   [ ] Caches cleared and rebuilt
-   [ ] Queue worker running via supervisor
-   [ ] SSL certificate installed
-   [ ] Firewall configured (ports 80, 443)
-   [ ] Log rotation configured
-   [ ] Backups scheduled
-   [ ] Monitoring alerts configured
-   [ ] DNS records updated

---

## 13. Appendices

### Appendix A: Glossary

| Term                    | Definition                                               |
| ----------------------- | -------------------------------------------------------- |
| **Pessimistic Locking** | Database row locking to prevent concurrent modifications |
| **Eloquent**            | Laravel's ORM (Object-Relational Mapping) system         |
| **Livewire**            | Full-stack framework for building dynamic interfaces     |
| **Blade**               | Laravel's templating engine                              |
| **Carbon**              | PHP datetime library (Laravel's default)                 |
| **Nominatim**           | OpenStreetMap's geocoding service                        |

### Appendix B: Change Log

| Version | Date       | Author   | Changes              |
| ------- | ---------- | -------- | -------------------- |
| 1.0     | 2025-11-20 | Dev Team | Initial SDS creation |

### Appendix C: Future Enhancements

1. **Payment Integration**: Stripe/PayPal for booking deposits
2. **SMS Notifications**: Twilio integration for reminders
3. **Multi-location**: Support businesses with multiple locations
4. **Calendar Sync**: Google Calendar, Outlook integration
5. **Reviews/Ratings**: Customer feedback system
6. **Mobile App**: Native iOS/Android applications
7. **Advanced Analytics**: Business dashboard metrics
8. **Internationalization**: Multi-language support

---

**Document Status**: Final  
**Approval**: Pending  
**Next Review**: February 2026
