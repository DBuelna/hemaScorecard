<?php
// Standalone CLI fixture builder: deliberately does not bootstrap the web app.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
$options = getopt('', ['help', 'dry-run', 'seed:', 'fighters:', 'pool-size:']);
if (isset($options['help'])) {
    echo "Usage: php scripts/seed-demo.php [--dry-run] [--seed=42] [--fighters=40] [--pool-size=5]\n";
    echo "DB_HOST, DB_USER, DB_PASSWORD, DB_NAME override includes/database.php.\n";
    exit;
}
$seed = filter_var($options['seed'] ?? 42, FILTER_VALIDATE_INT);
$fighters = filter_var($options['fighters'] ?? 40, FILTER_VALIDATE_INT);
$poolSize = filter_var($options['pool-size'] ?? 5, FILTER_VALIDATE_INT);
if ($seed === false || $fighters === false || $poolSize === false ||
    $fighters < 2 || $fighters > 1000 || $poolSize < 2 || $poolSize > 10 || $fighters % $poolSize !== 0) {
    fwrite(STDERR, "Use an integer seed, 2-1000 fighters, and a pool size of 2-10 dividing the fighter count.\n");
    exit(1);
}
define('DEPLOYMENT_UNKNOWN', 0);
require __DIR__ . '/../includes/database.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = null;
try {
    $db = new mysqli(getenv('DB_HOST') ?: DATABASE_HOST, getenv('DB_USER') ?: DATABASE_USER,
        getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : DATABASE_PASSWORD,
        getenv('DB_NAME') ?: PRIMARY_DATABASE);
    $db->set_charset('utf8mb4');
    // All values go through prepared statements; identifiers below are fixed code.
    $query = function ($sql, $values = []) use ($db) {
        $stmt = $db->prepare($sql);
        if ($values) {
            $types = str_repeat('s', count($values));
            $stmt->bind_param($types, ...$values);
        }
        $stmt->execute();
        return $stmt;
    };
    $insert = function ($table, $values) use ($query, $db) {
        $columns = implode(',', array_keys($values));
        $marks = implode(',', array_fill(0, count($values), '?'));
        $stmt = $query("INSERT INTO $table ($columns) VALUES ($marks)", array_values($values));
        $id = $db->insert_id;
        $stmt->close();
        return $id;
    };
    $lookup = function ($sql, $values = []) use ($query) {
        $row = $query($sql, $values)->get_result()->fetch_row();
        if (!$row) {
            throw new RuntimeException('Missing required reference data: ' . $sql);
        }
        return $row[0];
    };
    $eventName = "Demo HEMA Event (seed $seed)";
    $db->begin_transaction();
    // Serialize identical runs without creating schema or deleting existing data.
    $lockName = 'hema-demo-' . $seed;
    if ((int)$lookup('SELECT GET_LOCK(?, 10)', [$lockName]) !== 1) {
        throw new RuntimeException('Could not acquire demo seed lock.');
    }
    $existing = $query('SELECT eventID FROM systemEvents WHERE eventName = ?', [$eventName])->get_result()->fetch_row();
    if ($existing) {
        $db->rollback();
        echo "Demo already exists: event {$existing[0]}. No changes made. Use another --seed for a separate demo.\n";
        exit;
    }
    $ranking = $lookup('SELECT tournamentRankingID FROM systemRankings WHERE name = ? AND formatID = 2', ['Franklin 2014']);
    $format = $lookup('SELECT formatID FROM systemFormats WHERE formatID = 2');
    $double = $lookup('SELECT doubleTypeID FROM systemDoubleTypes WHERE doubleTypeID = 2');
    $colors = [$lookup('SELECT colorID FROM systemColors WHERE colorID = 1'), $lookup('SELECT colorID FROM systemColors WHERE colorID = 2')];
    $order = $query('SELECT matchNumber, fighter1, fighter2 FROM systemMatchOrder WHERE numberOfFighters = ? ORDER BY matchNumber', [$poolSize])->get_result()->fetch_all(MYSQLI_ASSOC);
    if (count($order) !== $poolSize * ($poolSize - 1) / 2) {
        throw new RuntimeException('Reference match order is incomplete for this pool size.');
    }
    $today = date('Y-m-d');
    $event = $insert('systemEvents', ['eventName' => $eventName, 'eventAbbreviation' => 'DEMO',
        'eventYear' => date('Y'), 'eventStartDate' => $today, 'eventEndDate' => $today,
        'countryIso2' => 'US', 'eventCity' => 'Demo City', 'eventStatus' => 'active']);
    $password = password_hash('demo-local', PASSWORD_DEFAULT);
    $insert('eventSettings', ['eventID' => $event, 'staffPassword' => $password,
        'organizerPassword' => $password, 'termsOfUseAccepted' => 1]);
    $insert('eventDefaults', ['eventID' => $event]);
    $insert('eventPublication', ['eventID' => $event, 'publishRoster' => 1, 'publishMatches' => 1]);
    $schools = [];
    for ($i = 1; $i <= 6; $i++) {
        $schools[] = $insert('systemSchools', ['schoolFullName' => "Demo $seed School $i",
            'schoolShortName' => "Demo School $i", 'countryIso2' => 'US']);
    }
    mt_srand($seed);
    $firstNames = ['Alex', 'Sam', 'Morgan', 'Jordan', 'Taylor', 'Casey', 'Riley', 'Jamie'];
    $roster = [];
    for ($i = 1; $i <= $fighters; $i++) {
        $school = $schools[($i - 1) % count($schools)];
        $person = $insert('systemRoster', ['firstName' => $firstNames[mt_rand(0, count($firstNames) - 1)],
            'lastName' => sprintf('Demo%sFighter%03d', $seed, $i), 'schoolID' => $school,
            'eMail' => "demo-$seed-$i@example.invalid"]);
        $roster[] = $insert('eventRoster', ['systemRosterID' => $person, 'eventID' => $event,
            'schoolID' => $school, 'eventCheckIn' => $i % 4 === 0 ? 0 : 1, 'eventWaiver' => 1]);
    }
    $matchCount = 0;
    foreach (['Longsword', 'Saber'] as $weaponName) {
        $weapon = $lookup('SELECT tournamentTypeID FROM systemTournaments WHERE tournamentTypeMeta = ? AND tournamentType = ?', ['weapon', $weaponName]);
        $tournament = $insert('eventTournaments', ['eventID' => $event, 'tournamentWeaponID' => $weapon,
            'tournamentRankingID' => $ranking, 'formatID' => $format, 'doubleTypeID' => $double,
            'color1ID' => $colors[0], 'color2ID' => $colors[1], 'numParticipants' => $fighters,
            'maxPoolSize' => $poolSize, 'maximumPoints' => 10, 'timeLimit' => 120, 'timerCountdown' => 1]);
        // Generic scoring buttons, with no special target/type prerequisites.
        foreach ([1, 2, 3] as $points) {
            $insert('eventAttacks', ['tournamentID' => $tournament, 'attackPoints' => $points, 'attackNumber' => $points]);
        }
        $entries = [];
        foreach ($roster as $id) {
            $entries[$id] = $insert('eventTournamentRoster', ['tournamentID' => $tournament, 'rosterID' => $id]);
        }
        $participants = $roster;
        shuffle($participants);
        foreach (array_chunk($participants, $poolSize) as $poolIndex => $pool) {
            $group = $insert('eventGroups', ['tournamentID' => $tournament, 'groupType' => 'pool',
                'groupNumber' => $poolIndex + 1, 'groupName' => 'Pool ' . ($poolIndex + 1),
                'groupSet' => 1, 'numFighters' => $poolSize]);
            foreach ($pool as $position => $id) {
                $insert('eventGroupRoster', ['groupID' => $group, 'rosterID' => $id,
                    'poolPosition' => $position + 1, 'tournamentTableID' => $entries[$id]]);
            }
            foreach ($order as $match) {
                $insert('eventMatches', ['groupID' => $group, 'matchNumber' => $match['matchNumber'],
                    'fighter1ID' => $pool[$match['fighter1'] - 1], 'fighter2ID' => $pool[$match['fighter2'] - 1]]);
                $matchCount++;
            }
        }
    }
    if (isset($options['dry-run'])) {
        $db->rollback();
        echo "Dry run passed; all inserted rows rolled back (auto-increment IDs may advance).\n";
    } else {
        $db->commit();
        echo "Created $eventName: eventID=$event\n";
        echo "Event staff and organizer password: demo-local\n";
    }
    echo "6 schools, $fighters fighters, 2 tournaments, " . (2 * $fighters / $poolSize) . " pools, $matchCount unscored matches.\n";
} catch (Throwable $error) {
    if ($db) {
        $db->rollback();
    }
    fwrite(STDERR, "Seed failed: " . $error->getMessage() . "\n");
    exit(1);
} finally {
    if ($db) {
        $db->close();
    }
}
