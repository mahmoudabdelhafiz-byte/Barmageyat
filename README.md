# Barmageyat Website

Source control for the public-facing Barmageyat corporate website.

Website: https://barmageyat.net/

## Source of truth and deployment

The repository is intended for **public website assets only**, not a dump of the entire cPanel document root. Deploy static public site files to the configured web document root after review. Do not delete or overwrite server-managed configuration and business applications when deploying.

## Security boundary

The existing hosting archive contains a separate, legitimate internal accounting application under `Cash_Management/`, database dumps, uploaded receipts and configuration. Those files **must never be committed here**. The accounting application remains in its own protected deployment.

Never commit credentials, `.env`, `config.php`, SQL backups, logs, uploads containing personal data, cPanel files, or other server-only material.

## Website products

- CardIQ Identity
- ManpowerIQ
- AttendanceIQ
- CCTV-IQ
- AI Data Analytics
- Yardsyncs TAS & Autogate

## Project hero design

Use the CardIQ reference palette, with navy `#071b3b`, white text, and consistent two-column product heroes. Yardsyncs has a slot-board screenshot on the right.

## Next action

Import the sanitized public website snapshot from the prepared source archive. Validate that every HTML, CSS, JavaScript and image path resolves, and use a preview or staging deployment before switching the live cPanel site.

The repository initialization does not itself deploy or change the live website.
