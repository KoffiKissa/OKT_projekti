# OKT_projekti
Repo ohjelmstokehittäjänätoimimisen kurssin projekti työlle

Projektissa käytetään seuraavia teknologioita: PHP, HTML, JavaScript, Docker, MySQL

The UI uses Tailwind CSS via its browser CDN and needs an internet connection to load styles.

## Health entries

After signing in, use **Add a new health entry** to enter a measurement and
save it. Weight, sleep, and steps are validated against their supported
numeric ranges; blood pressure is entered as systolic and diastolic values.
Saved measurements are stored in the signed-in user's `health_markers` records
in `health_db`, with an option to undo the latest save. Use **Edit entry**
beside a recent measurement to correct its parameter, value, or date; edits can
also be undone immediately after saving.

Step entries show estimated calories burned. The estimate uses the closest
dated weight entry and a step length of 0.415 times the user's height, with an
energy estimate of 0.5 kcal per kilogram per kilometer. If the user has no
weight entries, the estimate assumes BMI 22 to derive weight from height.

## Health dashboard

After signing in, use **View your progress dashboard** to see all goals with
their current progress, completion status, date range, and number of entries
recorded during each goal period. The dashboard also shows current BMI based
on the user's height and latest recorded weight. Step progress uses the daily
average across the elapsed date range through today (capped at the goal end
date), counting days without an entry as zero and excluding future days. Sleep
goals use the same elapsed-day average calculation. Multiple step or sleep
entries on the same date are included in that day's total.
All goal cards show a date-based progress bar from the start date to the end
date. Step goal cards also show the days until the goal ends and the daily step
average needed over the remaining goal dates to reach the target average. Step
and sleep cards include daily trend charts with their target averages.

## Health goals

Goals have a user-defined name, health parameter, target value, start date, and
end date. The name is saved in the `goal_name` column; the date range is stored
in the `start_date` and `done_by_date` columns of the `goals` table. Goals can
be saved directly from the goal form; edits can be canceled before saving.
After creating a goal, use **Cancel** in the success message to remove it if it
was saved by mistake and return its details to the form for editing.
Goals can be marked complete manually, or automatically when matching entries
within the date range reach the target (`goal_done` is set to `1`). Weight goals
compare individual readings (a reading at or below the target reaches the goal);
the dashboard shows the latest weight reading in the period. Sleep goals are
achieved when average sleep per day over elapsed calendar days in the goal's
date range (including days with no entry as zero, but excluding future days)
meets or exceeds the target. Steps use the same elapsed-day average calculation;
multiple step entries on a date are summed. Blood pressure goals use the sum of
matching entries (blood pressure uses systolic values).

For an existing database that still has the old `date` column, migrate it and
add `start_date` before using the updated goal form. If that database does not
already have `goal_name`, add it first:

```sql
ALTER TABLE goals ADD COLUMN goal_name VARCHAR(100) NOT NULL DEFAULT '';
ALTER TABLE goals
    CHANGE COLUMN date done_by_date DATE NOT NULL,
    ADD COLUMN start_date DATE NULL;
UPDATE goals SET start_date = done_by_date WHERE start_date IS NULL;
ALTER TABLE goals MODIFY COLUMN start_date DATE NOT NULL;
```

If `goal_done` is not already present, add it with:

```sql
ALTER TABLE goals ADD COLUMN goal_done TINYINT(1) NOT NULL DEFAULT 0;
```

If the existing column allows `NULL`, normalize its values and default:

```sql
UPDATE goals SET goal_done = 0 WHERE goal_done IS NULL;
ALTER TABLE goals MODIFY COLUMN goal_done TINYINT(1) NOT NULL DEFAULT 0;
```

## Local AI chat

The dashboard chat sends signed-in users' messages to the local Ollama service
through the PHP API; the browser does not connect to Ollama directly. Docker
Compose defaults to `http://host.docker.internal:11434` and the
`qwen3.5:4b` model. Set `OLLAMA_BASE_URL` or `OLLAMA_MODEL` in your local
`.env` file to override these values.

Ollama must be running on the same host as Docker and reachable from the PHP
container. On Windows, Ollama may need to listen on an interface accessible to
Docker rather than only `127.0.0.1`; if changing its bind address, restrict
access with Windows Firewall and do not expose the Ollama port to public
networks. Each account has one persistent chat thread. Every message in that
thread shares its `chat_id`; `message_id` identifies an individual message.
The user's sender is their `user_id`, and the assistant's sender is the
configured Ollama model (default `qwen3.5:4b`). The `date` column is a
`DATETIME` populated when each message is saved. Existing date-only values
become midnight during migration because their original times were not stored.

For an existing database, run this migration once. It preserves messages whose
numeric user senders can be matched to the `users` table and associates bot
replies with the nearest preceding user message. It stops at the `NOT NULL`
change if any legacy messages cannot be safely assigned to an account; resolve
those rows before continuing.

```sql
CREATE TABLE chats (
    chat_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (chat_id),
    UNIQUE KEY user_id (user_id),
    CONSTRAINT fk_chat_user FOREIGN KEY (user_id) REFERENCES users (user_id)
);

INSERT INTO chats (user_id)
SELECT DISTINCT users.user_id
FROM users
JOIN messages ON messages.sender = CAST(users.user_id AS CHAR);

ALTER TABLE messages MODIFY COLUMN chat_id INT NOT NULL;
ALTER TABLE messages CHANGE COLUMN chat_id message_id INT NOT NULL;
ALTER TABLE messages MODIFY COLUMN message_id INT NOT NULL AUTO_INCREMENT;
ALTER TABLE messages
    ADD COLUMN chat_id INT NULL AFTER message_id,
    MODIFY COLUMN message VARCHAR(1000) NOT NULL,
    MODIFY COLUMN sender VARCHAR(100) NOT NULL,
    MODIFY COLUMN `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

CREATE TEMPORARY TABLE legacy_message_owners (
    message_id INT NOT NULL PRIMARY KEY,
    user_id INT NULL
);

INSERT INTO legacy_message_owners (message_id, user_id)
SELECT messages.message_id,
       (
           SELECT users.user_id
           FROM messages AS preceding
           JOIN users ON preceding.sender = CAST(users.user_id AS CHAR)
           WHERE preceding.message_id <= messages.message_id
           ORDER BY preceding.message_id DESC
           LIMIT 1
       )
FROM messages;

UPDATE messages
JOIN legacy_message_owners ON legacy_message_owners.message_id = messages.message_id
JOIN chats ON chats.user_id = legacy_message_owners.user_id
SET messages.chat_id = chats.chat_id;

ALTER TABLE messages
    MODIFY COLUMN chat_id INT NOT NULL,
    ADD KEY fk_messages_chat (chat_id),
    ADD CONSTRAINT fk_messages_chat FOREIGN KEY (chat_id) REFERENCES chats (chat_id);

DROP TEMPORARY TABLE legacy_message_owners;
```