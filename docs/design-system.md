# GymXBook CRM design system

## Direction

A polished growth workspace with a calm light content canvas and a light, grouped navigation rail. Pages prioritize confident typography, clear data hierarchy, generous breathing room, and purposeful actions. Flame Orange signals primary actions; status colors stay restrained and consistent. The design uses self-hosted type and directly served static CSS/JavaScript, so the UI works without npm or a generated asset bundle.

## Tokens

- Brand: Flame Orange `#FF5A1F`; hover `#E94B16`; pale accent `#FFF0E9`.
- Canvas `#F3F5F7`; surface `#FFFFFF`; text `#202A35`; muted `#667383`; border `#E4E8ED`.
- Navigation: white `#FFFFFF` rail, neutral slate labels/icons, pale Flame Orange active state `#FFF2EC`; success `#16845F`; warning `#A86C13`; danger `#C84F50`; info `#416FC4`.
- Sidebar width 264px; content max 1680px; 4/8px spacing rhythm; 10–22px corner radius; soft layered shadows.
- Typography is self-hosted: Poppins for all interface text and Space Grotesk only for the `GymXBook` wordmark/brand mentions. The files and SIL Open Font License notices are in `public/fonts/`; no npm step, external font request, or network access is needed. Body copy defaults to 16px; metrics use tabular numerals and controls remain comfortably sized.

## Interaction principles

- Responsive sidebar becomes a drawer on small screens; tables retain readable horizontal scroll rather than crushing columns.
- Loading, empty, error, and success states use the same surface/spacing language.
- Lead create/edit/preview opens in an accessible modal; lead search, filters, pagination, status updates, and deletion use delegated AJAX while preserving Laravel validation and policy checks.
- Live search exposes a loading spinner; destructive actions use a custom, keyboard-accessible confirmation dialog rather than native browser prompts.
- Native form controls remain keyboard-accessible; state is not communicated by color alone.
- Motion is subtle, short, and disabled under `prefers-reduced-motion`.
