-- Driver Transportation Website Template – Schema Blueprint
-- This file is a starter blueprint for the production database design.

CREATE TABLE roles (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL UNIQUE,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(30),
  password_hash VARCHAR(255) NOT NULL,
  avatar_url VARCHAR(255),
  role_id BIGINT,
  is_active BOOLEAN DEFAULT TRUE,
  last_login_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE permissions (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  key_name VARCHAR(100) NOT NULL UNIQUE,
  label VARCHAR(150) NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_role_permissions (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  role_id BIGINT NOT NULL,
  permission_id BIGINT NOT NULL,
  UNIQUE (role_id, permission_id),
  FOREIGN KEY (role_id) REFERENCES roles(id),
  FOREIGN KEY (permission_id) REFERENCES permissions(id)
);

CREATE TABLE business_settings (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  tenant_key VARCHAR(100) NOT NULL UNIQUE,
  brand_name VARCHAR(200) NOT NULL,
  slogan VARCHAR(255),
  logo_light VARCHAR(255),
  logo_dark VARCHAR(255),
  favicon VARCHAR(255),
  theme_name VARCHAR(50) DEFAULT 'luxury-dark',
  primary_color VARCHAR(20),
  secondary_color VARCHAR(20),
  accent_color VARCHAR(20),
  hotline VARCHAR(50),
  zalo VARCHAR(100),
  facebook VARCHAR(255),
  email VARCHAR(150),
  address TEXT,
  google_maps_url TEXT,
  footer_text TEXT,
  seo_title VARCHAR(255),
  seo_description TEXT,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE services (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NOT NULL,
  description TEXT,
  is_active BOOLEAN DEFAULT TRUE,
  is_featured BOOLEAN DEFAULT FALSE,
  image_url VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE vehicle_types (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(120) NOT NULL,
  seat_count INT,
  bed_count INT,
  description TEXT,
  is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE vehicles (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  vehicle_type_id BIGINT,
  name VARCHAR(150) NOT NULL,
  brand VARCHAR(100),
  model VARCHAR(100),
  license_plate VARCHAR(50),
  seat_count INT,
  bed_count INT,
  image_url VARCHAR(255),
  status VARCHAR(50) DEFAULT 'available',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (vehicle_type_id) REFERENCES vehicle_types(id)
);

CREATE TABLE seat_layouts (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  vehicle_id BIGINT NOT NULL,
  layout_json JSON NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

CREATE TABLE drivers (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  full_name VARCHAR(150) NOT NULL,
  phone VARCHAR(30),
  photo_url VARCHAR(255),
  status VARCHAR(50) DEFAULT 'available',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE routes (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  name VARCHAR(200) NOT NULL,
  departure_city VARCHAR(120),
  arrival_city VARCHAR(120),
  slug VARCHAR(200) NOT NULL,
  distance_km DECIMAL(10,2),
  estimated_duration_minutes INT,
  is_active BOOLEAN DEFAULT TRUE,
  seo_title VARCHAR(255),
  seo_description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE pickup_points (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  route_id BIGINT NOT NULL,
  name VARCHAR(200) NOT NULL,
  address TEXT,
  latitude DECIMAL(10,8),
  longitude DECIMAL(11,8),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (route_id) REFERENCES routes(id)
);

CREATE TABLE dropoff_points (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  route_id BIGINT NOT NULL,
  name VARCHAR(200) NOT NULL,
  address TEXT,
  latitude DECIMAL(10,8),
  longitude DECIMAL(11,8),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (route_id) REFERENCES routes(id)
);

CREATE TABLE trips (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  route_id BIGINT NOT NULL,
  vehicle_id BIGINT,
  driver_id BIGINT,
  service_id BIGINT,
  departure_datetime DATETIME NOT NULL,
  arrival_datetime DATETIME,
  status VARCHAR(50) DEFAULT 'scheduled',
  total_capacity INT,
  available_seats INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (route_id) REFERENCES routes(id),
  FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
  FOREIGN KEY (driver_id) REFERENCES drivers(id),
  FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE trip_seats (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  trip_id BIGINT NOT NULL,
  seat_code VARCHAR(20) NOT NULL,
  status VARCHAR(30) DEFAULT 'available',
  hold_until DATETIME NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE (trip_id, seat_code),
  FOREIGN KEY (trip_id) REFERENCES trips(id)
);

CREATE TABLE customers (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  full_name VARCHAR(150) NOT NULL,
  phone VARCHAR(30),
  email VARCHAR(150),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  booking_code VARCHAR(50) NOT NULL UNIQUE,
  customer_id BIGINT,
  service_id BIGINT,
  route_id BIGINT,
  trip_id BIGINT,
  vehicle_id BIGINT,
  status VARCHAR(50) DEFAULT 'draft',
  pickup_point VARCHAR(200),
  dropoff_point VARCHAR(200),
  departure_datetime DATETIME,
  passenger_count INT DEFAULT 1,
  notes TEXT,
  subtotal DECIMAL(12,2) DEFAULT 0,
  discount DECIMAL(12,2) DEFAULT 0,
  fees DECIMAL(12,2) DEFAULT 0,
  total_amount DECIMAL(12,2) DEFAULT 0,
  payment_status VARCHAR(50) DEFAULT 'unpaid',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id),
  FOREIGN KEY (service_id) REFERENCES services(id),
  FOREIGN KEY (route_id) REFERENCES routes(id),
  FOREIGN KEY (trip_id) REFERENCES trips(id),
  FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

CREATE TABLE booking_passengers (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  booking_id BIGINT NOT NULL,
  full_name VARCHAR(150) NOT NULL,
  phone VARCHAR(30),
  email VARCHAR(150),
  passenger_type VARCHAR(50) DEFAULT 'adult',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

CREATE TABLE booking_seats (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  booking_id BIGINT NOT NULL,
  trip_id BIGINT NOT NULL,
  seat_code VARCHAR(20) NOT NULL,
  status VARCHAR(30) DEFAULT 'reserved',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (booking_id, seat_code),
  FOREIGN KEY (booking_id) REFERENCES bookings(id),
  FOREIGN KEY (trip_id) REFERENCES trips(id)
);

CREATE TABLE booking_status_history (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  booking_id BIGINT NOT NULL,
  old_status VARCHAR(50),
  new_status VARCHAR(50) NOT NULL,
  changed_by BIGINT,
  note TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id),
  FOREIGN KEY (changed_by) REFERENCES users(id)
);

CREATE TABLE pricing_rules (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  service_id BIGINT,
  route_id BIGINT,
  vehicle_type_id BIGINT,
  pricing_type VARCHAR(50) NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  currency VARCHAR(10) DEFAULT 'VND',
  valid_from DATETIME,
  valid_to DATETIME,
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE booking_price_snapshots (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  booking_id BIGINT NOT NULL,
  snapshot_json JSON NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

CREATE TABLE payments (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  booking_id BIGINT NOT NULL,
  payment_code VARCHAR(60) NOT NULL UNIQUE,
  amount DECIMAL(12,2) NOT NULL,
  payment_method VARCHAR(50) NOT NULL,
  payment_status VARCHAR(50) DEFAULT 'pending',
  provider VARCHAR(100),
  provider_reference VARCHAR(150),
  notes TEXT,
  verified_by BIGINT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id),
  FOREIGN KEY (verified_by) REFERENCES users(id)
);

CREATE TABLE refunds (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  booking_id BIGINT NOT NULL,
  payment_id BIGINT,
  amount DECIMAL(12,2) NOT NULL,
  reason TEXT,
  status VARCHAR(50) DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id),
  FOREIGN KEY (payment_id) REFERENCES payments(id)
);

CREATE TABLE reviews (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  customer_name VARCHAR(120) NOT NULL,
  service_id BIGINT,
  rating INT NOT NULL,
  review_text TEXT NOT NULL,
  is_approved BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE media (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  media_type VARCHAR(50) DEFAULT 'image',
  title VARCHAR(200),
  file_path VARCHAR(255) NOT NULL,
  alt_text TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pages (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  slug VARCHAR(150) NOT NULL UNIQUE,
  title VARCHAR(200) NOT NULL,
  content LONGTEXT,
  meta_title VARCHAR(255),
  meta_description TEXT,
  status VARCHAR(20) DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE blog_posts (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  excerpt TEXT,
  content LONGTEXT,
  thumbnail_url VARCHAR(255),
  status VARCHAR(30) DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE faqs (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  question TEXT NOT NULL,
  answer TEXT NOT NULL,
  sort_order INT DEFAULT 0,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notifications (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  type VARCHAR(80) NOT NULL,
  channel VARCHAR(50),
  payload JSON,
  status VARCHAR(30) DEFAULT 'queued',
  sent_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE audit_logs (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  business_id BIGINT NOT NULL,
  actor_id BIGINT,
  action VARCHAR(120) NOT NULL,
  entity_type VARCHAR(120),
  entity_id BIGINT,
  payload JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (actor_id) REFERENCES users(id)
);

-- Key indexes for performance
CREATE INDEX idx_services_business ON services(business_id);
CREATE INDEX idx_trips_business_route ON trips(business_id, route_id);
CREATE INDEX idx_trip_seats_trip_status ON trip_seats(trip_id, status);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_bookings_business_date ON bookings(business_id, created_at);
CREATE INDEX idx_payments_booking ON payments(booking_id);
CREATE INDEX idx_reviews_approved ON reviews(is_approved);
