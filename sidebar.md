Act as an expert Laravel, Livewire 3, and Alpine.js developer. I need a robust "Sidebar" layout component for my project "CinchCal".

**Context:**
- Stack: Laravel 12, Livewire 3, Alpine.js, Tailwind CSS.
- Design System: "Supabase-like" dark mode. Dark slate backgrounds, Teal accents.
- Icons: Lucide Icons (using the `blade-lucide-icons` package or raw SVGs).

**The Goal:**
Create a reusable Blade Layout Component (`resources/views/components/layouts/app.blade.php`) that includes a **Collapsible Sidebar** and a **Main Content Area**.

**Sidebar Requirements (Gemini/Supabase Behavior):**
1.  **State Management (Alpine.js):**
    - Use `x-data` to handle the sidebar state.
    - The state must persist across page reloads using `Alpine.$persist` or `localStorage`.
    - Variable: `isCollapsed` (boolean).

2.  **Desktop Behavior:**
    - **Expanded (`isCollapsed = false`):** Width is `w-64`. Sidebar shows Logo, Icon + Text Label for links.
    - **Collapsed (`isCollapsed = true`):** Width shrinks to `w-20`. Text Labels must disappear completely, leaving only centered Icons.
    - **Transition:** The width change must be smooth (`transition-all duration-300`).
    - **No "Clanky" UI:** When collapsing, the Text Labels must use `overflow-hidden` and `whitespace-nowrap` so they don't wrap or look broken during the animation.

3.  **Mobile Behavior:**
    - The sidebar should be hidden by default on mobile.
    - A "Hamburger" button in the top header toggles a slide-over drawer (overlay).

4.  **Visual Style (Dark Mode):**
    - Sidebar Background: `bg-slate-900` (or `bg-[#1E293B]` to match Supabase).
    - Border: Right border `border-r border-slate-800`.
    - Active Link: `bg-teal-500/10 text-teal-400 border-l-4 border-teal-500`.
    - Inactive Link: `text-slate-400 hover:text-slate-100 hover:bg-slate-800`.

5.  **Livewire Integration:**
    - Use `wire:navigate` on the links for SPA-like smooth page transitions.

**Please provide code for:**
1.  The Main Layout file (`app.blade.php`) containing the Alpine logic and the sidebar structure.
2.  A reusable Blade component for the links (`components/sidebar-link.blade.php`) that handles the logic of "if collapsed, hide text".

**Example Alpine Logic to start with:**
`<div x-data="{ collapsed: $persist(false) }" ...>`