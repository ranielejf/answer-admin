# Current State

## Objective
Track the current operational status of `answer-admin`.

## Snapshot Date
- 2026-05-16

## Summary
- Laravel app scaffold exists.
- Filament parallel implementation strategy documented.
- AGENTS and docs standards are in place.
- Admin authentication decision is defined: SSO is the official access model; local login is temporary and limited to local testing.
- Scope decision updated: `answer-admin` is admin-only and is no longer a full migration target for all `answer` features.

## Open Items
- Confirm DB connectivity against shared database.
- Install/configure Filament panel workflows.
- Prioritize admin-only parity for backoffice workflows.

## Authentication Decision Note (2026-05-16)
- Official admin authentication model: SSO.
- Local login is allowed only for development/testing in local environment.
- Local login must remain disabled in production.
- Current local user exists only to validate application behavior before SSO cutover.

## Scope Decision Note (2026-05-16)
- Previous direction (full `answer` migration) is deprecated.
- Current direction: `answer-admin` is only the administration panel for the main project.
- End-user features (including Q&A flows) remain in the main `answer` application and are out of scope for `answer-admin`.
