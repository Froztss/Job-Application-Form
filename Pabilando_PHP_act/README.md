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


