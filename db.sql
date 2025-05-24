create database zoomwheels;

create table users(
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL
);

create table rentals(
	rental_id INT AUTO_INCREMENT PRIMARY KEY,
    car_brand VARCHAR(50) NOT NULL,
    car_model VARCHAR(100) NOT NULL,
    car_license_plate VARCHAR(20) NOT NULL UNIQUE,
    car_daily_rate DECIMAL(10, 2) NOT NULL,
    rental_status ENUM('available', 'rented', 'out of service') NOT NULL DEFAULT 'available'
);