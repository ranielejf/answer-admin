# Service Boundaries

## Objective
Define clear boundaries between `answer` and `answer-admin`.

## `answer` (Service A)
- Public and end-user workflows.

## `answer-admin` (Service B)
- Internal/admin operations.
- Curation, moderation, reporting, and governance workflows.

## Data Model
- Shared MySQL database during parallel phase.
- Backwards-compatible changes only.
