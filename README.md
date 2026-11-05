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

## Health dashboard

After signing in, use **View your progress dashboard** to see all goals with
their current progress, completion status, date range, and number of entries
recorded during each goal period. Step progress uses the daily average across
the elapsed date range through today (capped at the goal end date), counting
days without an entry as zero and excluding future days. Multiple step entries
on the same date are all included in that day's total.

## Health goals

Goals have a user-defined name, health parameter, target value, start date, and
end date. The name is saved in the `goal_name` column; the date range is stored
in the `start_date` and `done_by_date` columns of the `goals` table. Goals can
be saved directly from the goal form; edits can be canceled before saving.
After creating a goal, use **Cancel** in the success message to remove it if it
was saved by mistake and return its details to the form for editing.
Goals can be marked complete manually, or automatically when matching entries
within the date range reach the target (`goal_done` is set to `1`). Weight, sleep, and
blood pressure use the sum of matching entries (blood pressure uses systolic
values); steps goals are achieved when the average daily steps over elapsed
calendar days in the goal's date range (including days with no entry as zero,
but excluding future days) meets or exceeds the target. Multiple step entries
on a date are summed.

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