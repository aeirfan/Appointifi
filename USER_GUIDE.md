# Appointifi - User Guide

## Document Information

| Field              | Value                                   |
| ------------------ | --------------------------------------- |
| **Document Title** | User Guide                              |
| **Project**        | Appointifi Appointment Booking System   |
| **Version**        | 1.0                                     |
| **Date**           | November 20, 2025                       |
| **Audience**       | End Users (Customers & Business Owners) |

---

## Table of Contents

1. [Introduction](#1-introduction)
2. [Getting Started](#2-getting-started)
3. [Customer Guide](#3-customer-guide)
4. [Business Owner Guide](#4-business-owner-guide)
5. [Account Settings](#5-account-settings)
6. [Troubleshooting](#6-troubleshooting)
7. [FAQ](#7-faq)

---

## 1. Introduction

### 1.1 Welcome to Appointifi

Appointifi is an online appointment booking platform that connects customers with local service businesses. Whether you're looking to book a haircut, a massage, or a consultation, Appointifi makes it easy to find available time slots and manage your appointments.

### 1.2 Who is Appointifi For?

-   **Customers**: Browse businesses, view availability in real-time, and book appointments online
-   **Business Owners**: Manage your business profile, services, schedules, and customer appointments

### 1.3 System Requirements

-   **Browser**: Chrome, Firefox, Safari, or Edge (latest version)
-   **Internet Connection**: Broadband connection recommended
-   **Device**: Desktop, laptop, tablet, or mobile phone
-   **Screen Resolution**: Optimized for all screen sizes (responsive design)

### 1.4 Getting Help

If you encounter any issues or have questions:

-   Check the [FAQ section](#7-faq)
-   Review the [Troubleshooting guide](#6-troubleshooting)
-   Contact support at support@appointifi.com

---

## 2. Getting Started

### 2.1 Creating an Account

#### Step 1: Navigate to Registration Page

1. Visit the Appointifi homepage
2. Click the **Register** button in the top navigation bar

#### Step 2: Choose Your Role

-   **Customer**: For individuals looking to book appointments
-   **Business Owner**: For service providers managing a business

#### Step 3: Fill Out Registration Form

**Required Information**:

-   Full Name
-   Email Address
-   Password (minimum 8 characters)
-   Confirm Password
-   Role Selection (Customer or Business Owner)

#### Step 4: Complete Registration

1. Click the **Register** button
2. Check your email for a verification link
3. Click the verification link to activate your account

**⚠️ Important**: You must verify your email before you can book appointments or create a business profile.

### 2.2 Logging In

#### Step 1: Access Login Page

1. Click **Login** in the navigation bar
2. Or visit `/login` directly

#### Step 2: Enter Credentials

-   Email Address
-   Password

#### Step 3: Submit

1. Click **Login**
2. You'll be redirected to your dashboard based on your role:
    - Customers → Business search page
    - Business Owners → Business dashboard

### 2.3 Password Reset

#### Forgot Your Password?

1. Click **Forgot your password?** on the login page
2. Enter your email address
3. Click **Email Password Reset Link**
4. Check your email for the reset link
5. Click the link and enter a new password
6. Confirm your new password
7. Click **Reset Password**

---

## 3. Customer Guide

### 3.1 Searching for Businesses

#### Basic Search

1. From the homepage or `/businesses`, enter search terms:
    - Business name
    - Service type (e.g., "haircut", "massage")
2. Optionally, enter a location (city or address)
3. Click **Search**

#### Search Results

Each business card displays:

-   Business name
-   Description
-   Location (city)
-   Distance from your search location (if provided)

#### Sorting Options

-   **Name**: Alphabetical order
-   **Distance**: Nearest first (requires location input)

**💡 Tip**: Use the **Clear** button to reset all search filters.

### 3.2 Viewing Business Details

#### Access Business Profile

1. From search results, click **View Details** on any business card
2. Or visit `/businesses/{id}` directly

#### Business Profile Includes

-   **Business Information**: Name, description
-   **Location**: Full address
-   **Opening Hours**: Daily schedule (displayed in table format)
-   **Services**: List of available services with:
    -   Service name
    -   Description
    -   Duration (e.g., "60 minutes")
    -   Price (if available)

### 3.3 Checking Availability

#### Step 1: Select a Service

1. On the business profile page, find the service you want
2. Click **Book Now**

#### Step 2: Choose a Date

1. You'll see a date picker (defaults to tomorrow)
2. Select your preferred date
3. Click **Check Availability**

#### Step 3: View Available Slots

-   **Available Slots**: Displayed as clickable time buttons
-   **Unavailable**: Message explains why (e.g., "Closed on this day", "No slots available")

**Example Time Slots**:

```
09:00 AM   10:00 AM   11:00 AM   02:00 PM   03:00 PM
```

**⚠️ Note**: Slots are only shown if they start at least 30 minutes in the future.

### 3.4 Booking an Appointment

#### Step 1: Select a Time Slot

1. Click on your desired time slot button
2. Confirm the booking details:
    - Service name
    - Date and time
    - Business name
    - Duration

#### Step 2: Confirm Booking

1. Review the details
2. Click **Confirm Booking**

#### Step 3: Confirmation

-   You'll see a success message
-   You'll receive a confirmation email with appointment details
-   The business owner also receives a notification

**✉️ Email Includes**:

-   Appointment date and time
-   Service details
-   Business contact information

### 3.5 Managing Your Bookings

#### Viewing Your Appointments

1. Click **My Bookings** in the navigation bar
2. Or visit `/my-bookings`

#### Bookings Organization

Your appointments are organized into three tabs:

**Upcoming**:

-   Future appointments with status "Confirmed" or "Arrival"
-   Shows appointment details and **Cancel** button

**Completed**:

-   Past appointments with status "Completed"
-   No actions available (historical record)

**Cancelled**:

-   Appointments you or the business cancelled
-   Shows cancellation status

#### Appointment Card Details

Each card displays:

-   Business name
-   Service name
-   Date and time
-   Duration
-   Status badge (color-coded)

### 3.6 Cancelling an Appointment

#### Cancellation Requirements

-   Only **Upcoming** appointments can be cancelled
-   You must be the person who booked the appointment
-   Appointment must be in the future

#### Cancellation Steps

1. Go to **My Bookings**
2. Find the appointment in the **Upcoming** tab
3. Click **Cancel Appointment**
4. Confirm the cancellation in the popup dialog
5. Receive confirmation message

#### After Cancellation

-   Appointment moves to **Cancelled** tab
-   You receive a cancellation email
-   Business owner receives a notification
-   Time slot becomes available for other customers

**⚠️ Warning**: Cancellations are immediate and cannot be undone.

---

## 4. Business Owner Guide

### 4.1 Creating Your Business Profile

#### First-Time Setup

After registering as a business owner, you'll be prompted to create your business profile.

#### Step 1: Access Profile Creation

1. Click **Create Business Profile** from the dashboard
2. Or visit `/business/profile/create`

#### Step 2: Enter Business Details

**Required Information**:

-   **Business Name**: Your official business name
-   **Description**: Brief overview of your services (optional)
-   **Address**: Street address
-   **City**: City name

**💡 Tip**: Provide a complete address for accurate location mapping.

#### Step 3: Set Opening Hours

For each day of the week:

-   **Leave blank** if you're closed that day
-   **Enter times** in HH:MM format (24-hour or 12-hour with AM/PM)

**Example**:

```
Monday:    09:00 AM - 05:00 PM
Tuesday:   09:00 AM - 05:00 PM
Wednesday: (leave blank - closed)
Thursday:  10:00 AM - 06:00 PM
```

**⚠️ Important**: Opening hours determine when customers can book appointments.

#### Step 4: Submit

1. Review all information
2. Click **Create Business**
3. Your profile is now live!

**What Happens Next**:

-   Your address is geocoded for map display
-   Your business appears in customer searches
-   You can now add services

### 4.2 Managing Business Profile

#### Editing Business Information

1. Navigate to **Dashboard**
2. Click **Edit Profile**
3. Modify business name, description, address, city
4. Click **Update**

#### Editing Location and Hours

1. From the dashboard, click **Edit Location**
2. Update address and/or opening hours
3. Click **Update Location**

**📍 Note**: Changing the address will trigger re-geocoding for accurate location mapping.

### 4.3 Managing Services

#### Viewing Services

1. Go to **Dashboard**
2. Click **Manage Services**
3. Or visit `/business/services`

#### Creating a Service

1. Click **Create New Service**
2. Fill in the form:
    - **Name**: Service name (e.g., "60-Minute Massage")
    - **Description**: What's included (optional)
    - **Duration**: Length in minutes (e.g., 60)
    - **Price**: Cost in your currency (optional)
3. Click **Create Service**

**Example Service**:

```
Name:        Deep Tissue Massage
Description: Therapeutic massage targeting muscle tension
Duration:    90 minutes
Price:       $120.00
```

#### Editing a Service

1. From the services list, click **Edit** next to the service
2. Modify any field
3. Click **Update Service**

#### Deleting a Service

1. Click **Delete** next to the service
2. Confirm deletion

**⚠️ Warning**: You cannot delete a service that has future appointments. Cancel or complete those appointments first.

### 4.4 Viewing Appointments

#### Accessing Appointments

1. From the dashboard, click **View Appointments**
2. Or visit `/business/appointments`

#### Appointments Organization

Appointments are organized into two tabs:

**Upcoming**:

-   Future appointments (today and beyond)
-   Includes statuses: Confirmed, Arrival

**Past**:

-   Historical appointments
-   Includes statuses: Completed, Cancelled, No Show

#### Appointment Card Details

-   Customer name and email
-   Service name
-   Date and time
-   Duration
-   Current status
-   **Status Update** dropdown

### 4.5 Managing Appointment Status

#### Status Workflow

Appointments follow this lifecycle:

```
Confirmed → Arrival → Completed
          ↘ No Show
```

**Status Definitions**:

-   **Confirmed**: Booking confirmed, customer expected
-   **Arrival**: Customer has arrived (check-in)
-   **Completed**: Service successfully delivered
-   **No Show**: Customer did not arrive
-   **Cancelled**: Booking cancelled by customer or owner

#### Updating Status

1. Find the appointment in **View Appointments**
2. Click the **Status** dropdown
3. Select new status:
    - **Arrival**: When customer checks in
    - **Completed**: When service is finished
    - **No Show**: If customer doesn't show up
4. Status updates automatically

**💡 Best Practice**: Update status as appointments progress to maintain accurate records.

### 4.6 Managing Holidays

#### Purpose

Mark days when your business is closed (e.g., public holidays, vacation days).

#### Adding a Holiday

1. From **Edit Profile**, scroll to **Holidays** section
2. Enter the date (YYYY-MM-DD format or use date picker)
3. Optionally, add a name (e.g., "Christmas Day")
4. Click **Add Holiday**

#### Viewing Holidays

All holidays are listed in the Holidays section, sorted by date.

#### Removing a Holiday

1. Find the holiday in the list
2. Click **Remove**
3. Holiday is deleted immediately

**Effect**: Customers cannot book appointments on holiday dates. Availability checker shows "Business is closed on this day."

### 4.7 Managing Recurring Blocked Times

#### Purpose

Block off recurring time slots each week (e.g., lunch breaks, administrative tasks).

#### Adding a Recurring Block

1. From **Edit Profile**, scroll to **Recurring Blocked Times** section
2. Fill in the form:
    - **Title**: Description (e.g., "Lunch Break")
    - **Start Time**: When block starts (e.g., 12:00 PM)
    - **End Time**: When block ends (e.g., 01:00 PM)
    - **Days of Week**: Check days this block applies to
3. Click **Add Recurring Block**

**Example**:

```
Title:       Team Meeting
Start Time:  09:00 AM
End Time:    10:00 AM
Days:        ☑ Monday  ☑ Friday
```

#### Viewing Recurring Blocks

All blocks are listed, showing title, time range, and applicable days.

#### Removing a Recurring Block

1. Find the block in the list
2. Click **Remove**
3. Block is deleted immediately

**Effect**: Customers cannot book appointments during blocked times on specified days.

### 4.8 Business Dashboard Overview

#### Dashboard Components

When you log in, you'll see:

**Quick Stats**:

-   Total services offered
-   Recent appointment count

**Quick Actions**:

-   Edit Profile
-   Manage Services
-   View Appointments
-   Edit Location

**Recent Appointments**:

-   List of upcoming appointments
-   Quick access to customer details

**💡 Tip**: Use the dashboard as your daily starting point to monitor bookings and manage operations.

---

## 5. Account Settings

### 5.1 Dark Mode Toggle

#### Enabling Dark Mode

1. Look for the sun/moon icon in the navigation bar
2. Click to toggle between light and dark themes
3. Your preference is saved automatically

**Benefits**:

-   Reduced eye strain in low-light environments
-   Battery savings on OLED screens
-   Personal preference customization

### 5.2 Updating Your Profile

#### Changing Your Name

1. Go to **Profile** (link in navigation)
2. Update the **Name** field
3. Click **Save**

#### Changing Your Email

**⚠️ Not Currently Supported**: Email changes require admin assistance for security. Contact support.

### 5.3 Changing Your Password

#### Step 1: Access Password Settings

1. Click **Profile** in navigation
2. Scroll to **Update Password** section

#### Step 2: Enter Passwords

-   **Current Password**: Your existing password
-   **New Password**: Your new password (minimum 8 characters)
-   **Confirm Password**: Re-enter new password

#### Step 3: Save

1. Click **Save**
2. You'll receive a confirmation message
3. Use the new password for future logins

**🔒 Security Tip**: Use a strong password with a mix of letters, numbers, and symbols.

### 5.4 Deleting Your Account

**⚠️ Warning**: Account deletion is permanent and cannot be undone.

#### Before Deleting

-   **Customers**: Cancel all upcoming appointments
-   **Business Owners**: Complete or cancel all customer appointments
-   Download any data you wish to keep

#### Deletion Process

1. Go to **Profile**
2. Scroll to **Delete Account** section
3. Confirm your password
4. Click **Delete Account**
5. Confirm in the popup dialog

**What Gets Deleted**:

-   Your user account
-   Business profile (if owner)
-   All associated data

**⚠️ Note**: Deletion is irreversible. Consider carefully before proceeding.

---

## 6. Troubleshooting

### 6.1 Common Issues

#### Issue: "Email already registered"

**Cause**: Email is already associated with an account.

**Solution**:

1. Try logging in instead
2. If you forgot your password, use the password reset link
3. If you believe this is an error, contact support

#### Issue: "No available slots"

**Possible Causes**:

-   Business is closed on selected date
-   All slots are booked
-   Selected date is a holiday
-   Slots conflict with recurring blocked times

**Solution**:

1. Try a different date
2. Check business opening hours
3. Contact the business directly for availability

#### Issue: "Unable to cancel appointment"

**Possible Causes**:

-   Appointment is in the past
-   You're not the appointment owner
-   Appointment is already cancelled

**Solution**:

1. Verify appointment details
2. Check if you're logged in as the correct user
3. Contact the business for manual cancellation

#### Issue: "Service cannot be deleted"

**Cause**: Service has future appointments.

**Solution**:

1. Wait for appointments to complete
2. Or cancel future appointments first
3. Then delete the service

### 6.2 Email Issues

#### Not Receiving Emails

**Check**:

1. Spam/junk folder
2. Email address spelling in your profile
3. Email server settings (if using custom domain)

**Solution**:

-   Add support@appointifi.com to your contacts
-   Check with your email provider
-   Update your email address in profile settings

### 6.3 Login Problems

#### "Invalid credentials"

**Solution**:

1. Double-check email and password
2. Ensure Caps Lock is off
3. Use password reset if needed

#### Session Timeout

**Cause**: Inactivity for extended period.

**Solution**:

-   Simply log in again
-   Your data is safe

### 6.4 Browser Compatibility

#### Display Issues

**Solution**:

1. Clear browser cache and cookies
2. Update browser to latest version
3. Try a different browser (Chrome, Firefox, Safari, Edge)
4. Disable browser extensions that may interfere

---

## 7. FAQ

### General Questions

**Q: Is Appointifi free to use?**  
A: Yes, both customers and business owners can use Appointifi for free during the MVP phase.

**Q: Do I need to download an app?**  
A: No, Appointifi is a web-based platform accessible from any browser on any device.

**Q: Can I use Appointifi on my mobile phone?**  
A: Yes! Appointifi is fully responsive and optimized for mobile devices.

### Customer Questions

**Q: How far in advance can I book appointments?**  
A: You can book as far ahead as the business's schedule allows. However, you must book at least 30 minutes in the future.

**Q: Can I modify an appointment instead of cancelling?**  
A: Not currently. You'll need to cancel and create a new booking for a different time.

**Q: Will I be charged for appointments?**  
A: Payment processing is not currently integrated. Contact the business directly for payment arrangements.

**Q: How will I be reminded of my appointment?**  
A: You'll receive an email confirmation when you book. Additional reminder features (email/SMS) are planned for future releases.

### Business Owner Questions

**Q: Can I have multiple locations?**  
A: Not in the current version. Multi-location support is planned for a future release.

**Q: How do I accept payment through Appointifi?**  
A: Payment integration (Stripe, PayPal) is not yet available. Handle payments directly with customers.

**Q: Can I set different hours for each service?**  
A: No, opening hours apply to all services. Use recurring blocked times to restrict specific time periods.

**Q: What happens if two customers try to book the same slot?**  
A: Appointifi uses pessimistic locking to prevent double-bookings. The first customer to confirm gets the slot; the second will see an error message.

**Q: Can I export my appointment data?**  
A: Export functionality is not currently available but is planned for a future release.

**Q: How many services can I offer?**  
A: There's no limit on the number of services you can create.

### Technical Questions

**Q: What browsers are supported?**  
A: Chrome, Firefox, Safari, and Edge (latest versions). Internet Explorer is not supported.

**Q: Is my data secure?**  
A: Yes. Appointifi uses industry-standard security practices including password hashing, CSRF protection, and SQL injection prevention.

**Q: Can I integrate Appointifi with my Google Calendar?**  
A: Calendar integration is not currently available but is planned for a future release.

**Q: Does Appointifi work offline?**  
A: No, an internet connection is required to use Appointifi.

### Account Questions

**Q: Can I change my role from Customer to Business Owner?**  
A: Not directly. You'll need to create a new account with the Business Owner role.

**Q: What happens to my bookings if I delete my account?**  
A: All your bookings and associated data will be permanently deleted.

**Q: Can I recover a deleted account?**  
A: No, account deletion is permanent and cannot be undone.

---

## 8. Contact & Support

### Getting Help

-   **Email Support**: support@appointifi.com
-   **Response Time**: Within 24-48 hours

### Feedback

We value your feedback! Share your suggestions and ideas:

-   **Email**: feedback@appointifi.com

### Report a Bug

Found an issue? Let us know:

-   **Email**: bugs@appointifi.com
-   **Include**: Screenshots, browser version, steps to reproduce

---

## 9. Appendix

### Keyboard Shortcuts

| Action                  | Shortcut |
| ----------------------- | -------- |
| Navigate to search      | Alt + S  |
| Navigate to my bookings | Alt + B  |
| Toggle dark mode        | Alt + D  |
| Open help               | Alt + H  |

**Note**: Keyboard shortcuts may vary by browser.

### Status Badge Colors

| Status    | Color  | Meaning                |
| --------- | ------ | ---------------------- |
| Confirmed | Blue   | Booking confirmed      |
| Arrival   | Green  | Customer checked in    |
| Completed | Gray   | Service completed      |
| Cancelled | Red    | Booking cancelled      |
| No Show   | Orange | Customer didn't arrive |

### Time Format Guide

-   **12-Hour Format**: 09:00 AM, 02:30 PM
-   **24-Hour Format**: 09:00, 14:30
-   Both formats are accepted in opening hours and recurring blocks

### Best Practices

#### For Customers

✅ Book appointments at least 1 hour in advance  
✅ Cancel promptly if you can't make it  
✅ Check your email for confirmations  
✅ Verify business opening hours before booking  
✅ Provide accurate contact information

#### For Business Owners

✅ Keep opening hours up to date  
✅ Add holidays in advance  
✅ Update appointment statuses promptly  
✅ Respond to customer inquiries quickly  
✅ Set realistic service durations  
✅ Use recurring blocks for regular breaks  
✅ Review your schedule daily

---

**Document Version**: 1.0  
**Last Updated**: November 20, 2025  
**Next Review**: February 2026

**Thank you for using Appointifi!**
