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