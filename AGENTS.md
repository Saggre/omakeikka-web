# AGENTS.md

Project-local rules for AI coding agents. Adds to the global rules in `~/.claude/CLAUDE.md` and the codebase docs in `CLAUDE.md`.

## Copy rules

### No dashes in user-facing text

Do not use dashes of any kind in Finnish prose, headings, button labels, or other user-facing strings:

- No em dash `—`
- No en dash `–`
- No hyphen `-` as a sentence connector or aside marker

Use a comma `,` for asides and pauses, and a colon `:` for introductions and lists.

Acceptable hyphens: inside compound words written that way in Finnish dictionaries (for example `IT-tuki`, `kevyt-yrittäjä` is not standard but `kevytyrittäjä` is), file paths, slugs, CSS class names, code identifiers, and CLI flags.

Examples:

```
bad:  Julkaise tehtävä palvelutorille — tekijät vastaavat tunneissa.
good: Julkaise tehtävä palvelutorille: tekijät vastaavat tunneissa.

bad:  Valitse sopivin tekijä – sovi yksityiskohdat suoraan.
good: Valitse sopivin tekijä, sovi yksityiskohdat suoraan.
```

### Marketplace and palvelutori copy comes from the omakeikka app

Finnish strings for the palvelutori feature must be copied verbatim from the omakeikka Laravel app, not composed locally. The source of truth is:

- `~/repos/omakeikka/lang/fi.json` for translated strings
- `~/repos/omakeikka/resources/ts/Pages/Marketplace/Index.vue` for landing copy
- `~/repos/omakeikka/database/seeders/MarketplaceCategorySeeder.php` for category names and slugs
- `~/repos/omakeikka/resources/ts/composables/useCategoryIcon.ts` for per-category Bootstrap Icons classes
- `~/repos/omakeikka/resources/ts/composables/useCategoryGroups.ts` for group names, group icons and sort order ranges

When you need a string the app doesn't already define, ask first or note the gap in the PR description, do not invent Finnish copy.

## Palvelutori link targets

**The principle: buyer intent stays on omakeikka.fi, seller intent goes to the app.**

The reason is structural, not cosmetic. In the omakeikka Laravel app, `marketplace_requests.poster_employee_profile_id` is a non-nullable FK to `employee_profiles`, and the whole `marketplace-requests` route group sits behind `auth + verified + onboarded + employee`. A household sent to `app.omakeikka.fi/marketplace-requests/create` registers, verifies, onboards, and is then refused for not being an employee. Linking a buyer there is a dead end, so buyers go to the WordPress help request form instead.

| Entry point | Target |
|---|---|
| Landing page (`/palvelutori/`) group cards | help request form, group pre-filled |
| Homepage category grid widget cards | help request form, group pre-filled |
| All-categories archive (`/palvelut/`) cards | single SEO page (`/palvelut/{slug}/`), unchanged, so search traffic lands on indexable content |
| All-categories archive group headings | group taxonomy archive, this is the only crawl path left to `/palvelut/ryhma/*` |
| Group taxonomy archive cards and CTAs | help request form, group and category pre-filled |
| Single SEO page buyer CTAs | help request form, group and category pre-filled |
| Single SEO page "Luo tili omakeikkaan" and "Rekisteröidy palveluun" | app register, unchanged, seller path |
| `single-occupation.blade.php` "Luo oma profiili" | app register, unchanged, aimed at the professional |
| App `/marketplace-requests/create` for guests and non-employees | help request form with only `?palvelu=` (the app has no groups), the group is derived from the category's `group_key` |

Card hrefs come from `App\Services\MarketplaceCategoryQuery`, never hardcoded. `formUrl( $group, $category )` builds the form link and falls back to the app create URL when the form page does not exist yet, which matters because deploy order is code first, content second.

**Adding a key to `groups()` or `mapCategory()` requires bumping the transient cache key in the same commit.** The cached arrays are read by key in three templates, so a stale pre-change array produces `Undefined array key` warnings for up to an hour. `deploy.php` runs `wp transient delete --all`, but code goes live before that task, so the bump is what actually closes the window.

## Deploy notes

When `_elementor_data` is updated outside the Elementor editor (for example via wp eval-file), Elementor's asset cache still hides the new content. Run after any such update:

```
wp elementor flush_css
```

The standard `wp acorn view:clear` and `delete_post_meta _elementor_css` are not sufficient on their own.
