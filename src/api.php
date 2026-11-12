<?php

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
    'httponly' => true,
    'secure' => $isHttps,
    'samesite' => 'Lax',
]);
session_start();
require_once __DIR__ . '/includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'] ?? '';

if ($method !== 'GET' && $method !== 'POST') {
    header('Allow: GET, POST');
    respond(405, ['error' => 'Method not allowed.']);
}

try {
    if ($method === 'GET') {
        if (!isset($_SESSION['user_id'])) {
            respond(401, ['error' => 'Please sign in.']);
        }

        $pdo = connectToDatabase();
        $user = findUser($pdo, 'user_id', (int) $_SESSION['user_id']);
        if ($user === null) {
            unset($_SESSION['user_id']);
            respond(401, ['error' => 'Please sign in.']);
        }

        if (($_GET['resource'] ?? '') === 'bmi') {
            respond(200, ['bmi' => calculateBMI($pdo, (int) $user['user_id'])]);
        }

        if (($_GET['resource'] ?? '') === 'goals') {
            $statement = $pdo->prepare(
                'SELECT id, goal_name, category, goal, start_date, done_by_date, goal_done
                 FROM goals
                 WHERE user_id = :user_id
                 ORDER BY start_date ASC, id DESC'
            );
            $statement->execute(['user_id' => (int) $user['user_id']]);
            $goals = $statement->fetchAll();
            foreach ($goals as &$savedGoal) {
                $savedGoal['goal'] = (float) $savedGoal['goal'];
                $savedGoal['goal_done'] = (bool) ((int) $savedGoal['goal_done']);
                $savedGoal['steps_average'] = $savedGoal['category'] === 'steps'
                    ? averageDailyValueForGoal(
                        $pdo,
                        (int) $user['user_id'],
                        'steps',
                        $savedGoal['start_date'],
                        $savedGoal['done_by_date']
                    )
                    : null;
                $savedGoal['sleep_average'] = $savedGoal['category'] === 'sleep'
                    ? averageDailyValueForGoal(
                        $pdo,
                        (int) $user['user_id'],
                        'sleep',
                        $savedGoal['start_date'],
                        $savedGoal['done_by_date']
                    )
                    : null;
            }
            unset($savedGoal);
            respond(200, ['goals' => $goals]);
        }

        if (($_GET['resource'] ?? '') === 'health_markers') {
            $statement = $pdo->prepare(
                'SELECT id, category, measurement, date
                 FROM health_markers
                 WHERE user_id = :user_id
                 ORDER BY date DESC, id DESC'
            );
            $statement->execute(['user_id' => (int) $user['user_id']]);
            $entries = $statement->fetchAll();
            $heightCm = is_numeric($user['length']) ? (float) $user['length'] : null;
            $weightEntries = [];
            foreach ($entries as $candidate) {
                if ($candidate['category'] === 'weight') {
                    $weightEntries[] = $candidate;
                }
            }
            foreach ($entries as &$entry) {
                if ($entry['category'] === 'weight') {
                    $entry['bmi'] = is_numeric($entry['measurement']) && $heightCm !== null
                        ? calculateBMI(
                            $pdo,
                            (int) $user['user_id'],
                            (float) $entry['measurement'],
                            $heightCm
                        )
                        : null;
                } elseif ($entry['category'] === 'steps') {
                    $stepCount = is_numeric($entry['measurement'])
                        ? (float) $entry['measurement']
                        : null;
                    $entry['calories_burned'] = $stepCount !== null
                        && $stepCount >= 0
                        && floor($stepCount) === $stepCount
                        && $heightCm !== null
                        ? calculateCaloriesBurned(
                            $pdo,
                            (int) $user['user_id'],
                            (string) $entry['date'],
                            (int) $stepCount,
                            $heightCm,
                            $weightEntries
                        )
                        : null;
                }
            }
            unset($entry);
            respond(200, ['entries' => $entries]);
        }

        respond(200, ['user' => publicUser($user)]);
    }

    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== 0) {
        respond(415, ['error' => 'Send login details as JSON.']);
    }

    try {
        $credentials = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        respond(400, ['error' => 'Invalid JSON request.']);
    }

    if (!is_array($credentials)) {
        respond(400, ['error' => 'Invalid request.']);
    }

    $action = $credentials['action'] ?? 'login';
    $username = $credentials['username'] ?? null;
    $password = $credentials['password'] ?? null;

    if (!is_string($action) || !in_array($action, ['login', 'register', 'logout', 'create_goal', 'update_goal', 'delete_goal', 'mark_goal_done', 'mark_goal_not_done', 'create_entry', 'update_entry', 'delete_entry'], true)) {
        respond(400, ['error' => 'Invalid account action.']);
    }

    if ($action === 'logout') {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $cookie = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $cookie['path'],
                'domain' => $cookie['domain'],
                'secure' => $cookie['secure'],
                'httponly' => $cookie['httponly'],
                'samesite' => $cookie['samesite'],
            ]);
        }
        session_destroy();
        respond(200, ['logged_out' => true]);
    }

    if ($action === 'delete_entry') {
        if (!isset($_SESSION['user_id'])) {
            respond(401, ['error' => 'Please sign in before removing an entry.']);
        }

        $entryId = $credentials['id'] ?? null;
        if (!is_int($entryId) || $entryId < 1) {
            respond(400, ['error' => 'A valid entry ID is required.']);
        }

        $pdo = connectToDatabase();
        $statement = $pdo->prepare(
            'DELETE FROM health_markers WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $entryId,
            'user_id' => (int) $_SESSION['user_id'],
        ]);
        if ($statement->rowCount() === 0) {
            respond(404, ['error' => 'Entry not found.']);
        }

        respond(200, ['deleted' => true, 'id' => $entryId]);
    }

    if ($action === 'create_entry' || $action === 'update_entry') {
        if (!isset($_SESSION['user_id'])) {
            respond(401, ['error' => 'Please sign in before changing an entry.']);
        }

        $entryId = $credentials['id'] ?? null;
        if ($action === 'update_entry' && (!is_int($entryId) || $entryId < 1)) {
            respond(400, ['error' => 'A valid entry ID is required.']);
        }

        $category = $credentials['category'] ?? null;
        $date = $credentials['date'] ?? null;
        $categories = ['weight', 'sleep', 'steps', 'blood_pressure'];
        if (!is_string($category) || !in_array($category, $categories, true)) {
            respond(400, ['error' => 'Choose a valid health parameter.']);
        }

        $isBloodPressure = $category === 'blood_pressure';
        $measurement = $credentials['measurement'] ?? null;
        $systolic = $credentials['systolic'] ?? null;
        $diastolic = $credentials['diastolic'] ?? null;

        if ($isBloodPressure) {
            if (
                !is_int($systolic)
                || !is_int($diastolic)
                || $systolic < 50
                || $systolic > 300
                || $diastolic < 30
                || $diastolic > 200
                || $systolic <= $diastolic
            ) {
                respond(400, ['error' => 'Enter valid whole-number blood pressure values (systolic 50–300 and diastolic 30–200 mmHg; systolic must be higher).']);
            }
            $measurement = "{$systolic}/{$diastolic}";
        } elseif (
            (!is_int($measurement) && !is_float($measurement))
            || !is_finite((float) $measurement)
        ) {
            respond(400, ['error' => 'Enter a numeric measurement.']);
        } elseif ($category === 'weight' && ($measurement < 0.1 || $measurement > 500)) {
            respond(400, ['error' => 'Weight must be between 0.1 and 500 kg.']);
        } elseif ($category === 'sleep' && ($measurement < 0 || $measurement > 24)) {
            respond(400, ['error' => 'Sleep must be between 0 and 24 hours.']);
        } elseif ($category === 'steps' && (!is_int($measurement) || $measurement < 0 || $measurement > 200000)) {
            respond(400, ['error' => 'Daily steps must be a whole number between 0 and 200,000.']);
        } elseif (in_array($category, ['weight', 'sleep'], true) && abs($measurement - round($measurement, 2)) > 0.00000001) {
            respond(400, ['error' => 'Measurements can have up to 2 decimal places.']);
        }

        if (!$isBloodPressure) {
            $measurement = number_format((float) $measurement, 2, '.', '');
            $measurement = rtrim(rtrim($measurement, '0'), '.');
        }

        $entryDate = is_string($date) ? DateTimeImmutable::createFromFormat('!Y-m-d', $date) : false;
        $dateErrors = DateTimeImmutable::getLastErrors();
        if (
            $entryDate === false
            || $entryDate->format('Y-m-d') !== $date
            || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))
            || $entryDate > new DateTimeImmutable('today')
        ) {
            respond(400, ['error' => 'Choose a valid measurement date that is not in the future.']);
        }

        $pdo = connectToDatabase();
        $userId = (int) $_SESSION['user_id'];
        $savedDate = $entryDate->format('Y-m-d');
        if ($action === 'create_entry') {
            $statement = $pdo->prepare(
                'INSERT INTO health_markers (user_id, category, measurement, date)
                 VALUES (:user_id, :category, :measurement, :date)'
            );
            $statement->execute([
                'user_id' => $userId,
                'category' => $category,
                'measurement' => $measurement,
                'date' => $savedDate,
            ]);
            $entryId = (int) $pdo->lastInsertId();
            $previousCategory = null;
        } else {
            $existingEntryStatement = $pdo->prepare(
                'SELECT category FROM health_markers WHERE id = :id AND user_id = :user_id'
            );
            $existingEntryStatement->execute([
                'id' => $entryId,
                'user_id' => $userId,
            ]);
            $previousCategory = $existingEntryStatement->fetchColumn();
            if ($previousCategory === false) {
                respond(404, ['error' => 'Entry not found.']);
            }

            $statement = $pdo->prepare(
                'UPDATE health_markers
                 SET category = :category, measurement = :measurement, date = :date
                 WHERE id = :id AND user_id = :user_id'
            );
            $statement->execute([
                'category' => $category,
                'measurement' => $measurement,
                'date' => $savedDate,
                'id' => $entryId,
                'user_id' => $userId,
            ]);
        }

        foreach (array_unique(array_filter([$previousCategory, $category])) as $affectedCategory) {
            completeGoalsReachedByEntries($pdo, $userId, $affectedCategory);
        }

        $savedEntry = [
            'id' => $entryId,
            'category' => $category,
            'measurement' => $measurement,
            'date' => $savedDate,
        ];
        if ($category === 'weight') {
            $savedEntry['bmi'] = calculateBMI($pdo, $userId, (float) $measurement);
        } elseif ($category === 'steps') {
            $savedEntry['calories_burned'] = calculateCaloriesBurned(
                $pdo,
                $userId,
                $savedDate,
                (int) $measurement
            );
        }

        respond($action === 'create_entry' ? 201 : 200, ['entry' => $savedEntry]);
    }

    $goalActions = ['create_goal', 'update_goal', 'delete_goal', 'mark_goal_done', 'mark_goal_not_done'];
    if (in_array($action, $goalActions, true)) {
        if (!isset($_SESSION['user_id'])) {
            respond(401, ['error' => 'Please sign in before adding a goal.']);
        }

        $goalId = $credentials['id'] ?? null;
        if (
            in_array($action, ['update_goal', 'delete_goal', 'mark_goal_done', 'mark_goal_not_done'], true)
            && (!is_int($goalId) || $goalId < 1)
        ) {
            respond(400, ['error' => 'A valid goal ID is required.']);
        }

        if ($action === 'delete_goal') {
            $pdo = connectToDatabase();
            $statement = $pdo->prepare('DELETE FROM goals WHERE id = :id AND user_id = :user_id');
            $statement->execute([
                'id' => $goalId,
                'user_id' => (int) $_SESSION['user_id'],
            ]);

            if ($statement->rowCount() === 0) {
                respond(404, ['error' => 'Goal not found.']);
            }

            respond(200, ['deleted' => true, 'id' => $goalId]);
        }

        if ($action === 'mark_goal_done') {
            $pdo = connectToDatabase();
            $check = $pdo->prepare('SELECT id FROM goals WHERE id = :id AND user_id = :user_id');
            $check->execute([
                'id' => $goalId,
                'user_id' => (int) $_SESSION['user_id'],
            ]);
            if ($check->fetch() === false) {
                respond(404, ['error' => 'Goal not found.']);
            }

            $statement = $pdo->prepare(
                'UPDATE goals SET goal_done = 1 WHERE id = :id AND user_id = :user_id'
            );
            $statement->execute([
                'id' => $goalId,
                'user_id' => (int) $_SESSION['user_id'],
            ]);

            respond(200, ['goal' => ['id' => $goalId, 'goal_done' => true]]);
        }

        if ($action === 'mark_goal_not_done') {
            $pdo = connectToDatabase();
            $check = $pdo->prepare(
                'SELECT done_by_date FROM goals WHERE id = :id AND user_id = :user_id'
            );
            $check->execute([
                'id' => $goalId,
                'user_id' => (int) $_SESSION['user_id'],
            ]);
            $goal = $check->fetch();
            if ($goal === false) {
                respond(404, ['error' => 'Goal not found.']);
            }

            if ($goal['done_by_date'] < (new DateTimeImmutable('today'))->format('Y-m-d')) {
                respond(400, ['error' => 'A goal can only be marked unfinished before its end date.']);
            }

            $statement = $pdo->prepare(
                'UPDATE goals SET goal_done = 0 WHERE id = :id AND user_id = :user_id'
            );
            $statement->execute([
                'id' => $goalId,
                'user_id' => (int) $_SESSION['user_id'],
            ]);

            respond(200, ['goal' => ['id' => $goalId, 'goal_done' => false]]);
        }

        $category = $credentials['category'] ?? null;
        $goalName = $credentials['goal_name'] ?? null;
        $goal = $credentials['goal'] ?? null;
        $startDate = $credentials['start_date'] ?? null;
        $doneByDate = $credentials['done_by_date'] ?? null;
        $categories = ['weight', 'sleep', 'steps', 'blood_pressure'];

        if (!is_string($goalName) || trim($goalName) === '' || characterLength(trim($goalName)) > 100) {
            respond(400, ['error' => 'Enter a goal name of 100 characters or fewer.']);
        }
        $goalName = trim($goalName);

        if (!is_string($category) || !in_array($category, $categories, true)) {
            respond(400, ['error' => 'Choose a valid health parameter.']);
        }

        if (
            (!is_int($goal) && !is_float($goal))
            || !is_finite((float) $goal)
            || $goal <= 0
            || $goal > 99999999.99
        ) {
            respond(400, ['error' => 'Enter a number greater than 0 and no greater than 99,999,999.99.']);
        }

        $goal = (float) $goal;
        if (abs($goal - round($goal, 2)) > 0.00000001) {
            respond(400, ['error' => 'Goals can have up to 2 decimal places.']);
        }

        if (in_array($category, ['steps', 'blood_pressure'], true) && floor($goal) !== $goal) {
            respond(400, ['error' => 'Step and blood pressure goals must be whole numbers.']);
        }
        $goal = number_format($goal, 2, '.', '');

        $startDateValue = is_string($startDate) ? DateTimeImmutable::createFromFormat('!Y-m-d', $startDate) : false;
        $startDateErrors = DateTimeImmutable::getLastErrors();
        if (
            $startDateValue === false
            || $startDateValue->format('Y-m-d') !== $startDate
            || ($startDateErrors !== false && ($startDateErrors['warning_count'] > 0 || $startDateErrors['error_count'] > 0))
        ) {
            respond(400, ['error' => 'Choose a valid start date.']);
        }

        $doneByDateValue = is_string($doneByDate) ? DateTimeImmutable::createFromFormat('!Y-m-d', $doneByDate) : false;
        $doneByDateErrors = DateTimeImmutable::getLastErrors();
        if (
            $doneByDateValue === false
            || $doneByDateValue->format('Y-m-d') !== $doneByDate
            || ($doneByDateErrors !== false && ($doneByDateErrors['warning_count'] > 0 || $doneByDateErrors['error_count'] > 0))
        ) {
            respond(400, ['error' => 'Choose a valid end date.']);
        }

        if ($doneByDateValue < $startDateValue) {
            respond(400, ['error' => 'End date must be on or after the start date.']);
        }
    } elseif ($action === 'register') {
        $name = $credentials['name'] ?? null;
        $lastName = $credentials['last_name'] ?? null;
        $birthday = $credentials['birthday'] ?? null;
        $length = $credentials['length'] ?? null;
        $sex = $credentials['sex'] ?? null;
        $passwordConfirmation = $credentials['password_confirmation'] ?? null;

        if (
            !is_string($name)
            || trim($name) === ''
            || characterLength(trim($name)) > 50
            || !is_string($lastName)
            || trim($lastName) === ''
            || characterLength(trim($lastName)) > 50
        ) {
            respond(400, ['error' => 'Enter a first and last name, each 50 characters or fewer.']);
        }

        if (
            !is_string($birthday)
            || ($birthdayValue = DateTimeImmutable::createFromFormat('!Y-m-d', $birthday)) === false
            || $birthdayValue->format('Y-m-d') !== $birthday
            || $birthdayValue > new DateTimeImmutable('today')
        ) {
            respond(400, ['error' => 'Enter a valid birthday that is not in the future.']);
        }

        if (!is_string($length) || !preg_match('/\A[1-9]\d{0,9}\z/', $length) || (int) $length > 2147483647) {
            respond(400, ['error' => 'Length must be a positive whole number of centimeters.']);
        }

        if (!is_string($sex) || !in_array($sex, ['male', 'female', 'other'], true)) {
            respond(400, ['error' => 'Choose a valid sex option.']);
        }

        if (!is_string($username) || !preg_match('/\A[A-Za-z0-9._-]{3,50}\z/', $username)) {
            respond(400, ['error' => 'Username must be 3–50 characters using letters, numbers, dots, dashes, or underscores.']);
        }

        if (!is_string($password) || strlen($password) < 8 || strlen($password) > 72) {
            respond(400, ['error' => 'Password must be between 8 and 72 bytes.']);
        }

        if (!is_string($passwordConfirmation) || $password !== $passwordConfirmation) {
            respond(400, ['error' => 'The passwords do not match.']);
        }
    } elseif (
        !is_string($username)
        || trim($username) === ''
        || strlen($username) > 50
        || !is_string($password)
        || $password === ''
        || strlen($password) > 4096
    ) {
        respond(400, ['error' => 'Enter a valid username and password.']);
    }

    $pdo = connectToDatabase();
    if ($action === 'create_goal' || $action === 'update_goal') {
        if ($action === 'update_goal') {
            $statement = $pdo->prepare(
                'UPDATE goals
                 SET goal_name = :goal_name, category = :category, goal = :goal,
                     start_date = :start_date, done_by_date = :done_by_date
                 WHERE id = :id AND user_id = :user_id'
            );
            $statement->execute([
                'goal_name' => $goalName,
                'category' => $category,
                'goal' => $goal,
                'start_date' => $startDateValue->format('Y-m-d'),
                'done_by_date' => $doneByDateValue->format('Y-m-d'),
                'id' => $goalId,
                'user_id' => (int) $_SESSION['user_id'],
            ]);

            $check = $pdo->prepare('SELECT id FROM goals WHERE id = :id AND user_id = :user_id');
            $check->execute([
                'id' => $goalId,
                'user_id' => (int) $_SESSION['user_id'],
            ]);
            if ($check->fetch() === false) {
                respond(404, ['error' => 'Goal not found.']);
            }
        } else {
            $statement = $pdo->prepare(
                'INSERT INTO goals (user_id, goal_name, category, goal, start_date, done_by_date, goal_done)
                 VALUES (:user_id, :goal_name, :category, :goal, :start_date, :done_by_date, 0)'
            );
            $statement->execute([
                'user_id' => (int) $_SESSION['user_id'],
                'goal_name' => $goalName,
                'category' => $category,
                'goal' => $goal,
                'start_date' => $startDateValue->format('Y-m-d'),
                'done_by_date' => $doneByDateValue->format('Y-m-d'),
            ]);
            $goalId = (int) $pdo->lastInsertId();
        }

        completeGoalsReachedByEntries($pdo, (int) $_SESSION['user_id'], $category);
        $doneStatement = $pdo->prepare(
            'SELECT goal_done FROM goals WHERE id = :id AND user_id = :user_id'
        );
        $doneStatement->execute([
            'id' => $goalId,
            'user_id' => (int) $_SESSION['user_id'],
        ]);
        $goalDone = (bool) ((int) $doneStatement->fetchColumn());
        $stepsAverage = $category === 'steps'
            ? averageDailyValueForGoal(
                $pdo,
                (int) $_SESSION['user_id'],
                'steps',
                $startDateValue->format('Y-m-d'),
                $doneByDateValue->format('Y-m-d')
            )
            : null;
        $sleepAverage = $category === 'sleep'
            ? averageDailyValueForGoal(
                $pdo,
                (int) $_SESSION['user_id'],
                'sleep',
                $startDateValue->format('Y-m-d'),
                $doneByDateValue->format('Y-m-d')
            )
            : null;

        respond($action === 'create_goal' ? 201 : 200, [
            'goal' => [
                'id' => $goalId,
                'goal_name' => $goalName,
                'category' => $category,
                'goal' => (float) $goal,
                'start_date' => $startDateValue->format('Y-m-d'),
                'done_by_date' => $doneByDateValue->format('Y-m-d'),
                'goal_done' => $goalDone,
                'steps_average' => $stepsAverage,
                'sleep_average' => $sleepAverage,
            ],
        ]);
    }

    if ($action === 'register') {
        $statement = $pdo->prepare(
            'INSERT INTO users (name, last_name, birthday, length, sex, username, password_hash)
             VALUES (:name, :last_name, :birthday, :length, :sex, :username, :password_hash)'
        );
        $statement->execute([
            'name' => trim($name),
            'last_name' => trim($lastName),
            'birthday' => $birthday,
            'length' => (int) $length,
            'sex' => $sex,
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        $user = findUser($pdo, 'user_id', (int) $pdo->lastInsertId());
    } else {
        $user = findUser($pdo, 'username', trim($username));
        if ($user === null || !password_verify($password, $user['password_hash'])) {
            respond(401, ['error' => 'Invalid username or password.']);
        }
    }

    if ($user === null) {
        throw new RuntimeException('New account could not be retrieved after creation.');
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['user_id'];
    respond($action === 'register' ? 201 : 200, ['user' => publicUser($user)]);
} catch (PDOException $exception) {
    if (($exception->errorInfo[1] ?? null) === 1062) {
        respond(409, ['error' => 'That username is already taken. Please choose another.']);
    }

    error_log('Login API database error: ' . $exception->getMessage());
    respond(500, ['error' => 'The service is temporarily unavailable. Please try again later.']);
}
