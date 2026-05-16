# Filament Bootstrap Runbook

## Objective
Bootstrap Filament in `answer-admin` with shared-db constraints.

## Preconditions
- Local environment configured.
- Database credentials validated.
- Shared-DB freeze rule acknowledged (no schema change in this phase).

## Steps
1. Validate environment variables.
2. Install/verify Filament packages.
3. Configure admin panel and auth flow.
4. Validate local panel access.

## Validation
- Panel route is reachable locally.
- RBAC baseline can be enforced.

## Rollback
- Revert panel config changes if bootstrap fails.
