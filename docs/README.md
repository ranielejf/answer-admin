# Documentation Guide (`answer-admin`)

This folder is the canonical place for operational and project documentation for `answer-admin`.

## Language Policy
- Chat with user: Portuguese.
- Development artifacts: English only.
- Do not add new Portuguese text in UI labels, error messages, validation text, tests/asserts, code comments, or functional documentation.

## Documentation Principles
- Keep docs actionable and short.
- Prefer decision records over long narrative text.
- Always include context, decision, impact, and next steps.
- Update docs in the same change that introduces behavior changes.

## Suggested Structure
- `docs/operations/`: runbooks, deployment notes, production alignment.
- `docs/runbooks/`: step-by-step execution guides.
- `docs/devlogs/`: timestamped implementation logs.
- `docs/architecture/`: service boundaries, contracts, data-flow notes.

## File Naming
- Use kebab-case names.
- For logs, use `YYYY-MM-DD_hhmm_topic.md`.
- Prefer explicit names (example: `filament-rbac-v0.md`).

## Minimum Template (for new docs)
1. Objective
2. Scope
3. Current state
4. Decision / Implementation
5. Risks and mitigations
6. Validation performed
7. Open items / Next steps

## Git and Change Hygiene
- Separate commits by concern when multiple commits are needed:
  1. structural cleanup
  2. functional code
  3. documentation
- Do not mix unrelated refactors with documentation-only updates.

## Relation with AGENTS
- `AGENTS.md` defines coding and operational behavior for Codex/agents.
- This `docs/` folder records project decisions and execution history.

## Legacy Consultation Source
- `/Users/ranielejf/Projetos/answer` is the legacy consultation project and should be used as a reference for functional behavior, business rules, and migration parity while designing the new portal in `answer-admin`.
