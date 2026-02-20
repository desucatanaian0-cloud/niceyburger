-- Add missing columns to existing users table
ALTER TABLE users ADD COLUMN address TEXT AFTER role;
ALTER TABLE users ADD COLUMN payment_method VARCHAR(100) AFTER address;
