# Filament Admin Implementation Strategy

## Objective
Build `answer-admin` as a Laravel 12 + Filament administration service for `answer`, reusing the same MySQL database and explicitly excluding end-user product flows from this codebase.

## Strategic Decisions
- Admin-only service, not a full product migration target.
- `answer` remains Service A (public/user).
- `answer-admin` becomes Service B (internal/admin).
- Shared database during migration phase.
- Filament auth as default; Breeze not required by default.
- SSO-first for admin access, with local-login fallback only in local/dev and behind feature flag.

## Service Boundaries
### Service A (`answer`)
- Public landing and user-facing flows.
- End-user authenticated panel and Q&A experience.

### Service B (`answer-admin`)
- Internal/backoffice operations.
- Users, roles, workspaces, versions, login logs.
- Curation/moderation workflows.
- Internal reports and exports.
- Read/operate admin data only; no end-user Q&A runtime features.

## Shared Database Governance
- During current CRUD replication phase, `answer-admin` must not run schema-changing migrations.
- Schema changes require prior approval and compatibility checks for both apps.
- Use additive schema evolution first; avoid rename/drop until legacy admin deactivation.
- Keep one migration authority during overlap (recommended: `answer` app).

## RBAC Baseline (v0)
- `super_admin`: full access.
- `admin`: broad access, excluding governance-critical actions.
- `user`: no access to admin app.

Governance-critical actions (v0): role/permission governance, destructive high-impact actions, security-sensitive configuration.

## Authentication Strategy
- Official model: SSO for admin access.
- Local fallback: only when both are true:
  - `APP_ENV=local`
  - `ADMIN_LOCAL_LOGIN_ENABLED=true`
- Never enable local fallback in production.
- Log both successful and failed local login attempts.

## Execution Plan
1. Discovery and contracts
- Confirm route/domain boundaries, auth model, migration authority, and parity matrix.

2. Parallel bootstrap
- Keep `answer-admin` connected to the same DB (local scope first).

3. Incremental admin parity by waves
- Wave 1: Users, Roles, Workspaces, Login Logs.
- Wave 2: Versions and maintenance operations.
- Wave 3: Curation/moderation and operational reporting.

4. Validation and hardening
- UAT, authorization parity, export/report validation, security checks.

5. Controlled cutover
- Enable admin access via `answer-admin`, keep rollback window active.

## Blade -> Filament Replication Method
1. Inventory legacy admin route/controller/view and map to Filament Resource/Page/Widget/Action.
2. Rebuild workflow in Filament.
3. Validate parity: permissions, filters, search, sort, exports, critical actions.
4. Mark done only when behavior and access model match legacy intent.

## Cutover Readiness Criteria
- Functional parity complete for required workflows.
- Authorization parity validated for all internal personas.
- Operational parity validated (audit, reports, exports).
- Performance baseline acceptable.
- Rollback path tested.

## Legacy Admin Deactivation
1. Keep public/user area active in `answer`.
2. Disable legacy admin routes behind feature flag after parity sign-off.
3. Monitor metrics and logs during stabilization.
4. Remove legacy admin code only after steady-state confirmation.

## Explicit Out of Scope
- Migrating full end-user product experience into `answer-admin`.
- Implementing end-user question/answer runtime flows in this repository.
- Replacing Service A (`answer`) as the public application.

## Local Connectivity Notes
- Credentials source of truth: `/Users/ranielejf/Projetos/answer/.env`.
- If app runs inside Docker network, use `DB_HOST=mysql`.
- If app runs on host, use published DB host/port (often `127.0.0.1`).

## Immediate Next Steps in `answer-admin`
1. Confirm environment and DB connectivity.
2. Install/configure Filament panel.
3. Implement Wave 1 resources with parity checks.
4. Add role-based access gates for RBAC v0.
5. Prepare parity checklist per migrated workflow.
