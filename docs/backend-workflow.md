# Backend workflow for the ride-hailing platform

## 1. Role model

### Admin
- Login with strong credentials
- Full access to booking, route, driver, vehicle, pricing, payments, reports
- Approve driver verification
- Suspend or ban accounts
- Monitor service health

### Customer
- Register by phone or email
- Verify identity if required by policy
- Create booking request
- Track trip status
- Pay by cash or digital payment
- Review driver and trip

### Driver
- Register and complete KYC
- Upload documents: license, ID, vehicle registration, insurance
- Toggle online/offline state
- Receive trip requests
- Accept or reject booking
- Update status throughout trip
- Share GPS location
- Complete trip and submit end-of-trip details

### Dispatcher (optional)
- Manage failed dispatches
- Assign trips manually
- Handle exceptions and incident escalations

---

## 2. Service and vehicle model

### Service type
- airport_transfer
- private_car
- shared_ride
- limousine
- intercity
- corporate

### Vehicle type
- sedan_4
- suv_7
- van_12
- van_16
- limousine_4
- shuttle_16_plus

### Vehicle fields
- id
- driver_id
- vehicle_type_id
- plate_number
- color
- seats_total
- status
- model
- registration_year
- insurance_expiry
- is_verified

### Rules
- A driver can own one or more vehicles.
- A vehicle belongs to exactly one driver in active service.
- A vehicle is available only if status = active and not under maintenance.
- Shared rides should check remaining seats before accepting booking.

---

## 3. Booking lifecycle

### Booking states
- requested
- matched
- accepted
- driver_arrived
- picked_up
- in_progress
- completed
- cancelled
- timeout
- disputed

### Booking data
- id
- customer_id
- driver_id
- vehicle_id
- service_type
- vehicle_type
- pickup_lat
- pickup_lng
- dropoff_lat
- dropoff_lng
- pickup_address
- dropoff_address
- requested_time
- status
- fare_estimate
- final_fare
- payment_status
- created_at
- updated_at

### Booking rules
- The system must validate pickup/dropoff coordinates and address format.
- The system must check driver availability before assignment.
- The booking must hold a seat inventory for shared rides.
- Payment cannot be marked successful without payment verification.
- Every status transition must be logged in booking_status_history.

---

## 4. Driver state and readiness

### Driver status enums
- offline
- online
- busy
- on_trip
- break
- suspended

### Driver availability rules
- A driver can only receive bookings if status = online and verification_status = verified.
- A driver in on_trip cannot accept new trips.
- GPS last_updated must be fresh to keep a driver available in dispatch.
- If a driver is offline for too long, the system should automatically mark them inactive.

### Driver location table
- id
- driver_id
- latitude
- longitude
- accuracy
- updated_at

---

## 5. Dispatch logic

### Matching priority
1. Same service type
2. Same vehicle type
3. Driver verified
4. Driver online
5. Distance from pickup point
6. Driver rating history
7. Current trip load

### Dispatch algorithm
- Query available drivers in radius around pickup point
- Exclude drivers with status not online or not verified
- Sort by distance ascending, then rating descending
- Send booking request to top drivers
- If no response within timeout, retry with next eligible drivers
- If all fail, mark booking as pending or escalate to dispatcher

### Shared ride logic
- A trip can allow multiple passengers within one vehicle.
- Each passenger consumes one seat.
- Seat availability is checked against current bookings and route constraints.
- Once a shared ride reaches capacity, it should stop accepting additional bookings.

---

## 6. Fare calculation logic

### Formula
- Final fare = base fare + distance fee + waiting fee + time fee + surcharges + service fee + tolls - discounts

### Required components
- base_fee
- per_km_rate
- per_minute_rate
- waiting_fee_per_minute
- night_surcharge
- airport_surcharge
- holiday_surcharge
- service_fee_percent
- minimum_trip_fee

### Example
If the trip request is 12 km and 20 minutes:
- base fare = 15,000
- distance fee = 12 x 8,000 = 96,000
- time fee = 20 x 2,500 = 50,000
- surcharge = 20,000
- service fee = 10,000
- final total = 191,000

### Pricing table design
- pricing_rule_id
- service_type
- vehicle_type
- zone_id
- base_fee
- per_km_rate
- per_minute_rate
- waiting_fee_per_minute
- night_start_hour
- night_end_hour
- night_surcharge
- airport_surcharge
- holiday_surcharge
- min_fare
- active_from
- active_to

### Important rule
- Fare must be snapshotted at booking creation, so later price changes do not alter the customer’s confirmed total.

---

## 7. Google Maps and geolocation integration

### Required Google services
- Places API for auto-complete
- Geocoding API for lat/lng conversion
- Distance Matrix API for distance and ETA
- Directions API for route drawing
- Maps JavaScript API for map visualization in browser

### Real-time location flow
- Driver app or browser sends GPS updates every 10–30 seconds.
- Server stores latest driver coordinates.
- Customer UI displays driver marker on map.
- ETA updates based on route and traffic conditions.
- Admin sees live active trip objects and driver locations.

### Web hook / realtime infrastructure
- Use WebSocket, Firebase, or server push mechanism for live updates.
- Push events include:
  - driver_arrived
  - picked_up
  - completed
  - trip_cancelled
  - payment_received

---

## 8. Payment logic

### Payment methods
- cash
- bank transfer
- VNPay
- MoMo
- ZaloPay
- Stripe

### Payment states
- pending
- authorized
- captured
- failed
- refunded
- disputed

### Rules
- Only a verified payment status should allow the trip to complete in financial reporting.
- Cash trips must still record a payment record and payment status.
- Refunds must have reason codes and admin approval if required.
- Payment records must remain immutable after settlement.

---

## 9. Driver verification and KYC flow

### Verification states
- pending
- approved
- rejected
- suspended

### Required documents
- national ID / passport
- driver license
- vehicle registration
- insurance
- profile photo
- emergency contact

### Rules
- Driver must be approved before online status becomes active.
- Rejected documents require admin notes and re-upload.
- Suspended drivers cannot receive new rides.

---

## 10. Customer real-time booking experience

### Customer status screens
- booking requested
- searching driver
- driver accepted
- driver arrived
- trip in progress
- trip complete

### Customer actions
- cancel booking before driver accepts
- call driver from app or website
- message driver
- track live ETA
- share trip with family
- rate trip after completion

---

## 11. Admin reporting requirements

### Basic reports
- total bookings by day
- revenue by day / month
- completed trips per driver
- driver rating average
- cancellation rate
- vehicle utilization rate
- pending verification count
- payment status summary

### Dashboard widgets
- bookings today
- active drivers
- online drivers
- cash vs digital payment ratio
- cancelled trips
- top routes

---

## 12. Recommended implementation order

### Phase 1
- Role model and authentication
- Driver verification flow
- Vehicle and driver status
- Booking CRUD
- Booking state engine

### Phase 2
- Fare calculation rules
- Route + map integration
- Driver dispatch logic
- GPS tracking
- ETA calculation

### Phase 3
- Payment processing
- Admin reports
- Rating and review
- Notifications and escalation

### Phase 4
- Shared rides and multi-passenger logic
- Campaigns, vouchers, and promotions
- Dispatch optimization and route analytics

---

## 13. Key backend principle

The project should not treat booking as a single form submission. It must be a stateful trip workflow with continuous status transitions, location updates, fare snapshots, payment validation, and admin oversight.

This is the main difference between a simple website booking form and a real ride-hailing platform.
