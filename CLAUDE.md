# Tiny Steps Theme — Section Design Rules

Role: Read figma design, then make the exact design in WordPress (Use Figma MCP server and Verify each design using claude browser extension)
Follow these rules whenever you design or build a section, pattern, template or page for this theme.

## 0. Where to work: live site vs local site

-   **Live site — build every Figma page here:** https://tinysteps.1wp.site (admin: https://tinysteps.1wp.site/wp-admin/). Pages, posts, patterns, menus, media and site settings from the Figma design are always created on the live site, through the block editor in the browser (Claude in Chrome) or the `wp-tiny-steps` MCP server.
-   **Local site — testing only:** the WordPress Studio site this theme folder lives in. Use it to try out and check theme code changes before they go live. Content built here doesn't carry over to the live site, so never build Figma pages here as the real thing. `studio wp …` commands only reach the local site.
-   **Theme code (this repo):** always edit it locally, never in the live site's theme editor or over FTP. Ship it like this:
    1. Edit the files and run `npm run build` (the live site doesn't build, so the compiled `build/` folder must be committed).
    2. Test on the local site.
    3. Commit and push to `main` on https://github.com/zakariabinsaifullah/tiny-steps. **Deployer for Git** on the live site deploys `main` automatically through the push-to-deploy webhook. Release tags trigger a deploy of `main` too, so push the commit to `main` before tagging it.

## 1. Container: Kadence Row Layout

-   Wrap every section in a **Kadence Row Layout** block (`kadence/rowlayout`).
-   Don't use `core/group`, `core/cover` or `core/columns` as the outer container of a section.
-   **Global rule:** don't give blocks custom names (no `"metadata":{"name":...}`). Leave every block with its default name in the List View; the user renames blocks themselves if needed.

## 2. Block priority

Pick blocks in this order, and move down a level only when the level above can't do the job:

1. **Core WordPress blocks.** These are strongly preferred for all content: headings, paragraphs, buttons, images, lists, query loops and so on.
2. **Custom blocks in the Tiny Steps inserter category** (`tnys/*`):
    - Icon
    - Social Share
    - Carousel + Slide: any swipeable row of cards (slides per view, gap and slide min height per device; optional desktop grid, loop, autoplay). Slides take any blocks, plus an optional background photo with focal point and a dark bottom shade.
    - Carousel Arrows: remote prev/next buttons that control a Carousel by its HTML anchor (can sit anywhere, even inside a slide); per-device alignment and button size.
3. Always use Tiny Steps **Icon** block where icon is used, always use SVG code and saved them under **My Icons** for future use.
4. **Other Kadence blocks.** Use these only when neither core blocks nor the theme's custom blocks can do the job.

## 3. Row Layout spacing

-   A **standalone** Row Layout (a top-level section, not nested inside another Row Layout) uses:
    -   **100px** padding top and bottom
    -   **15px** padding left and right
-   In block markup that's `"padding":[100,15,100,15]`. The order is top, right, bottom, left.
-   on tablet use 70px top bottom padding (mobile will be auto like tablet)
-   **Global rule:** every top-level Row Layout has its top and bottom margin set to **0** (`"margin":[0,"",0,""],"marginUnit":"px"`). This applies everywhere: pages, posts, patterns, templates and template parts. Otherwise the theme's global block gap (1.2rem) adds about 19px between sections, on top of the padding.
-   Row Layouts nested inside another Row Layout don't get this default. Space them as the design needs.
-   but in case of same background based sections like two sections, both have same background, then use **Inner Space** 100px in total (50px-50px) while on tablet it will be (35px-35px)
-   **Gaps (global rule):** every gap between elements (heading to content, between items, between columns and rows, inside cards, icon to text) uses the exact value from the Figma frames: desktop values on desktop, mobile values on mobile, and desktop values on tablet unless Figma has a tablet frame. This keeps spacing consistent across the site. The fixed gap numbers in sections 3a, 3b and 6 are only fallbacks for when Figma doesn't show the spacing. Section padding (above) and card padding (24px) are not gaps and keep their rules.
-   When Figma doesn't show it, the space between a section title (heading block) and the content below it is **50px** on every device.
-   **Minimum heading spacing (global rule, overrides Figma):** on tablet and mobile there must be at least **16px** between any heading (section titles **and** card/item titles) and the content next to it. For Kadence columns set only the tablet/mobile `rowGap` to ≥16 (desktop keeps Figma); for Groups and Slides, whose gap isn't per device, use 16px on all devices. Figma's mobile heading rows often stack with 0px (e.g. heading → intro text → arrows); a heading row that stacks gets `"collapseGutter":"custom","customRowGutter":[16,16,16]` at minimum.

## 3a. Card columns (nested Row Layout)

-   Build card grids as a nested Row Layout (`kadence/rowlayout`) with one **Section** (`kadence/column`) per card.
-   Columns gap falls back to **24px** (Figma's value wins), both between columns and between rows: `"columnGutter":"custom","customGutter":[24,24,24],"collapseGutter":"custom","customRowGutter":[24,24,24]`.
-   On tablet and mobile, cards are **1 column** (`"tabletLayout":"row","mobileLayout":"row"`), unless Figma shows them as a horizontal slider on mobile: then build the cards as a **Carousel** (with "Show as grid on desktop" when desktop is a grid).
-   For image cards, don't use `core/cover`. Use the Section's own **Background Image + Overlay** settings (`backgroundImg`, `overlay` with a `tnys-*` colour, `overlayOpacity`).
-   Cards in the same row are always **equal height**. If the design gives a card height, set it as the Section's min height. Kadence adds padding on top of min height, so enter the design height minus the top and bottom padding (for example, 443px card − 48px padding = `"height":[395]`).
-   Card content is vertically aligned to the **bottom** (`"verticalAlignment":"bottom"`).
-   Card inner padding is an equal **24px** on all sides (`"padding":[24,24,24,24]`).
-   The gap between a card's title and its description follows Figma (fallback **16px**). Set it with the Section's **Vertical Gap** setting (`"rowGapVariable":["custom","",""],"rowGap":[16,"",""],"rowGapUnit":"px"`), not with block margins.
-   **Numbered items (global rule):** in a content item made of a number + heading + description (e.g. process steps "01 / 02 / 03"), the three are spaced by Figma's gap (fallback **16px**). Put them in one vertical Group with that `blockGap` and 0 margins; anything after the item (a divider, a button) sits outside that Group with its own gap.
-   The heading and paragraph inside a card have **0 margin and 0 padding** (`"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}`), so the Section's gap controls all spacing.

## 3b. Content columns (nested Row Layout)

-   For content layouts that aren't card grids, such as text beside an image, the column gap falls back to **70px** (Figma's value wins): `"columnGutter":"custom","customGutter":[70,70,70]`.
-   When the columns stack on tablet and mobile, the row gap falls back to **50px** (Figma's mobile value wins): `"collapseGutter":"custom","customRowGutter":[50,50,50]`.
-   Card grids keep the 24px gap from section 3a.

## 4. Style with existing block settings

Style blocks through the options they already have, in this order:

1. **Block settings in the editor sidebar:** typography, colour, spacing, border, layout.
2. **The theme's presets from `theme.json`.** Never hard-code values that a preset already covers.
    - **Colours:** `tnys-*`. Bay of Many is the primary and the heading colour, Bermuda the accent, and Bermuda Gray Dark the body text colour.
    - **Font sizes:** `tnys-heading-one` … `tnys-heading-six`, then `-extra-large`, `-large`, `-medium` (body default), `-small` and `-extra-small`.
    - **Fonts:** DM Sans for headings, Manrope for everything else.
    - **Spacing:** the `tnys-*` spacing presets.
3. **Registered block styles**, for example the button styles Alternative, Outline and Link, and the paragraph style **Underlined Links** (use it for any paragraph whose inline links are underlined in Figma; plain paragraph links are not underlined).
4. **The theme's editor extensions:**
    - Highlight
    - Annotation
    - Hover Color
    - Iconic Button
    - Read More Button
    - Text Max Width
    - Group Full Height, Group Overlay Background and Group Global Hover
    - Kadence Row Divider

## 5. No custom CSS or JS without permission

-   **Never** add custom CSS or JavaScript without asking the user first and getting a clear yes. This covers theme files, block "Additional CSS", inline `style` attributes that go beyond block settings, `theme.json` `css` fields, and new scripts.
-   If a design can't be built with the rules above, stop. Explain what's missing and propose the CSS/JS change, then wait for approval.

## 6. Images, Icons, Content

-   use exact text used in figma design
-   for each page, you will find its corresponding images at another page of its right side, take them from there, Like: Assets • Homepage
-   for Icons, always use SVG codes
-   **Uploading images (global rule):** the site uses **BinsOptimizer**, which converts images to WebP and compresses them **in the browser**, before the upload reaches the server. It hooks the block editor's `mediaUpload` setting. So:
    -   Upload images to the **live site's** media library, because that's where the pages are built (section 0).
    -   **Never** upload images with `studio wp media import`, the REST API directly, or by copying files into `uploads/`. Those skip BinsOptimizer and leave JPEG/PNG files unconverted.
    -   Upload through the block editor in the browser (Claude in Chrome), using the wrapped `mediaUpload` function:
        1. Give the browser a URL it can fetch the source file from. On the local site that can be a temporary folder under `wp-content/uploads/` (e.g. `claude-upload-tmp/`). On the live site you can't drop files into `uploads/`, so first confirm that a fetch from the live editor page works (e.g. the Figma asset URL). If nothing works, stop and ask the user.
        2. Open any post or page in the live site's editor and wait until `wp.data.select('core/block-editor').getSettings().mediaUpload.__biioWrapped` is `true`.
        3. Fetch the file as a blob, wrap it in a `File` with the right name and type, and call `mediaUpload({ filesList: [file], allowedTypes: ['image'], onFileChange, onError })`. Use the returned `id` and `url` (they end in `.webp`) in the blocks.
        4. Delete any temporary folder or file afterwards.
    -   Check the result: the attachment's file should be `.webp` and it should have `_biio_savings` / `_biio_optimized_at` meta.
-   Icon (SVG) size follows Figma (fallback **24px**). Set only the desktop size (`"sizes":{"Desktop":24},"iconSize":24`); tablet and mobile inherit it. Icons of the same kind must share one size across the site (see section 8).
-   **Icon + title gap (global rule):** when an Icon block shows a title next to the icon, the gap between them follows Figma (fallback **16px**, `"listGap":"16px"`). If you set it by script, also set `blockStyle["--list-gap"]` to the same value: the block only recalculates `blockStyle` when it renders in the editor, so changing `listGap` alone can leave the old gap in the saved markup.
-   When an icon sits in a box (border or background), the inner padding makes the box match Figma: (box size − icon size) ÷ 2, e.g. 26px icon in a 56px circle = **15px**, 22px in 48px = 13px, 18px in 36px = 9px. Fallback 15px. Don't add padding to icons without a box.
-   **Opening hours (global rule):** never type clinic times into blocks. Use the Office Hours placeholders (Appearance → Office Hours lists them all with their current values): `{{open_status}}` (e.g. "Open today until 9 PM" / "Opens tomorrow at 9 AM"), `{{opening_time}}`, `{{closing_time}}`, `{{hours_today}}`, `{{hours_weekdays}}` / `{{hours_weekend}}` ("9:00 AM to 9:00 PM"), `{{hours_weekdays_short}}` / `{{hours_weekend_short}}` ("9 AM to 9 PM"), `{{closing_weekdays}}` / `{{closing_weekend}}`, `{{phones_weekdays}}` / `{{phones_weekend}}`, and per day `{{hours_mon}}` … `{{hours_sun}}`, `{{phones_mon}}` … `{{phones_sun}}`. They are replaced in any block's rendered HTML (site timezone: America/Los_Angeles). If page caching is ever turned on, the status can go stale; tell the user.
-   When reusing an icon, take it from **My Icons** first; only add a new one when no existing icon matches. Normalise new SVGs to `fill="currentColor"` + `stroke="currentColor"` (keep Figma's stroke width) and round path numbers to 2 decimals.

## 7. Color, Typography

-   always use Tiny Steps theme color and typography
-   never use custom color and typography without permission
-   **Exception, transparent colours:** when Figma uses a transparent colour (e.g. a divider in Bay of Many at 16%), use it as a custom `rgba()` value built from the matching `tnys-*` colour, e.g. `rgba(36, 72, 132, 0.16)`. Solid colours always use the `tnys-*` presets.
-   never use letter spacing, custom line-height, etc without permisson

## 8. Consistency standards (measured on the finished homepage)

Every new section and page must match these. Check them at 1440, 820 and 390px before calling a section done.

-   **Typography (all from presets):**
    -   Page H1: `tnys-heading-one` (72 → 40px). Only the hero H1 has letter spacing (approved).
    -   Section title (H2): `tnys-heading-two` (52 → 34px), one per section.
    -   Sub-section title (H2/H3 inside a section, e.g. "Conditions we treat.", "Where to find us."): `tnys-heading-four` (36 → 26px).
    -   Card / item title: `tnys-heading-six` (22 → 20px). Larger feature numbers or time slots: `tnys-heading-five`.
    -   Lead paragraph: `tnys-large` (18 → 16px) or `tnys-extra-large` (22 → 18px) for quotes / hero intro. Body: `tnys-medium` (16px). Card body and meta text: `tnys-small` (14px).
    -   Weights: headings 700; emphasised body 500; names/labels 700. Line height: headings 1.2, text 1.5 (theme defaults, never override).
-   **Section header pattern:** 56×4px rule (radius 2px) → 18px gap → H2 (→ intro paragraph). On white or light-grey sections the rule is Bermuda and the H2 Bay of Many; on Bermuda Lighter (teal) sections the rule is Bermuda Dark and the H2 Bay of Many Darker. Cards use a 32×4px Bermuda tag rule.
-   **Section backgrounds** alternate: white → Bermuda Gray Lightest / Bermuda Lighter → white …, with Bay of Many for the closing CTA. Two neighbouring sections with the same background share their space (section 3).
-   **Cards:** radius **16px** (small info boxes inside a section: 12px). White cards on white/teal: 1px Bermuda Gray Lighter border (or `rgba(36, 72, 132, 0.16)` when Figma uses it) + `tnys-shadow-subtle`. Floating cards over photos/colour: `tnys-shadow-card` / `tnys-shadow-float`. Logo circles: `tnys-shadow-logo`.
-   **Buttons:** primary buttons use the theme default (Bay of Many, 999px radius, 16/28px padding, weight 500, 56px tall), with the Iconic Button extension for icons (20px icon, 10px gap). Don't give individual buttons custom padding. The only smaller variant is the compact button inside small cards (12/20px), as on the map card.
-   **Carousel arrows:** 48px by default; 44px inside cards (as in the reviews card).
-   **Icon sizes by kind:** feature/testing card icons 36px; badge icons in a Bermuda circle 26px in 56px; ER/list icons per Figma; small inline icons (pill, utility bar) 18–20px.
-   **Links:** inline links in body copy use the Underlined Links style, coloured Bermuda Darker on light backgrounds, Bay of Many on teal, Bermuda on Bay of Many.

## 9. Reuse homepage sections for inner pages

Build inner pages from the homepage sections whenever the Figma section matches one of them (same layout, different content). Only build a new section when no existing one fits. The live homepage is page **15**; its top-level Row Layouts are:

| uniqueID | Section | Reuse for |
|---|---|---|
| `tnys-home-hero` | Hero: H1 + intro + button over photo, plus the "Get care." 3-card bar | Page heroes with a photo |
| `tnys-home-hours` | "Clinic hours." centred title over photo + 2-column hours box | Hours / schedule blocks |
| `tnys-home-where` | Copy + info boxes beside a map card | Location / contact sections |
| `tnys-home-different` | Photo collage + copy, then a 5-card icon grid (hover cards) | Feature lists with icons |
| `tnys-home-er` | Copy + red ER list beside photo with overlapping checklist card, call strip, notes | Comparison / "when to go" content |
| `tnys-home-help` | Heading row with arrows, image-card carousel, icon-card grid/slider, image + colour band | Services / conditions / testing lists |
| `tnys-home-walkin` | Teal band: heading row, 3 numbered steps with dividers, info pill | Process steps, how-it-works |
| `tnys-home-insurance` | Centred header, overlapping logo circles, info pill | Insurance / partner logos |
| `tnys-home-reviews` | Header with rating, featured review carousel, 2 small review cards | Testimonials |
| `tnys-home-about` | Copy beside two offset photos | Intro / about blocks |
| `tnys-home-cta` | Closing CTA card over Bay of Many with stethoscope | End-of-page call to action |

How to reuse a section:
1. Copy it from the homepage editor (block toolbar → Copy, or read it with `wp.data.select('core/block-editor')` / the `get_post_blocks` MCP tool) and paste it into the new page.
2. Rename **every** Kadence `uniqueID` in the copy to the new page's prefix (e.g. `tnys-home-help-…` → `tnys-services-help-…`). IDs must be unique on a page, and unique IDs keep the CSS per block predictable.
3. Change only the content (text, images, icons, links, number of items). Keep the spacing, sizes, colours and structure, so the page stays consistent with the homepage.
4. Check it at 1440 / 820 / 390px like any new section.

## 10. Layout gotchas (Kadence and core blocks)

-   **Per-device column gap:** Kadence uses `customGutter` tablet/mobile values only when `tabletGutter:"custom"` and `mobileGutter:"custom"` are also set; otherwise every device uses the desktop value.
-   **Kadence columns in a row stretch to equal height**, and `height` is only a min height. To make one photo shorter than its neighbour, put it in a nested row (column background image + height) and bottom-align the outer column.
-   **Kadence per-device overlays:** this Kadence version outputs only the **opacity** of a tablet/mobile overlay, not its gradient, colour or image; every device gets the desktop overlay. For a different mobile overlay, bake the tint into a mobile background image (canvas composite, uploaded through BinsOptimizer) and set the mobile overlay opacity to 0. Always check text on photo/overlay backgrounds at 390px for contrast.
-   **Groups clip their content** (`.wp-block-group{overflow:clip}` in `blocks.css`), so shadows or anything pushed outside a Group get cut. Give the Group padding big enough for the shadow and the same negative margin (e.g. padding 4/16/24/16px, margin −4/−16/−24/−16px).
-   **Core flex children:** "Fixed" (`selfStretch:"fixed"`) can still shrink; use `fixedNoShrink` for avatars and badges. In a vertical flex group, a fixed size sets the height, so wrap fixed-width items (like the 56×4px rule) in a horizontal flex group, and set the vertical parent to `justifyContent:"stretch"` so separators and rules get a width.
-   **Editor scripts:** `getClientIdsWithDescendants(id)` ignores the id and returns the whole page; walk `getBlock(id).innerBlocks` instead. After every scripted save, compare the latest two revisions section by section to make sure only the intended section changed. Fetch revisions with `orderby=id&order=desc` (revision dates mix UTC and site time since the timezone change, so date order is wrong), and ignore autosaves (`/autosaves`) from other editor tabs.
