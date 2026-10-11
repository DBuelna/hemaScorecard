# hemaScorecard
HEMA Scorecard - Tournament Management Software

Software to run and manage tournaments.

Developed by Sean Franklin
A HEMA Alliance supported project

## Running in docker
Install [docker](https://docs.docker.com/install/) and [docker compose] (https://docs.docker.com/compose/install/).
After that simply issue docker-compose up in the source root directory. The application will be avaliable on http://localhost:8000
PHP docker image contains xdebug, and project contains sample VS Code config which allows user to debug application via "Listen for XDebug" command.

## Data persistence
MySQL data is persisted in the Docker-managed `mysql_data` volume. This avoids permission errors on Windows/WSL bind mounts. Existing files in `./data` are not migrated automatically or deleted. Avoid `docker compose down -v` if you want to keep the database.

### Troubleshooting

## Demo data

Create a published demo event with six schools, 40 fighters entered in both
Longsword and Saber, pools of five, and 160 scheduled, unscored matches:

```bash
docker compose exec web php /hemaScorecard/scripts/seed-demo.php --dry-run
docker compose exec web php /hemaScorecard/scripts/seed-demo.php
```

If your containers use the `hema_scorecard` project name, add
`-p hema_scorecard` after `docker compose`.

Select the demo event at `http://localhost:8000`. The event staff and organizer
password is `demo-local`; the script does not change the system admin account.
Roster and matches are published. Scores, exchanges, standings, brackets, and
logistics are left for you to populate through the application.

The same seed skips an existing demo without modifying it. Create a separate
dataset with `--seed=123 --fighters=100 --pool-size=5`. Fighter count must be
2–1000 and divisible by pool size (2–10). The seed reproduces names and pool
assignments; database IDs, password hashes, and event dates can differ.

The CLI script uses `includes/database.php` without loading the web bootstrap.
Override connection settings with `DB_HOST`, `DB_USER`, `DB_PASSWORD`, and
`DB_NAME`. Inserts run in one transaction and errors roll back the dataset.
`--dry-run` also rolls back, although MySQL auto-increment counters can advance.


If there is a need to get inside running container simply issue 

```bash
$ docker-compose exec db /bin/bash
``` 

to get into mysql container, and 

```bash
$ docker-compose exec web /bin/bash
```

to get to the web container, respectively.
