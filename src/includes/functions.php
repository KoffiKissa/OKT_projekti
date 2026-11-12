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
        "SELECT user_id, name, last_name, username, password_hash, length FROM users WHERE {$column} = :value LIMIT 1"
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

function averageDailyValueForGoal(
    PDO $pdo,
    int $userId,
    string $category,
    string $startDate,
    string $doneByDate
): float
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
        'category' => $category,
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
    $isDailyAverageCategory = in_array($category, ['steps', 'sleep'], true);
    if ($isDailyAverageCategory) {
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
        if ($isDailyAverageCategory) {
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
        } elseif ($isDailyAverageCategory) {
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

function nearestWeightForDate(array $weightEntries, string $entryDate): ?float
{
    $targetDate = new DateTimeImmutable($entryDate);
    $nearestWeight = null;
    $nearestDistance = PHP_INT_MAX;
    $nearestIsBefore = false;
    $nearestId = -1;

    foreach ($weightEntries as $entry) {
        if (!isset($entry['measurement'], $entry['date']) || !is_numeric($entry['measurement'])) {
            continue;
        }
        $weight = (float) $entry['measurement'];
        if ($weight <= 0 || !is_finite($weight)) {
            continue;
        }

        $weightDate = new DateTimeImmutable((string) $entry['date']);
        $distance = $weightDate->diff($targetDate)->days;
        $isBefore = $weightDate <= $targetDate;
        $id = (int) ($entry['id'] ?? 0);
        if (
            $distance < $nearestDistance
            || ($distance === $nearestDistance && $isBefore && !$nearestIsBefore)
            || ($distance === $nearestDistance && $isBefore === $nearestIsBefore && $id > $nearestId)
        ) {
            $nearestWeight = $weight;
            $nearestDistance = $distance;
            $nearestIsBefore = $isBefore;
            $nearestId = $id;
        }
    }

    return $nearestWeight;
}

function calculateCaloriesBurned(
    PDO $pdo,
    int $userId,
    string $entryDate,
    int $steps,
    ?float $heightCm = null,
    ?array $weightEntries = null
): ?int {
    if ($heightCm === null) {
        $heightStatement = $pdo->prepare('SELECT length FROM users WHERE user_id = :user_id');
        $heightStatement->execute(['user_id' => $userId]);
        $savedHeight = $heightStatement->fetchColumn();
        if (!is_numeric($savedHeight)) {
            return null;
        }
        $heightCm = (float) $savedHeight;
    }

    if ($heightCm <= 0 || !is_finite($heightCm) || $steps < 0) {
        return null;
    }

    if ($weightEntries === null) {
        $weightStatement = $pdo->prepare(
            'SELECT id, measurement, date
             FROM health_markers
             WHERE user_id = :user_id AND category = :category
             ORDER BY date DESC, id DESC'
        );
        $weightStatement->execute([
            'user_id' => $userId,
            'category' => 'weight',
        ]);
        $weightEntries = $weightStatement->fetchAll();
    }

    $weightKg = nearestWeightForDate($weightEntries, $entryDate);
    if ($weightKg === null) {
        if ($weightEntries !== []) {
            return null;
        }
        $heightMeters = $heightCm / 100;
        $weightKg = 22 * ($heightMeters ** 2);
    }

    if ($weightKg <= 0 || !is_finite($weightKg)) {
        return null;
    }

    $heightMeters = $heightCm / 100;
    $distanceKm = ($steps * 0.415 * $heightMeters) / 1000;

    return (int) round(0.5 * $weightKg * $distanceKm);
}

function calculateBMI(PDO $pdo, int $userId, ?float $weightKg = null, ?float $heightCm = null): ?float
{
    if ($weightKg === null) {
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
        if ($measurement === false || !is_numeric($measurement['measurement'])) {
            return null;
        }

        $weightKg = (float) $measurement['measurement'];
        if ($heightCm === null) {
            if (!is_numeric($measurement['length'])) {
                return null;
            }
            $heightCm = (float) $measurement['length'];
        }
    } elseif ($heightCm === null) {
        $statement = $pdo->prepare('SELECT length FROM users WHERE user_id = :user_id');
        $statement->execute(['user_id' => $userId]);
        $savedHeight = $statement->fetchColumn();
        if (!is_numeric($savedHeight)) {
            return null;
        }
        $heightCm = (float) $savedHeight;
    }

    if ($weightKg <= 0 || $heightCm <= 0 || !is_finite($weightKg) || !is_finite($heightCm)) {
        return null;
    }

    $heightMeters = $heightCm / 100;

    return round($weightKg / ($heightMeters ** 2), 1);
}