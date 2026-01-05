Goal
- Change slug semantics so the same slug can exist across content types (Post/Project/Thought), while keeping production deploys safe and low-risk.

Current state / risks observed
- `contents.slug` is globally unique today;

Preferred approach (allow duplicates across types without rewriting slugs)
1) Schema: add a `content_type` (or `kind`) column on `contents` (nullable initially).
2) Backfill: set `content_type` for existing rows based on which relation exists (post/thought/project).
3) Indexes: replace unique index on `contents.slug` with composite unique (`content_type`, `slug`).
4) Code: update all read/write paths to always scope slug queries by content type.
5) Validation: update uniqueness validation rules to be unique within `content_type`.

Production rollout strategy (safe)
- Deploy A (backwards compatible)
  - Add nullable `content_type` (tinyint) column.
  - Ship code that, on new writes, sets `content_type` and scopes queries using either `content_type` when present or fallback to existing behavior.
  - Add new enum ContentType with values Post, Project, Thought.
- Backfill (out of band)
  - Run an Artisan command/job to fill `content_type` in batches (no long global transaction).
  - Make it idempotent and resumable.
- Deploy B (enforcement)
  - Add composite unique index (`content_type`, `slug`).
  - Drop old unique index on `slug`.
  - Make `content_type` non-nullable if desired.

Verification checklist
- Confirm all slug-based lookups are type-scoped (show/edit/update/destroy, Form Requests).
- Confirm unique constraints and validation enforce uniqueness per type.
- Ensure backfill can be re-run safely and handles large tables.
- Add/adjust tests for slug uniqueness and routing per type.

Open questions
- Do you want URLs to stay the same (`/posts/{slug}`) while allowing duplicates across types? (Recommended)
Yes, this is preferred.

- Or do you explicitly want prefixed slugs stored and shown in URLs (e.g. `/content/post-foo`)?
No, keep URLs unchanged.

- Should `content_type` be derived from existing relations or stored directly going forward?
It should be stored directly going forward.
