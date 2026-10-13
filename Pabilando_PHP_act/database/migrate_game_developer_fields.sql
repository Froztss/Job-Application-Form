USE game_developer_applications;

ALTER TABLE applications
    MODIFY COLUMN skills VARCHAR(1000) NOT NULL,
    ADD COLUMN education_training TEXT NULL AFTER education_level,
    ADD COLUMN experience_details TEXT NULL AFTER years_experience,
    ADD COLUMN game_engines VARCHAR(255) NOT NULL DEFAULT '' AFTER skills,
    ADD COLUMN game_projects TEXT NULL AFTER game_engines,
    ADD COLUMN resume_url VARCHAR(255) NULL AFTER portfolio_url,
    ADD COLUMN references_info TEXT NULL AFTER cover_letter,
    ADD COLUMN availability VARCHAR(120) NOT NULL DEFAULT '' AFTER references_info,
    ADD COLUMN work_arrangement VARCHAR(80) NOT NULL DEFAULT '' AFTER availability;
