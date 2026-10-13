USE game_developer_applications;

ALTER TABLE applications
    ADD COLUMN position_applied VARCHAR(120) NOT NULL DEFAULT 'General Application' AFTER last_name,
    CHANGE COLUMN programming_languages skills VARCHAR(255) NOT NULL;
