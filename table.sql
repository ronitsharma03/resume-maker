CREATE TABLE resumedata (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Basic / About Section Fields
    first_name    VARCHAR(100) NOT NULL,
    middle_name   VARCHAR(100) DEFAULT NULL,
    last_name     VARCHAR(100) NOT NULL,
    image_path    VARCHAR(255) DEFAULT NULL,
    designation   VARCHAR(100) DEFAULT NULL,
    address       VARCHAR(255) DEFAULT NULL,
    email         VARCHAR(100) NOT NULL,
    phone_no      VARCHAR(50) DEFAULT NULL,
    summary       TEXT DEFAULT NULL,

    -- Repeated sections stored as JSON
    achievements  JSON DEFAULT NULL,
    experiences   JSON DEFAULT NULL,
    educations    JSON DEFAULT NULL,
    projects      JSON DEFAULT NULL,
    skills        JSON DEFAULT NULL,

    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

