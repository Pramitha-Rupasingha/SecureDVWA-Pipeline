# SecureDVWA-Pipeline

A DevSecOps pipeline built around DVWA (Damn Vulnerable Web Application) for IE3142 DevOps Security (SLIIT, Year 3 Semester 1). The project demonstrates exploit-and-fix secure coding, STRIDE threat modelling, and a GitHub Actions CI/CD pipeline with automated security gates.

> **Warning:** DVWA is intentionally vulnerable. Run it on your own machine only (`localhost`). Never expose it to the internet.

## Team and roles

| Member | Responsibility |
|---|---|
| Pramitha | DevSecOps / CI-CD (GitHub Actions, security gates, secrets, PR merging) |
| Isuru | Docker / architecture (Dockerfile, Docker Compose, architecture diagram) |
| Asirindu | Vulnerability remediation (SQLi, XSS, command injection, CSRF fixes and evidence) |
| Venura | Threat modelling / documentation (STRIDE, README, report sections) |

## Repository structure

```
.github/workflows/devsecops-pipeline.yml   CI/CD pipeline
dvwa/Dockerfile                            Builds our image with the remediated files baked in
dvwa/docker-compose.yml                    Runs the application
dvwa/vulnerabilities/*/source/low.php      Remediated source files (sqli, xss_r, exec, csrf)
docs/threat-model.md                       STRIDE threat model and risk assessment
docs/architecture-diagram.jpg              Architecture and trust boundaries
```

## Architecture note

The `dvwa` container bundles its own MariaDB database process (listening on `127.0.0.1:3306` inside the container). The `dvwa-db` (`mysql:5.7`) service in `docker-compose.yml` is **not** currently used by the application — DVWA connects to its bundled database, not to that container. This is documented in detail in [`docs/threat-model.md`](docs/threat-model.md) and shown in [`docs/architecture-diagram.jpg`](docs/architecture-diagram.jpg).

## Prerequisites

- Docker Desktop (or Docker Engine with the Compose plugin)
- Git

## Setup and run

1. Clone the repository:
   ```bash
   git clone https://github.com/Pramitha-Rupasingha/SecureDVWA-Pipeline.git
   cd SecureDVWA-Pipeline/dvwa
   ```

2. Create a `.env` file inside the `dvwa` folder. Choose your own values; this file is listed in `.gitignore` and must never be committed.
   ```
   MYSQL_DATABASE=<database name>
   MYSQL_USER=<database user>
   MYSQL_PASSWORD=<choose a password>
   MYSQL_ROOT_PASSWORD=<choose a root password>
   ```
   (These values are only consumed by the `dvwa-db` service, which the application does not currently use — see the architecture note above.)

3. Build and start the application with one command:
   ```bash
   docker compose up -d --build
   ```

4. Open `http://localhost` in your browser. On first run, follow the DVWA setup page to create/reset the database, then log in with the default DVWA credentials from the upstream DVWA documentation.

5. Stop the application:
   ```bash
   docker compose down
   ```

## CI/CD pipeline

The workflow in `.github/workflows/devsecops-pipeline.yml` runs on every push and pull request to `main`.

| Job | Step | Tool |
|---|---|---|
| Build & Test | Build the stack, start it, smoke-test `login.php` | Docker Compose, curl |
| Security Gates | Secrets scanning | Gitleaks |
| Security Gates | SAST | Semgrep |
| Security Gates | Dependency scanning | Trivy (filesystem) |
| Security Gates | Container image scanning | Trivy (our built `dvwa-local:latest` image) |

The Trivy gates use `exit-code: 1` on CRITICAL/HIGH findings, so the pipeline genuinely fails when they are found. The container image scan currently fails because the upstream base image (Debian 9.5) is end of life with known CVEs; this failing run is kept as evidence that the gate blocks a build.

### Secrets

No credentials are stored in the repository. The pipeline reads database settings from GitHub Actions encrypted secrets, which must exist in the repository settings: `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_ROOT_PASSWORD`. Locally, the same values come from your untracked `.env` file.

## Documentation

- Threat model and risk assessment: [`docs/threat-model.md`](docs/threat-model.md)
- Architecture diagram: [`docs/architecture-diagram.jpg`](docs/architecture-diagram.jpg)

