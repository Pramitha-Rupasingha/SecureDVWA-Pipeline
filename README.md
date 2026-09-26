# SecureDVWA-Pipeline
AI-Powered DevSecOps Security Pipeline built around DVWA (Damn Vulnerable Web Application) — featuring automated SAST, dependency scanning, secrets detection, and container image scanning via GitHub Actions CI/CD.

## Threat Model

The threat model for the SecureDVWA-Pipeline project is based on the STRIDE methodology. The following application-specific threats were identified:

| STRIDE Category | Threat | Potential Impact |
|---|---|---|
| Spoofing | Weak or default credentials may allow unauthorized users to access the DVWA application. | Unauthorized access |
| Tampering | SQL Injection may allow malicious input to alter database queries and manipulate application data. | Data manipulation |
| Information Disclosure | SQL Injection may expose user information stored in the database. | Sensitive information exposure |
| Denial of Service | Malicious input or excessive requests may affect the availability of the application. | Service disruption |

### Risk Assessment Matrix

| Threat | Likelihood | Impact | Risk Level |
|---|---|---|---|
| Weak/default credentials (Spoofing) | High | High | High |
| SQL Injection (Tampering) | High | High | High |
| Information disclosure through SQL Injection | High | High | High |
| Application disruption through malicious input (Denial of Service) | Medium | High | Medium-High |

### Threat-to-Control Mapping

| Threat | Security Control |
|---|---|
| Weak/default credentials | Strong authentication, secure credential management, and removal of default credentials |
| SQL Injection | Parameterized queries / prepared statements and input validation |
| Information Disclosure | Input validation, output handling, and access control |
| Denial of Service | Input validation, request controls, and resource monitoring |