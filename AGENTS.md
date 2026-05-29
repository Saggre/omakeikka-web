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

The link target for a category card depends on context:

- Landing page (`/palvelutori/`) group cards: group taxonomy archive (`/palvelut/ryhma/{slug}/`) so visitors can preview the services in that group before committing
- All-categories archive (`/palvelut/`) cards: single SEO page (`/palvelut/{slug}/`) so search traffic lands on indexable content
- Single SEO page CTAs: the app's create page with `?category_id` pre-filled
- Group taxonomy archive (`/palvelut/ryhma/{slug}/`) cards: same app deep link, since the visitor has already chosen a group and is ready to act

This is intentional. Keep it consistent when adding new entry points.

## Deploy notes

When `_elementor_data` is updated outside the Elementor editor (for example via wp eval-file), Elementor's asset cache still hides the new content. Run after any such update:

```
wp elementor flush_css
```

The standard `wp acorn view:clear` and `delete_post_meta _elementor_css` are not sufficient on their own.
