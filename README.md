# Driver Transfer Platform

This project is a PHP + MySQL transportation booking website with a premium responsive landing page, full booking flow, admin dashboard, and API endpoints for operational automation.

## Included

- Public homepage and service pages
- Booking form with backend processing
- Admin dashboard and management screens
- JSON API endpoints for health and data access
- MySQL schema for bookings, vehicles, drivers, routes, and admin users
- Mobile-first responsive design built for phone-first UX

## Project structure

- `index.php` – landing page and booking entry
- `services.php` – service overview page
- `routes.php` – route catalog page
- `fleet.php` – vehicle catalog page
- `pricing.php` – pricing page
- `contact.php` – contact page
- `booking.php` – booking confirmation page
- `admin/` – admin dashboard and management pages
- `api/` – JSON API endpoints
- `includes/` – configuration, DB access, and shared functions
- `assets/css/app.css` – site styling
- `assets/js/app.js` – frontend interactivity
- `db-schema.sql` – starter schema for MySQL
- `docs/technical-plan.md` – architecture reference

## Admin access

Default admin credentials:

- Username: `admin`
- Password: `admin123`

## Local run

A local PHP runtime is required. If PHP is installed, start the site with:

```bash
php -S 127.0.0.1:8000
```

Then open:

- http://127.0.0.1:8000/
- http://127.0.0.1:8000/admin/login.php

## Notes

- The app is built to work with MySQL if the database is configured in environment variables or defaults.
- If MySQL is unavailable, the app falls back to sample data so the storefront and admin screens can still demonstrate the flow.
