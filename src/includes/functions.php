<?php

function respond(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function connectToDatabase(): PDO
{
    $host = getenv('DB_HOST') ?: 'db';
    $database = getenv('DB_NAME') ?: 'health_db';
    $username = getenv('DB_USER') ?: 'user';
    $password = getenv('DB_PASSWORD') ?: 'pass';

    return new PDO(
        "mysql:host={$host};dbname={$database};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
}

function findUser(PDO $pdo, string $column, string|int $value): ?array
{
    $statement = $pdo->prepare(
        "SELECT user_id, name, last_name, username, password_hash FROM users WHERE {$column} = :value LIMIT 1"
    );
    $statement->execute(['value' => $value]);
    $user = $statement->fetch();

    return $user === false ? null : $user;
}

function publicUser(array $user): array
{
    return [
        'user_id' => (int) $user['user_id'],
        'name' => $user['name'],
        'last_name' => $user['last_name'],
        'username' => $user['username'],
    ];
}

function characterLength(string $value): int
{
    $count = preg_match_all('/./us', $value, $matches);

    return $count === false ? PHP_INT_MAX : $count;
}

function goalEntryValue(string $category, string $measurement): ?float
{
    if ($category === 'blood_pressure') {
        if (preg_match('/\A(\d+)\/\d+\z/', $measurement, $matches) !== 1) {
            return null;
        }

        return (float) $matches[1];
    }

    if (!is_numeric($measurement) || !is_finite((float) $measurement)) {
        return null;
    }

    return (float) $measurement;
}

function elapsedGoalDays(string $startDate, string $doneByDate): int
{
    $start = new DateTimeImmutable($startDate);
    $today = new DateTimeImmutable('today');
    if ($start > $today) {
        return 0;
    }

    $end = new DateTimeImmutable($doneByDate);
    if ($end > $today) {
        $end = $today;
    }

    return $start->diff($end)->days + 1;
}

function averageStepsForGoal(PDO $pdo, int $userId, string $startDate, string $doneByDate): float
{
    $throughDate = min($doneByDate, (new DateTimeImmutable('today'))->format('Y-m-d'));
    $days = elapsedGoalDays($startDate, $doneByDate);
    if ($days === 0) {
        return 0.0;
    }

    $statement = $pdo->prepare(
        'SELECT COALESCE(SUM(CAST(measurement AS DECIMAL(10,2))), 0)
         FROM health_markers
         WHERE user_id = :user_id AND category = :category
           AND date >= :start_date AND date <= :through_date'
    );
    $statement->execute([
        'user_id' => $userId,
        'category' => 'steps',
        'start_date' => $startDate,
        'through_date' => $throughDate,
    ]);
    $total = (float) $statement->fetchColumn();

    return $total / $days;
}

function completeGoalsReachedByEntries(PDO $pdo, int $userId, string $category): void
{
    $goalsStatement = $pdo->prepare(
        'SELECT id, goal, start_date, done_by_date
         FROM goals
         WHERE user_id = :user_id AND category = :category
           AND (goal_done = 0 OR goal_done IS NULL)'
    );
    $goalsStatement->execute([
        'user_id' => $userId,
        'category' => $category,
    ]);

    $entriesQuery = 'SELECT measurement
                     FROM health_markers
                     WHERE user_id = :user_id AND category = :category
                       AND date >= :start_date AND date <= :done_by_date';
    if ($category === 'steps') {
        $entriesQuery .= ' AND date <= :through_date';
    }
    $entriesStatement = $pdo->prepare($entriesQuery);
    $completeStatement = $pdo->prepare(
        'UPDATE goals SET goal_done = 1
         WHERE id = :id AND user_id = :user_id
           AND (goal_done = 0 OR goal_done IS NULL)'
    );

    foreach ($goalsStatement->fetchAll() as $goal) {
        $entryParameters = [
            'user_id' => $userId,
            'category' => $category,
            'start_date' => $goal['start_date'],
            'done_by_date' => $goal['done_by_date'],
        ];
        if ($category === 'steps') {
            $entryParameters['through_date'] = min(
                (string) $goal['done_by_date'],
                (new DateTimeImmutable('today'))->format('Y-m-d')
            );
        }
        $entriesStatement->execute($entryParameters);

        $total = 0.0;
        $weightGoalReached = false;
        foreach ($entriesStatement->fetchAll() as $entry) {
            $value = goalEntryValue($category, (string) $entry['measurement']);
            if ($value === null) {
                continue;
            }

            if ($category === 'weight') {
                $weightGoalReached = $weightGoalReached || $value <= (float) $goal['goal'];
            } else {
                $total += $value;
            }
        }

        if ($category === 'weight') {
            $reached = $weightGoalReached;
        } elseif ($category === 'steps') {
            $periodDays = elapsedGoalDays((string) $goal['start_date'], (string) $goal['done_by_date']);
            $reached = $periodDays > 0 && ($total / $periodDays) >= (float) $goal['goal'];
        } else {
            $reached = $total >= (float) $goal['goal'];
        }
        if ($reached) {
            $completeStatement->execute([
                'id' => (int) $goal['id'],
                'user_id' => $userId,
            ]);
        }
    }
}

function calculateBMI(PDO $pdo, int $userId): ?float
{
    $statement = $pdo->prepare(
        'SELECT users.length, health_markers.measurement
         FROM users
         LEFT JOIN health_markers
           ON health_markers.user_id = users.user_id
          AND health_markers.category = :category
         WHERE users.user_id = :user_id
         ORDER BY health_markers.id DESC
         LIMIT 1'
    );
    $statement->execute([
        'category' => 'weight',
        'user_id' => $userId,
    ]);
    $measurement = $statement->fetch();

    if (
        $measurement === false
        || $measurement['length'] === null
        || $measurement['measurement'] === null
        || !is_numeric($measurement['length'])
        || !is_numeric($measurement['measurement'])
    ) {
        return null;
    }

    $heightMeters = (float) $measurement['length'] / 100;
    $weightKg = (float) $measurement['measurement'];
    if ($heightMeters <= 0 || $weightKg <= 0 || !is_finite($heightMeters) || !is_finite($weightKg)) {
        return null;
    }

    return round($weightKg / ($heightMeters ** 2), 1);
}