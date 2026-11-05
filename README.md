# OKT_projekti
Repo ohjelmstokehittäjänätoimimisen kurssin projekti työlle

Projektissa käytetään seuraavia teknologioita: PHP, HTML, JavaScript, Docker, MySQL

The UI uses Tailwind CSS via its browser CDN and needs an internet connection to load styles.

## Health entries

After signing in, use **Add a new health entry** to record and review a
measurement before saving it. Weight, sleep, and steps are validated against
their supported numeric ranges; blood pressure is entered as systolic and
diastolic values. Confirmed measurements are saved to the signed-in user's
`health_markers` records in `health_db`.

## Health dashboard

After signing in, use **View your progress dashboard** to see all goals with
their current progress, completion status, date range, and number of entries
recorded during each goal period. Step progress uses the daily average across
the full date range, counting days without an entry as zero.

## Health goals

Goals have a user-defined name, health parameter, target value, start date, and
end date. The name is saved in the `goal_name` column; the date range is stored
in the `start_date` and `done_by_date` columns of the `goals` table. Goals can
be marked complete manually, or automatically when matching entries within the
date range reach the target (`goal_done` is set to `1`). Weight, sleep, and
blood pressure use the sum of matching entries (blood pressure uses systolic
values); steps goals are achieved when the average daily steps over every
calendar day in the goal's date range (including days with no entry as zero)
meets or exceeds the target.

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