FROM vulnerables/web-dvwa:latest

# Bake the remediated (fixed) vulnerability source files into the image itself,
# instead of relying on runtime volume mounts. This ensures the image we scan
# with Trivy is the SAME image that reflects our secure coding fixes.

COPY vulnerabilities/sqli/source/low.php /var/www/html/vulnerabilities/sqli/source/low.php
COPY vulnerabilities/xss_r/source/low.php /var/www/html/vulnerabilities/xss_r/source/low.php
COPY vulnerabilities/exec/source/low.php /var/www/html/vulnerabilities/exec/source/low.php
COPY vulnerabilities/csrf/source/low.php /var/www/html/vulnerabilities/csrf/source/low.php