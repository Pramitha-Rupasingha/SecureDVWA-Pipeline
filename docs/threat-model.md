# STRIDE Threat Model and Risk Assessment

Project: SecureDVWA-Pipeline (IE3142 DevOps Security)
Scope: the architecture in `docs/architecture-diagram.jpg` (local Docker deployment of DVWA + MySQL, and the GitHub Actions CI/CD pipeline).

## 1. Trust boundaries (from the architecture diagram)

| Boundary | Contents | Notes |
|---|---|---|
| A. Local Developer Machine | Browser, Docker Engine | Accessed via `http://localhost` only; not internet-facing |
| B. Docker Internal Network | DVWA container, MySQL container | MySQL has no published host port; only DVWA can reach it on 3306 |
| C. GitHub Cloud (CI/CD) | Repository, GitHub Actions runner | Runs in an isolated runner; no access to local containers or DB |

## 2. Rating scale

Likelihood and Impact are each rated on a 3-level scale: Low (1), Medium (2), High (3).
Risk score = Likelihood x Impact. 1-2 = Low, 3-4 = Medium, 6-9 = High.

## 3. Threat register

| ID | Threat | STRIDE | Component / boundary crossing | Likelihood | Impact | Risk |
|---|---|---|---|---|---|---|
| T1 | SQL injection through the SQLi form returns unauthorised user records | Tampering, Information Disclosure | DVWA -> MySQL (Boundary B) | High (3) | High (3) | 9 High |
| T2 | Reflected XSS payload executes script in the victim's browser | Tampering, Spoofing | Browser -> DVWA (A to B) | High (3) | Medium (2) | 6 High |
| T3 | OS command injection through the exec form runs arbitrary commands in the DVWA container | Elevation of Privilege | Browser -> DVWA (A to B) | Medium (2) | High (3) | 6 High |
| T4 | CSRF forces a logged-in user to change their password without consent | Spoofing, Tampering | Browser -> DVWA (A to B) | Medium (2) | Medium (2) | 4 Medium |
| T5 | Database credentials leak through the repository, workflow file or CI logs | Information Disclosure | Developer -> GitHub (A to C) | Medium (2) | High (3) | 6 High |
| T6 | Vulnerable dependency or base image reaches the built container image | Tampering (supply chain) | GitHub Actions build (C) | High (3) | Medium (2) | 6 High |

## 4. Justification and control mapping

**T1 SQL injection.**
Likelihood is High because the original SQLi page concatenated user input directly into the query and the payload is trivial and widely documented. Impact is High because a successful payload returns rows from the users table.
Control: parameterised query in `dvwa/vulnerabilities/sqli/source/low.php` (Asirindu's fix). Verified by re-running the same exploit against the fixed code. Semgrep SAST gate in `.github/workflows/devsecops-pipeline.yml` provides ongoing detection.

**T2 Reflected XSS.**
Likelihood is High because the reflected input was echoed without encoding and needs only a crafted link. Impact is Medium because it affects the victim's session in their browser rather than the server or database.
Control: output encoding in `dvwa/vulnerabilities/xss_r/source/low.php`. Verified by re-running the same payload after the fix.

**T3 Command injection.**
Likelihood is Medium because exploitation requires reaching the exec page, but the input was passed to a shell unfiltered. Impact is High because arbitrary command execution in the container could expose files and environment variables.
Control: input validation in `dvwa/vulnerabilities/exec/source/low.php`. The container also runs with no host access beyond the published port 80, limiting the blast radius (`dvwa/docker-compose.yml`).

**T4 CSRF.**
Likelihood is Medium because the attack needs a logged-in victim to open an attacker-controlled page. Impact is Medium because the outcome is an unwanted account change, not full compromise.
Control: request verification added in `dvwa/vulnerabilities/csrf/source/low.php`. Verified by re-running the forged request after the fix.

**T5 Secrets leakage.**
Likelihood is Medium because credentials are needed by the app and the pipeline, so there is a real chance of committing them by mistake. Impact is High because the credentials give full access to the database.
Controls: `.env` is listed in `.gitignore` and has never been committed (checked with `git log --all --full-history -- dvwa/.env`); CI uses GitHub encrypted secrets (`secrets.MYSQL_*`) in `.github/workflows/devsecops-pipeline.yml`; the Gitleaks gate scans the repository history on every push.

**T6 Vulnerable dependency or base image.**
Likelihood is High because DVWA builds on the upstream `vulnerables/web-dvwa` image (Debian 9, end of life), which contains known CVEs. Impact is Medium because the application runs locally in a lab setting rather than exposed to the internet.
Controls: Trivy filesystem scan and Trivy image scan of our own built image (`dvwa-local:latest`) in the pipeline, configured with `exit-code: 1` on CRITICAL/HIGH findings so the build fails. The failing run is the evidence for this gate.

## 5. Residual risk and limitations

- DVWA is intentionally vulnerable; only the four exploit-and-fix vulnerabilities above are remediated. Other DVWA modules remain vulnerable by design.
- Traffic between the browser and DVWA is plain HTTP on localhost; this is acceptable for a local lab but would need TLS if deployed publicly.
- The base image is end of life, so the Trivy gate is expected to keep failing until the base image is replaced.

