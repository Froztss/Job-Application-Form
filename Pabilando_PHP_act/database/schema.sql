CREATE DATABASE IF NOT EXISTS game_developer_applications
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE game_developer_applications;

CREATE TABLE IF NOT EXISTS applications (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    position_applied VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    location VARCHAR(120) NOT NULL,
    education_level VARCHAR(100) NOT NULL,
    education_training TEXT NULL,
    years_experience TINYINT UNSIGNED NOT NULL DEFAULT 0,
    experience_details TEXT NULL,
    skills VARCHAR(1000) NOT NULL,
    game_engines VARCHAR(255) NOT NULL,
    game_projects TEXT NULL,
    portfolio_url VARCHAR(255) NULL,
    resume_url VARCHAR(255) NULL,
    cover_letter TEXT NOT NULL,
    references_info TEXT NULL,
    availability VARCHAR(120) NOT NULL,
    work_arrangement VARCHAR(80) NOT NULL,
    date_added TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_applications_date_added (date_added)
) ENGINE=InnoDB;
