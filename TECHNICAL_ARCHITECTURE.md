# Appointifi - Technical Architecture Documentation

## Document Information

-   **Version**: 1.0
-   **Last Updated**: November 20, 2025
-   **Project**: Appointifi Appointment Booking System
-   **Framework**: Laravel 12.37.0
-   **PHP Version**: 8.3.16

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Architecture Principles](#architecture-principles)
3. [Technology Stack](#technology-stack)
4. [System Architecture](#system-architecture)
5. [Component Architecture](#component-architecture)
6. [Data Architecture](#data-architecture)
7. [Security Architecture](#security-architecture)
8. [Integration Architecture](#integration-architecture)
9. [Deployment Architecture](#deployment-architecture)
10. [Performance Considerations](#performance-considerations)

---

## 1. System Overview

### 1.1 Purpose

Appointifi is a dual-sided marketplace platform that facilitates appointment booking between customers and service-based businesses. The system handles real-time availability calculation, booking management, and operational workflows for business owners.

### 1.2 System Context

```
┌─────────────┐         ┌──────────────────┐         ┌────────────────┐
│  Customers  │────────▶│   Appointifi     │◀────────│ Business Owners│
│             │         │   Web Platform   │         │                │
└─────────────┘         └──────────────────┘         └────────────────┘
                              │        │
                              ▼        ▼
                        ┌──────────┐  ┌──────────────┐
                        │ Database │  │ Email Service│
                        └──────────┘  └──────────────┘
                              │              │
                              ▼              ▼
                        ┌──────────┐  ┌──────────────┐
                        │Nominatim │  │  Queue Jobs  │
                        │ Geocoding│  │              │
                        └──────────┘  └──────────────┘
```

### 1.3 High-Level Goals

-   **Zero Double-Bookings**: Guarantee booking integrity through pessimistic locking
-   **Accurate Availability**: Complex real-time slot calculation
-   **Scalable Architecture**: Service-oriented design for maintainability
-   **User Experience**: Responsive UI with dark mode support
-   **Data Integrity**: Transactional consistency and referential integrity

---

## 2. Architecture Principles

### 2.1 Design Principles

-   **Separation of Concerns**: Distinct namespaces for Business and Booking domains
-   **Service Layer Pattern**: Business logic encapsulated in reusable services
-   **Event-Driven Communication**: Domain events for cross-cutting concerns (notifications)
-   **Repository Pattern**: Eloquent ORM as implicit repository layer
-   **Single Responsibility**: Controllers delegate to services, maintain thin logic

### 2.2 Architectural Patterns

-   **MVC (Model-View-Controller)**: Laravel's foundational pattern
-   **Service Layer**: Complex business logic extraction (`AvailabilityService`, `BookingActionService`)
-   **Observer Pattern**: Eloquent events and custom domain events
-   **Strategy Pattern**: Opening hours calculation, availability logic
-   **Facade Pattern**: Laravel facades for system services

### 2.3 Code Organization Strategy

```
app/
├── Http/Controllers/
│   ├── Auth/              # Authentication flows
│   ├── Business/          # Business owner domain
│   │   ├── DashboardController
│   │   ├── ProfileController
│   │   ├── LocationController
│   │   ├── AppointmentController
│   │   ├── HolidayController
│   │   └── RecurringBlockController
│   ├── Booking/           # Customer booking domain
│   │   ├── BusinessSearchController
│   │   ├── AvailabilityController
│   │   └── BookingController
│   └── ServiceController  # Shared service CRUD
├── Services/              # Business logic layer
├── Models/                # Domain entities
├── Events/                # Domain events
└── Listeners/             # Event handlers
```

---

## 3. Technology Stack

### 3.1 Backend Stack

| Component | Technology       | Version | Purpose                      |
| --------- | ---------------- | ------- | ---------------------------- |
| Framework | Laravel          | 12.37.0 | Core application framework   |
| Language  | PHP              | 8.3.16  | Backend programming language |
| Database  | MySQL/PostgreSQL | -       | Relational data storage      |
| Cache     | Redis (optional) | -       | Session/cache backend        |
| Queue     | Database/Redis   | -       | Asynchronous job processing  |

### 3.2 Frontend Stack

| Component         | Technology   | Version    | Purpose                          |
| ----------------- | ------------ | ---------- | -------------------------------- |
| Templating        | Blade        | Laravel 12 | Server-side rendering            |
| CSS Framework     | Tailwind CSS | 3.x        | Utility-first styling            |
| JavaScript        | Alpine.js    | 3.x        | Reactive UI components           |
| UI Components     | Livewire     | 3.6        | Dynamic server-driven components |
| Component Library | Flux UI      | 2.6        | Pre-built UI components          |
| Build Tool        | Vite         | -          | Asset bundling and HMR           |

### 3.3 External Services

| Service      | Provider                | Purpose                          |
| ------------ | ----------------------- | -------------------------------- |
| Geocoding    | OpenStreetMap Nominatim | Address → coordinates conversion |
| Email (Dev)  | Log Driver / Mailpit    | Email testing and delivery       |
| Email (Prod) | SMTP / Mailtrap         | Production email delivery        |

### 3.4 Development Tools

-   **Package Manager**: Composer (PHP), NPM (JavaScript)
-   **Code Quality**: Laravel Pint (PSR-12 formatting)
-   **Testing**: PHPUnit (unit/feature tests)
-   **Version Control**: Git

---

## 4. System Architecture

### 4.1 Layered Architecture

```
┌─────────────────────────────────────────────────────────┐
│              Presentation Layer                         │
│  (Blade Templates, Livewire Components, Alpine.js)     │
└─────────────────────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────┐
│              Application Layer                          │
│  (Controllers, Middleware, Form Requests, Routes)       │
└─────────────────────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────┐
│              Domain/Business Layer                      │
│  (Services, Events, Listeners, Policies)                │
└─────────────────────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────┐
│              Data Access Layer                          │
│  (Eloquent Models, Migrations, Seeders)                 │
└─────────────────────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────┐
│              Infrastructure Layer                       │
│  (Database, Cache, Queue, Mail, External APIs)          │
└─────────────────────────────────────────────────────────┘
```

### 4.2 Request Flow

```
1. HTTP Request → Web Server (Apache/Nginx)
                    ↓
2. Laravel Router → Middleware Stack
                    ↓
3. Controller → Service Layer
                    ↓
4. Service → Model/Repository
                    ↓
5. Model → Database Query
                    ↓
6. Response ← Blade View/JSON
                    ↓
7. HTTP Response → Browser
```

### 4.3 Domain-Driven Boundaries

#### Booking Domain (Customer-facing)

-   **Responsibilities**: Business discovery, availability checking, booking creation
-   **Controllers**: `BusinessSearchController`, `AvailabilityController`, `BookingController`
-   **Services**: `SearchService`, `AvailabilityService`, `BookingActionService`

#### Business Domain (Owner-facing)

-   **Responsibilities**: Profile management, schedule configuration, appointment operations
-   **Controllers**: `DashboardController`, `ProfileController`, `LocationController`, `AppointmentController`, `HolidayController`, `RecurringBlockController`
-   **Services**: `GeocodingService`, `OpeningHoursBuilder`

---

## 5. Component Architecture

### 5.1 Service Layer Components

#### AvailabilityService

**Responsibility**: Calculate available time slots for a service on a given date

**Algorithm**:

```
1. Retrieve business opening hours for the day of week
2. If closed or holiday → return empty + reason
3. Generate time slots based on service duration
4. Fetch existing appointments for the date
5. Fetch recurring blocked times for day of week
6. Filter slots:
   - Remove past times (< now + 30 min buffer)
   - Remove overlapping appointments
   - Remove recurring blocks
7. Return available slots collection
```

**Key Methods**:

-   `getAvailableSlots(Business $business, Service $service, Carbon $date): Collection`
-   `isSlotAvailable(Business $business, Service $service, Carbon $startTime): bool`
-   `getUnavailabilityReason(Business $business, Carbon $date): ?string`

#### BookingActionService

**Responsibility**: Handle booking creation with transactional integrity

**Process**:

```
1. Begin database transaction
2. Lock business row (lockForUpdate)
3. Re-check availability (prevent race conditions)
4. Create appointment record
5. Commit transaction
6. Fire BookingCreated event
7. Return success/error result
```

**Concurrency Strategy**:

-   **Pessimistic Locking**: Serializes concurrent booking attempts per business
-   **Transaction Isolation**: Ensures atomicity of availability check + insert

#### GeocodingService

**Responsibility**: Convert addresses to geographic coordinates

**Integration**: OpenStreetMap Nominatim API

-   Best-effort approach (non-fatal failures)
-   Rate limiting respected (1 req/sec)
-   User-Agent header required

#### OpeningHoursBuilder

**Responsibility**: Construct opening hours JSON from form inputs

**Features**:

-   Preserves existing hours when inputs blank (update mode)
-   Treats missing inputs as closed (create mode)
-   Handles explicit "closed" checkbox overrides

### 5.2 Controller Architecture

#### Namespace Organization

-   **`App\Http\Controllers\Business\`**: Owner-specific operations
-   **`App\Http\Controllers\Booking\`**: Customer-specific operations
-   **Root Controllers**: Shared resources (ServiceController, ProfileController)

#### Controller Responsibilities

-   **Thin Controllers**: Delegate business logic to services
-   **Validation**: Use Form Request classes or inline validation
-   **Authorization**: Enforce ownership checks before mutations
-   **Response**: Return views with compact data or redirect with flash messages

### 5.3 Event-Driven Architecture

#### Domain Events

```php
BookingCreated         → Triggered after successful appointment creation
BookingCancelled       → Triggered after customer cancels appointment
```

#### Event Listeners

```php
SendBookingCreatedNotification (ShouldQueue)
  ├─ BookingCreatedCustomer email
  └─ BookingCreatedOwner email

SendBookingCancelledNotification (ShouldQueue)
  ├─ BookingCancelledCustomer email
  └─ BookingCancelledOwner email
```

**Benefits**:

-   Decouples notification logic from booking logic
-   Asynchronous processing via queue
-   Easily extendable (add SMS, webhooks, analytics)

---

## 6. Data Architecture

### 6.1 Database Schema

#### Entity-Relationship Diagram

```
┌──────────┐
│  users   │
│   (PK)   │
└────┬─────┘
     │ 1
     │ owns (owner_id)
     ▼ 1
┌──────────────┐ 1      * ┌────────────┐
│  businesses  │◀─────────│  services  │
│     (PK)     │          │    (PK)    │
└───────┬──────┘          └─────┬──────┘
        │ 1                     │ *
        │                       │
        ▼ *                     │
┌──────────────┐                │
│  locations   │                │
│     (PK)     │                │
└──────────────┘                │
        │ 1                     │
        │                       │
        ▼ *                     │
┌──────────────┐                │
│  holidays    │                │
│     (PK)     │                │
└──────────────┘                │
        │ 1                     │
        │                       │
        ▼ *                     │
┌────────────────────────┐      │
│recurring_blocked_times │      │
│         (PK)           │      │
└────────────────────────┘      │
                                │
        ┌───────────────────────┘
        │ *
        ▼
┌──────────────┐
│ appointments │
│     (PK)     │
└──────────────┘
        │ *
        ├─────────┐
        │         │
        ▼         ▼
┌──────────┐  ┌──────────┐
│  users   │  │businesses│
│(customer)│  │          │
└──────────┘  └──────────┘
```

### 6.2 Key Tables

#### users

-   **Purpose**: Store both customer and business owner accounts
-   **Key Field**: `role` (enum: 'customer', 'owner')
-   **Indexes**: `email` (unique)

#### businesses

-   **Purpose**: Business profile information
-   **Relationships**: 1:1 with users (owner), 1:\* with services, locations, appointments
-   **Cascade**: Delete cascades from owner user

#### locations

-   **Purpose**: Business address with geocoded coordinates
-   **JSON Field**: `opening_hours` (weekday → {open, close} map)
-   **Nullable**: `latitude`, `longitude` (geocoding may fail)

#### services

-   **Purpose**: Service offerings with duration and pricing
-   **Duration**: Stored in minutes (integer)
-   **Integrity**: Cannot delete if future appointments exist

#### appointments

-   **Purpose**: Booking records linking customer, business, service
-   **Status Flow**: confirmed → arrival → completed (or cancelled/no_show)
-   **Indexes**: `start_time`, `customer_id`, `business_id`, `service_id`

#### holidays

-   **Purpose**: Specific dates when business is closed
-   **Scope**: Business-specific
-   **Usage**: Availability calculation excludes these dates

#### recurring_blocked_times

-   **Purpose**: Weekly recurring unavailable periods (e.g., lunch)
-   **JSON Field**: `days_of_week` (array of weekday names)
-   **Time Range**: `start_time`, `end_time` (HH:MM format)

### 6.3 Data Integrity Constraints

#### Foreign Key Constraints

```sql
appointments.customer_id → users.id (ON DELETE CASCADE)
appointments.business_id → businesses.id (ON DELETE CASCADE)
appointments.service_id → services.id (ON DELETE CASCADE)
businesses.owner_id → users.id (ON DELETE CASCADE)
services.business_id → businesses.id (ON DELETE CASCADE)
```

#### Application-Level Constraints

-   Service deletion blocked if future appointments exist
-   Only appointment owner (customer) can cancel
-   Only past appointments can be marked completed
-   Booking creation requires slot availability

---

## 7. Security Architecture

### 7.1 Authentication & Authorization

#### Authentication Mechanisms

-   **Laravel Breeze**: Scaffolded authentication (login, register, password reset)
-   **Session-Based Auth**: Cookie + CSRF token
-   **Email Verification**: Optional verification flow (built-in support)

#### Authorization Layers

```
1. Middleware Layer
   ├─ auth (authenticated users only)
   └─ owner (business owners only)

2. Controller Layer
   ├─ Ownership checks (service.business_id === auth.user.business.id)
   └─ Role checks (customer vs owner endpoints)

3. Policy Layer (optional future enhancement)
   └─ Fine-grained permission checks
```

#### RBAC Implementation

```php
// Middleware: app/Http/Middleware/CheckRoleOwner.php
if (auth()->user()->role !== 'owner') {
    abort(403);
}

// Controller authorization
if ($service->business_id !== Auth::user()->business->id) {
    abort(403, 'Unauthorized action.');
}
```

### 7.2 Data Protection

#### Input Validation

-   **Form Requests**: Validation rules for complex forms
-   **Inline Validation**: `$request->validate()` in controllers
-   **Sanitization**: Implicit via Eloquent mass assignment protection

#### Output Escaping

-   **Blade**: Auto-escapes `{{ $variable }}` syntax
-   **Raw Output**: `{!! $html !!}` used cautiously (admin-only content)

#### CSRF Protection

-   **Global Middleware**: `VerifyCsrfToken` on all POST/PUT/DELETE
-   **Blade Forms**: `@csrf` directive auto-includes token

#### SQL Injection Prevention

-   **Eloquent ORM**: Parameterized queries
-   **Query Builder**: Bindings prevent injection
-   **Raw Queries**: Use bindings (`DB::select('...', [$param])`)

### 7.3 Concurrency & Race Conditions

#### Pessimistic Locking Strategy

```php
DB::beginTransaction();
$business = Business::where('id', $id)->lockForUpdate()->first();
// Critical section: availability check + appointment creation
DB::commit();
```

**Benefits**:

-   Serializes concurrent bookings for same business
-   Prevents double-booking under high load
-   Database-level guarantee

**Trade-offs**:

-   Slight performance impact (locks held during transaction)
-   Mitigated by short transaction scope

---

## 8. Integration Architecture

### 8.1 External API Integrations

#### OpenStreetMap Nominatim (Geocoding)

**Endpoint**: `https://nominatim.openstreetmap.org/search`

**Usage**:

```php
Http::withHeaders([
    'User-Agent' => 'appointifi-app/1.0 (contact@localhost)'
])->get('https://nominatim.openstreetmap.org/search', [
    'q' => $address,
    'format' => 'json',
    'limit' => 1,
]);
```

**Error Handling**: Best-effort (failures return null, profile creation continues)

**Rate Limiting**: 1 request per second (manual throttling)

### 8.2 Email Integration

#### Development

-   **Driver**: `log` (writes to `storage/logs/laravel.log`)
-   **Preview**: Local routes `/test-email` and `/test-email/send`

#### Production Options

-   **SMTP**: Direct SMTP configuration
-   **Mailtrap**: Email sandbox for staging
-   **MailHog**: Local email testing server

#### Queue Processing

```bash
php artisan queue:work --once  # Process one job
php artisan queue:work         # Continuous processing
```

---

## 9. Deployment Architecture

### 9.1 Server Requirements

#### Minimum Requirements

-   **PHP**: 8.2+ (8.3.16 recommended)
-   **Database**: MySQL 8.0+ / PostgreSQL 13+
-   **Web Server**: Apache 2.4+ / Nginx 1.18+
-   **Memory**: 512MB minimum (1GB recommended)
-   **Extensions**: PDO, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath

### 9.2 Environment Configuration

#### Key Environment Variables

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://appointifi.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=appointifi
DB_USERNAME=root
DB_PASSWORD=secret

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525

QUEUE_CONNECTION=redis  # or database
```

### 9.3 Deployment Steps

```bash
# 1. Clone repository
git clone https://github.com/yourusername/appointifi.git
cd appointifi

# 2. Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database migration
php artisan migrate --force

# 5. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 9.4 Production Checklist

-   [ ] `APP_DEBUG=false`
-   [ ] Strong `APP_KEY` generated
-   [ ] Database credentials secured
-   [ ] HTTPS enabled (SSL certificate)
-   [ ] Cache drivers configured (Redis recommended)
-   [ ] Queue worker supervisor process
-   [ ] Log rotation configured
-   [ ] Backup strategy implemented
-   [ ] Monitoring/alerting setup

---

## 10. Performance Considerations

### 10.1 Query Optimization

#### Eager Loading

```php
// Avoid N+1 queries
$appointments = Appointment::with(['customer', 'business', 'service'])->get();
```

#### Selective Column Loading

```php
// Load only needed columns
Business::select(['id', 'name', 'description'])->get();
```

#### Index Strategy

-   Primary keys (automatic)
-   Foreign keys (appointments.customer_id, appointments.business_id, etc.)
-   Query filters (appointments.start_time, appointments.status)

### 10.2 Caching Strategy

#### Application Cache

```php
// Cache business opening hours
Cache::remember("business:{$id}:hours", 3600, function() {
    return $this->location->opening_hours;
});
```

#### Route/Config Caching

```bash
php artisan config:cache   # Cache config files
php artisan route:cache    # Cache routes
php artisan view:cache     # Compile Blade templates
```

### 10.3 Asset Optimization

-   **Vite**: Production builds with minification
-   **CSS**: Tailwind purging unused classes
-   **JS**: Tree-shaking and code splitting
-   **Images**: Compress before upload (future: Laravel Media Library)

### 10.4 Database Optimization

-   **Connection Pooling**: MySQL persistent connections
-   **Read Replicas**: Future scaling (read-heavy operations)
-   **Partitioning**: Future (appointments table by date range)

---

## Appendix A: Technology Decision Rationale

### Why Laravel?

-   **Mature Ecosystem**: Extensive packages, documentation, community
-   **Developer Productivity**: Blade, Eloquent, built-in auth reduce boilerplate
-   **Queue System**: Native async job processing for emails
-   **Migration System**: Version-controlled schema changes

### Why Tailwind CSS?

-   **Utility-First**: Rapid prototyping, consistent design system
-   **Dark Mode**: Built-in class strategy (`dark:` variants)
-   **Customization**: Easy theme configuration via `tailwind.config.js`

### Why Livewire + Alpine.js?

-   **Livewire**: Server-driven reactivity without complex JS framework
-   **Alpine.js**: Minimal client-side interactivity (tabs, dark mode toggle)
-   **Synergy**: Complementary tools (Livewire for data, Alpine for UI state)

### Why Pessimistic Locking?

-   **Simplicity**: No complex retry logic or conflict resolution
-   **Guarantee**: Database-level enforcement of uniqueness
-   **Trade-off**: Acceptable for appointment booking (low contention per business)

---

## Appendix B: Future Architecture Enhancements

### Planned Improvements

1. **Caching Layer**: Redis for session/cache to reduce DB load
2. **Read Replicas**: Scale read-heavy queries (business search)
3. **CDN Integration**: Serve static assets (images, CSS, JS)
4. **API Layer**: RESTful API for mobile app or third-party integrations
5. **Microservices**: Extract notification service (when scale demands)
6. **Elasticsearch**: Full-text search for business/service discovery

### Scalability Roadmap

-   **Horizontal Scaling**: Load-balanced app servers
-   **Database Sharding**: Partition by geographic region (future)
-   **Queue Workers**: Multiple workers for high email volume
-   **Monitoring**: Application Performance Monitoring (APM) tools

---

**Document Prepared By**: Development Team  
**Review Cycle**: Quarterly  
**Next Review Date**: February 2026
