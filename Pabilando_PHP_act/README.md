# Game Developer Application Form

A simple PHP and MySQL game developer application system with no login or registration. Applicants provide their contact details, preferred game development area, education and training, work experience, programming and technical skills, game engine experience, project details, links, references, availability, and preferred work arrangement. Applications appear in a table where they can be updated or deleted.

## Application fields

The `applications` table contains fields for:

| Column | Purpose |
| --- | --- |
| `id` | Auto-increment primary key |
| `first_name`, `last_name` | Applicant name |
| `position_applied` | Preferred area of game development |
| `email`, `phone` | Contact information |
| `location` | Applicant's current city or location |
| `education_level`, `education_training` | Education and relevant training |
| `years_experience`, `experience_details` | Work experience or internships |
| `skills`, `game_engines` | Programming languages, technical skills, and game engines |
| `game_projects`, `portfolio_url` | Game project details and an optional portfolio link |
| `resume_url`, `cover_letter` | Optional resume link and cover letter |
| `references_info` | Optional professional or academic references |
| `availability`, `work_arrangement` | When the applicant can start and preferred work setup |
| `date_added` | Automatically recorded submission timestamp |

## Run with XAMPP

1. Copy this folder into `C:\xampp\htdocs\Pabilando_PHP_act` (or use the folder name in your XAMPP `htdocs` directory).
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open phpMyAdmin at `http://localhost/phpmyadmin`.
4. For a new setup, import [`database/schema.sql`](database/schema.sql). It creates the database and table.
   If your database already has the earlier applications table, import [`database/migrate_general_job_application.sql`](database/migrate_general_job_application.sql) once, then import [`database/migrate_game_developer_fields.sql`](database/migrate_game_developer_fields.sql) once. If it already has the general job application fields, import only `migrate_game_developer_fields.sql`. These migrations add the game developer fields without deleting existing applications.
5. If your local MySQL credentials differ from the XAMPP defaults, edit the host, database, username, and password at the top of [`config/database.php`](config/database.php).
6. Visit `http://localhost/Pabilando_PHP_act/`.

The database connection uses PDO. Create, update, delete, and list operations use prepared PDO statements. The project needs PHP with `pdo_mysql` and `mbstring` enabled.

## Screenshots for the activity

Open the app in a browser and capture:

1. The application form before submission.
2. The applications table after adding a sample application.
3. The form loaded with an existing application for editing, and the table after saving the changes.
4. The table after deleting an application.

The SQL used to create the table is in [`database/schema.sql`](database/schema.sql). The folder structure is:

```text
Pabilando_PHP_act/
├── assets/
│   └── style.css
├── config/
│   └── database.php
├── database/
│   ├── migrate_game_developer_fields.sql
│   ├── migrate_general_job_application.sql
│   └── schema.sql
├── index.php
└── README.md
```
