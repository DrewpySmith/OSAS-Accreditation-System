-- Insert Admin User (password: password)
INSERT INTO users (username, password, role, organization_id, is_active, created_at, updated_at) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, 1, NOW(), NOW());

-- Insert Sample Organization
INSERT INTO organizations (name, acronym, description, status, created_at, updated_at) 
VALUES ('Student Council', 'SC', 'Main student governing body of the university', 'active', NOW(), NOW());

-- Insert Organization User (password: password)
INSERT INTO users (username, password, role, organization_id, is_active, created_at, updated_at) 
VALUES ('sc_user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'organization', 1, 1, NOW(), NOW());
