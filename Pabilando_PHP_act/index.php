<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require __DIR__ . '/config/database.php';

$fields = [
    'first_name' => '',
    'last_name' => '',
    'position_applied' => '',
    'email' => '',
    'phone' => '',
    'location' => '',
    'education_level' => '',
    'education_training' => '',
    'years_experience' => '0',
    'experience_details' => '',
    'skills' => '',
    'game_engines' => '',
    'game_projects' => '',
    'portfolio_url' => '',
    'resume_url' => '',
    'cover_letter' => '',
    'references_info' => '',
    'availability' => '',
    'work_arrangement' => '',
];
$errors = [];
$notice = '';
$editingId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT) ?: null;
$formTitle = 'Start your application';
$submitLabel = 'Submit application';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], (string) ($_POST['csrf_token'] ?? ''))) {
        http_response_code(400);
        exit('Invalid form token. Refresh the page and try again.');
    }

    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'delete') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            exit('Invalid application ID.');
        }

        $statement = $pdo->prepare('DELETE FROM applications WHERE id = :id');
        $statement->execute(['id' => $id]);
        $_SESSION['flash'] = $statement->rowCount() === 1
            ? 'Application deleted.'
            : 'That application was already removed.';
        header('Location: index.php');
        exit;
    }

    if ($action === 'create' || $action === 'update') {
        foreach ($fields as $name => $default) {
            $fields[$name] = trim((string) ($_POST[$name] ?? $default));
        }
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if ($fields['first_name'] === '' || mb_strlen($fields['first_name']) > 80) {
            $errors['first_name'] = 'Enter a first name of 1 to 80 characters.';
        }
        if ($fields['last_name'] === '' || mb_strlen($fields['last_name']) > 80) {
            $errors['last_name'] = 'Enter a last name of 1 to 80 characters.';
        }
        if ($fields['position_applied'] === '' || mb_strlen($fields['position_applied']) > 120) {
            $errors['position_applied'] = 'Enter the position you are applying for (up to 120 characters).';
        }
        if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($fields['email']) > 190) {
            $errors['email'] = 'Enter a valid email address (up to 190 characters).';
        }
        if ($fields['phone'] === '' || mb_strlen($fields['phone']) > 30) {
            $errors['phone'] = 'Enter a phone number of 1 to 30 characters.';
        }
        if ($fields['location'] === '' || mb_strlen($fields['location']) > 120) {
            $errors['location'] = 'Enter your current location (up to 120 characters).';
        }
        if ($fields['education_level'] === '' || mb_strlen($fields['education_level']) > 100) {
            $errors['education_level'] = 'Enter your education level (up to 100 characters).';
        }
        if (mb_strlen($fields['education_training']) > 2000) {
            $errors['education_training'] = 'Keep education and training details under 2,000 characters.';
        }
        $experience = filter_var($fields['years_experience'], FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => 255],
        ]);
        if ($experience === false) {
            $errors['years_experience'] = 'Enter a whole number from 0 to 255.';
        }
        if (mb_strlen($fields['experience_details']) > 5000) {
            $errors['experience_details'] = 'Keep your experience details under 5,000 characters.';
        }
        if ($fields['skills'] === '' || mb_strlen($fields['skills']) > 1000) {
            $errors['skills'] = 'List your programming languages and technical skills (up to 1,000 characters).';
        }
        if ($fields['game_engines'] === '' || mb_strlen($fields['game_engines']) > 255) {
            $errors['game_engines'] = 'List the game engines you have used (up to 255 characters).';
        }
        if ($fields['game_projects'] === '' || mb_strlen($fields['game_projects']) > 5000) {
            $errors['game_projects'] = 'Describe your game projects in up to 5,000 characters.';
        }
        if ($fields['portfolio_url'] !== '' &&
            (!filter_var($fields['portfolio_url'], FILTER_VALIDATE_URL) || mb_strlen($fields['portfolio_url']) > 255)) {
            $errors['portfolio_url'] = 'Enter a valid portfolio URL of up to 255 characters.';
        }
        if ($fields['resume_url'] !== '' &&
            (!filter_var($fields['resume_url'], FILTER_VALIDATE_URL) || mb_strlen($fields['resume_url']) > 255)) {
            $errors['resume_url'] = 'Enter a valid resume URL of up to 255 characters.';
        }
        if ($fields['cover_letter'] === '') {
            $errors['cover_letter'] = 'Tell us why you are interested in this position.';
        }
        if (mb_strlen($fields['references_info']) > 2000) {
            $errors['references_info'] = 'Keep your references under 2,000 characters.';
        }
        if ($fields['availability'] === '' || mb_strlen($fields['availability']) > 120) {
            $errors['availability'] = 'Enter your availability (up to 120 characters).';
        }
        if (!in_array($fields['work_arrangement'], ['Remote', 'Hybrid', 'On-site', 'Flexible'], true)) {
            $errors['work_arrangement'] = 'Choose a preferred work arrangement.';
        }
        if ($action === 'update' && !$id) {
            $errors['form'] = 'The application to update could not be identified.';
        }

        if (!$errors) {
            $values = [
                'first_name' => $fields['first_name'],
                'last_name' => $fields['last_name'],
                'position_applied' => $fields['position_applied'],
                'email' => $fields['email'],
                'phone' => $fields['phone'],
                'location' => $fields['location'],
                'education_level' => $fields['education_level'],
                'education_training' => $fields['education_training'],
                'years_experience' => $experience,
                'experience_details' => $fields['experience_details'],
                'skills' => $fields['skills'],
                'game_engines' => $fields['game_engines'],
                'game_projects' => $fields['game_projects'],
                'portfolio_url' => $fields['portfolio_url'] !== '' ? $fields['portfolio_url'] : null,
                'resume_url' => $fields['resume_url'] !== '' ? $fields['resume_url'] : null,
                'cover_letter' => $fields['cover_letter'],
                'references_info' => $fields['references_info'] !== '' ? $fields['references_info'] : null,
                'availability' => $fields['availability'],
                'work_arrangement' => $fields['work_arrangement'],
            ];

            if ($action === 'create') {
                $statement = $pdo->prepare(
                    'INSERT INTO applications
                        (first_name, last_name, position_applied, email, phone, location, education_level,
                         education_training, years_experience, experience_details, skills, game_engines, game_projects,
                         portfolio_url, resume_url, cover_letter, references_info, availability, work_arrangement)
                     VALUES
                        (:first_name, :last_name, :position_applied, :email, :phone, :location, :education_level,
                         :education_training, :years_experience, :experience_details, :skills, :game_engines, :game_projects,
                         :portfolio_url, :resume_url, :cover_letter, :references_info, :availability, :work_arrangement)'
                );
                $statement->execute($values);
                $_SESSION['flash'] = 'Application submitted successfully.';
            } else {
                $values['id'] = $id;
                $statement = $pdo->prepare(
                    'UPDATE applications SET
                        first_name = :first_name, last_name = :last_name,
                        position_applied = :position_applied, email = :email,
                        phone = :phone, location = :location,
                        education_level = :education_level, education_training = :education_training,
                        years_experience = :years_experience, experience_details = :experience_details,
                        skills = :skills, game_engines = :game_engines, game_projects = :game_projects,
                        portfolio_url = :portfolio_url, resume_url = :resume_url,
                        cover_letter = :cover_letter, references_info = :references_info,
                        availability = :availability, work_arrangement = :work_arrangement
                     WHERE id = :id'
                );
                $statement->execute($values);
                $_SESSION['flash'] = 'Application updated successfully.';
            }

            header('Location: index.php');
            exit;
        }

        if ($action === 'update') {
            $editingId = $id;
            $formTitle = 'Update your application';
            $submitLabel = 'Save changes';
        }
    }
}

if ($editingId && !$errors) {
    $statement = $pdo->prepare('SELECT * FROM applications WHERE id = :id');
    $statement->execute(['id' => $editingId]);
    $application = $statement->fetch();
    if ($application) {
        foreach ($fields as $name => $default) {
            $fields[$name] = (string) ($application[$name] ?? $default);
        }
        $formTitle = 'Update your application';
        $submitLabel = 'Save changes';
    } else {
        $editingId = null;
        $notice = 'The application you selected no longer exists.';
    }
}

if (isset($_SESSION['flash'])) {
    $notice = (string) $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$applications = $pdo->query(
    'SELECT id, first_name, last_name, position_applied, email, phone, location, education_level,
    education_training, years_experience, experience_details, skills, game_engines, game_projects,
    portfolio_url, resume_url, cover_letter, references_info, availability, work_arrangement, date_added
     FROM applications
     ORDER BY date_added DESC, id DESC'
)->fetchAll();

function e(string|null $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f6f8">
    <title>Game Developer Application</title>
    <link rel="stylesheet" href="assets/style.css?v=9">
</head>
<body>
<header class="site-header">
    <a class="site-title" href="index.php">Game Developer Application</a>
    <a class="header-link" href="#applications">View applications (<?= count($applications) ?>)</a>
</header>

<main>
    <?php if ($notice !== ''): ?>
        <div class="notice" role="status"><?= e($notice) ?></div>
    <?php endif; ?>

    <section class="application-section" id="application-form">
        <div class="section-heading">
            <div><h1><?= e($formTitle) ?></h1><p class="section-description">Apply for a game development opportunity. Fields marked <span>*</span> are required.</p></div>
        </div>
        <form class="application-form" method="post" action="index.php<?= $editingId ? '?edit=' . (int) $editingId : '' ?>">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action" value="<?= $editingId ? 'update' : 'create' ?>">
            <?php if ($editingId): ?><input type="hidden" name="id" value="<?= (int) $editingId ?>"><?php endif; ?>
            <?php if (isset($errors['form'])): ?><p class="form-error form-error-wide"><?= e($errors['form']) ?></p><?php endif; ?>
            <div class="field">
                <label for="first_name">First name <span>*</span></label>
                <input id="first_name" name="first_name" value="<?= e($fields['first_name']) ?>" maxlength="80" autocomplete="given-name" required>
                <?php if (isset($errors['first_name'])): ?><small class="field-error"><?= e($errors['first_name']) ?></small><?php endif; ?>
            </div>
            <div class="field">
                <label for="last_name">Last name <span>*</span></label>
                <input id="last_name" name="last_name" value="<?= e($fields['last_name']) ?>" maxlength="80" autocomplete="family-name" required>
                <?php if (isset($errors['last_name'])): ?><small class="field-error"><?= e($errors['last_name']) ?></small><?php endif; ?>
            </div>
            <div class="field field-wide">
                <label for="position_applied">Preferred area of game development <span>*</span></label>
                <select id="position_applied" name="position_applied" required>
                    <option value="">Choose an area</option>
                    <?php foreach (['Game programming', 'Game design', '2D / 3D art', 'Quality assurance / testing', 'Game audio', 'Other'] as $area): ?>
                        <option value="<?= e($area) ?>" <?= $fields['position_applied'] === $area ? 'selected' : '' ?>><?= e($area) ?></option>
                    <?php endforeach; ?>
                    <?php if ($fields['position_applied'] !== '' && !in_array($fields['position_applied'], ['Game programming', 'Game design', '2D / 3D art', 'Quality assurance / testing', 'Game audio', 'Other'], true)): ?>
                        <option value="<?= e($fields['position_applied']) ?>" selected><?= e($fields['position_applied']) ?></option>
                    <?php endif; ?>
                </select>
                <?php if (isset($errors['position_applied'])): ?><small class="field-error"><?= e($errors['position_applied']) ?></small><?php endif; ?>
            </div>
            <div class="field">
                <label for="email">Email address <span>*</span></label>
                <input id="email" name="email" type="email" value="<?= e($fields['email']) ?>" maxlength="190" autocomplete="email" required>
                <?php if (isset($errors['email'])): ?><small class="field-error"><?= e($errors['email']) ?></small><?php endif; ?>
            </div>
            <div class="field">
                <label for="phone">Phone number <span>*</span></label>
                <input id="phone" name="phone" type="tel" value="<?= e($fields['phone']) ?>" maxlength="30" autocomplete="tel" required>
                <?php if (isset($errors['phone'])): ?><small class="field-error"><?= e($errors['phone']) ?></small><?php endif; ?>
            </div>
            <div class="field">
                <label for="location">Current location <span>*</span></label>
                <input id="location" name="location" value="<?= e($fields['location']) ?>" maxlength="120" autocomplete="address-level2" placeholder="e.g. Manila, Philippines" required>
                <?php if (isset($errors['location'])): ?><small class="field-error"><?= e($errors['location']) ?></small><?php endif; ?>
            </div>
            <div class="field">
                <label for="education_level">Education level <span>*</span></label>
                <select id="education_level" name="education_level" required>
                    <option value="">Choose your highest level</option>
                    <?php foreach (['High school', 'Associate degree', "Bachelor's degree", "Master's degree", 'Doctorate', 'Self-taught / other'] as $level): ?>
                        <option value="<?= e($level) ?>" <?= $fields['education_level'] === $level ? 'selected' : '' ?>><?= e($level) ?></option>
                    <?php endforeach; ?>
                    <?php if ($fields['education_level'] !== '' && !in_array($fields['education_level'], ['High school', 'Associate degree', "Bachelor's degree", "Master's degree", 'Doctorate', 'Self-taught / other'], true)): ?>
                        <option value="<?= e($fields['education_level']) ?>" selected><?= e($fields['education_level']) ?></option>
                    <?php endif; ?>
                </select>
                <?php if (isset($errors['education_level'])): ?><small class="field-error"><?= e($errors['education_level']) ?></small><?php endif; ?>
            </div>
            <div class="field">
                <label for="years_experience">Years of work experience or internships <span>*</span></label>
                <div class="input-suffix"><input id="years_experience" name="years_experience" type="number" min="0" max="255" value="<?= e($fields['years_experience']) ?>" required><span>years</span></div>
                <?php if (isset($errors['years_experience'])): ?><small class="field-error"><?= e($errors['years_experience']) ?></small><?php endif; ?>
            </div>
            <div class="field">
                <label for="skills">Programming languages and technical skills <span>*</span></label>
                <input id="skills" name="skills" value="<?= e($fields['skills']) ?>" maxlength="1000" placeholder="e.g. C#, C++, Lua, 3D math" required>
                <?php if (isset($errors['skills'])): ?><small class="field-error"><?= e($errors['skills']) ?></small><?php endif; ?>
            </div>
            <div class="field field-wide">
                <label for="education_training">Relevant education, courses, or training</label>
                <textarea id="education_training" name="education_training" rows="3" maxlength="2000" placeholder="Include relevant degrees, courses, or training."><?= e($fields['education_training']) ?></textarea>
                <?php if (isset($errors['education_training'])): ?><small class="field-error"><?= e($errors['education_training']) ?></small><?php endif; ?>
            </div>
            <div class="field field-wide">
                <label for="experience_details">Work experience or internships</label>
                <textarea id="experience_details" name="experience_details" rows="3" maxlength="5000" placeholder="Describe relevant roles, internships, or responsibilities."><?= e($fields['experience_details']) ?></textarea>
                <?php if (isset($errors['experience_details'])): ?><small class="field-error"><?= e($errors['experience_details']) ?></small><?php endif; ?>
            </div>
            <div class="field field-wide">
                <label for="game_engines">Game engines and experience <span>*</span></label>
                <input id="game_engines" name="game_engines" value="<?= e($fields['game_engines']) ?>" maxlength="255" placeholder="e.g. Unity (beginner), Unreal Engine (project experience), or None yet" required>
                <?php if (isset($errors['game_engines'])): ?><small class="field-error"><?= e($errors['game_engines']) ?></small><?php endif; ?>
            </div>
            <div class="field field-wide">
                <label for="game_projects">Game projects <span>*</span></label>
                <textarea id="game_projects" name="game_projects" rows="4" maxlength="5000" placeholder="Describe games, prototypes, or practice projects and what you contributed."><?= e($fields['game_projects']) ?></textarea>
                <?php if (isset($errors['game_projects'])): ?><small class="field-error"><?= e($errors['game_projects']) ?></small><?php endif; ?>
            </div>
            <div class="field field-wide">
                <label for="portfolio_url">Game portfolio link</label>
                <input id="portfolio_url" name="portfolio_url" type="url" value="<?= e($fields['portfolio_url']) ?>" maxlength="255" placeholder="https://">
                <?php if (isset($errors['portfolio_url'])): ?><small class="field-error"><?= e($errors['portfolio_url']) ?></small><?php endif; ?>
            </div>
            <div class="field field-wide">
                <label for="resume_url">Resume link</label>
                <input id="resume_url" name="resume_url" type="url" value="<?= e($fields['resume_url']) ?>" maxlength="255" placeholder="https://">
                <?php if (isset($errors['resume_url'])): ?><small class="field-error"><?= e($errors['resume_url']) ?></small><?php endif; ?>
            </div>
            <div class="field field-wide">
                <label for="cover_letter">Cover letter <span>*</span></label>
                <textarea id="cover_letter" name="cover_letter" rows="4" required placeholder="Tell us why you want to become a game developer and why you'd be a good fit."><?= e($fields['cover_letter']) ?></textarea>
                <?php if (isset($errors['cover_letter'])): ?><small class="field-error"><?= e($errors['cover_letter']) ?></small><?php endif; ?>
            </div>
            <div class="field field-wide">
                <label for="references_info">References</label>
                <textarea id="references_info" name="references_info" rows="3" maxlength="2000" placeholder="Optional: name and contact information for professional or academic references."><?= e($fields['references_info']) ?></textarea>
                <?php if (isset($errors['references_info'])): ?><small class="field-error"><?= e($errors['references_info']) ?></small><?php endif; ?>
            </div>
            <div class="field">
                <label for="availability">Availability <span>*</span></label>
                <input id="availability" name="availability" value="<?= e($fields['availability']) ?>" maxlength="120" placeholder="e.g. Immediately, or from June 2026" required>
                <?php if (isset($errors['availability'])): ?><small class="field-error"><?= e($errors['availability']) ?></small><?php endif; ?>
            </div>
            <div class="field">
                <label for="work_arrangement">Preferred work arrangement <span>*</span></label>
                <select id="work_arrangement" name="work_arrangement" required>
                    <option value="">Choose an arrangement</option>
                    <?php foreach (['Remote', 'Hybrid', 'On-site', 'Flexible'] as $arrangement): ?>
                        <option value="<?= e($arrangement) ?>" <?= $fields['work_arrangement'] === $arrangement ? 'selected' : '' ?>><?= e($arrangement) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['work_arrangement'])): ?><small class="field-error"><?= e($errors['work_arrangement']) ?></small><?php endif; ?>
            </div>
            <div class="form-footer field-wide">
                <p>Your details are only used to review this application.</p>
                <div class="form-buttons">
                    <?php if ($editingId): ?><a class="button button-secondary" href="index.php#applications">Cancel</a><?php endif; ?>
                    <button class="button button-primary" type="submit"><?= e($submitLabel) ?> <span aria-hidden="true">→</span></button>
                </div>
            </div>
        </form>
    </section>

    <section class="applications-section" id="applications">
        <div class="section-heading">
            <div><h2>Game developer applications <span class="count-badge"><?= count($applications) ?></span></h2><p class="section-description">Applications submitted through this form.</p></div>
        </div>
        <?php if (!$applications): ?>
            <p class="empty-state">No applications have been submitted yet.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Applicant</th><th>Preferred area</th><th>Contact</th><th>Development skills</th><th>Education</th><th>Work arrangement</th><th>Links</th><th>Applied</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><div class="applicant"><span class="avatar"><?= e(strtoupper(mb_substr($application['first_name'], 0, 1) . mb_substr($application['last_name'], 0, 1))) ?></span><div><strong><?= e($application['first_name'] . ' ' . $application['last_name']) ?></strong><small><?= e($application['location']) ?></small></div></div></td>
                            <td><?= e($application['position_applied']) ?></td>
                            <td><a class="table-link" href="mailto:<?= e($application['email']) ?>"><?= e($application['email']) ?></a><small class="table-subtext"><?= e($application['phone']) ?></small></td>
                            <td><span class="skill-tag"><?= e($application['skills']) ?></span><small class="table-subtext">Engines: <?= e($application['game_engines']) ?></small><small class="table-subtext"><?= (int) $application['years_experience'] ?> years experience</small></td>
                            <td><?= e($application['education_level']) ?><?php if ($application['education_training']): ?><small class="table-subtext"><?= e($application['education_training']) ?></small><?php endif; ?></td>
                            <td><?= e($application['work_arrangement']) ?><small class="table-subtext"><?= e($application['availability']) ?></small></td>
                            <td><?php if ($application['portfolio_url']): ?><a class="table-link" href="<?= e($application['portfolio_url']) ?>" target="_blank" rel="noopener noreferrer">Portfolio</a><?php endif; ?><?php if ($application['resume_url']): ?><?php if ($application['portfolio_url']): ?><br><?php endif; ?><a class="table-link" href="<?= e($application['resume_url']) ?>" target="_blank" rel="noopener noreferrer">Resume</a><?php endif; ?><?php if (!$application['portfolio_url'] && !$application['resume_url']): ?><span class="muted">Not provided</span><?php endif; ?></td>
                            <td><?= e(date('M j, Y', strtotime($application['date_added']))) ?></td>
                            <td><div class="row-actions"><a class="action-edit" href="index.php?edit=<?= (int) $application['id'] ?>#application-form">Edit</a><form method="post" action="index.php" onsubmit="return confirm('Delete this application? This cannot be undone.');"><input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $application['id'] ?>"><button class="action-delete" type="submit">Delete</button></form></div></td>
                        </tr>
                        <tr class="letter-row"><td colspan="9"><span class="letter-label">GAME PROJECTS</span> <?= e($application['game_projects']) ?><br><span class="letter-label">EXPERIENCE</span> <?= e($application['experience_details'] ?: 'Not provided') ?><br><span class="letter-label">COVER LETTER</span> <?= e($application['cover_letter']) ?><?php if ($application['references_info']): ?><br><span class="letter-label">REFERENCES</span> <?= e($application['references_info']) ?><?php endif; ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>

<footer class="site-footer"><span>Game Developer Application</span><span><?= date('Y') ?></span></footer>
</body>
</html>
