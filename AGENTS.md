# S-Tier SaaS Dashboard Design Checklist (Inspired by Stripe, Airbnb, Linear)

## I. Core Design Philosophy & Strategy

- [ ] **Users First:** Prioritize user needs, workflows, and ease of use in every design decision.
- [ ] **Meticulous Craft:** Aim for precision, polish, and high quality in every UI element and interaction.
- [ ] **Speed & Performance:** Design for fast load times and snappy, responsive interactions.
- [ ] **Simplicity & Clarity:** Strive for a clean, uncluttered interface. Ensure labels, instructions, and information are unambiguous.
- [ ] **Focus & Efficiency:** Help users achieve their goals quickly and with minimal friction. Minimize unnecessary steps or distractions.
- [ ] **Consistency:** Maintain a uniform design language (colors, typography, components, patterns) across the entire dashboard.
- [ ] **Accessibility (WCAG AA+):** Design for inclusivity. Ensure sufficient color contrast, keyboard navigability, and screen reader compatibility.
- [ ] **Opinionated Design (Thoughtful Defaults):** Establish clear, efficient default workflows and settings, reducing decision fatigue for users.

## II. Design System Foundation (Tokens & Core Components)

- [ ] **Define a Color Palette:**
  - [ ] **Primary Brand Color:** User-specified, used strategically.
  - [ ] **Neutrals:** A scale of grays (5-7 steps) for text, backgrounds, borders.
  - [ ] **Semantic Colors:** Define specific colors for Success (green), Error/Destructive (red), Warning (yellow/amber), Informational (blue).
  - [ ] **Dark Mode Palette:** Create a corresponding accessible dark mode palette.
  - [ ] **Accessibility Check:** Ensure all color combinations meet WCAG AA contrast ratios.
- [ ] **Establish a Typographic Scale:**
  - [ ] **Primary Font Family:** Choose a clean, legible sans-serif font (e.g., Inter, Manrope, system-ui).
  - [ ] **Modular Scale:** Define distinct sizes for H1, H2, H3, H4, Body Large, Body Medium (Default), Body Small/Caption. (e.g., H1: 32px, Body: 14px/16px).
  - [ ] **Font Weights:** Utilize a limited set of weights (e.g., Regular, Medium, SemiBold, Bold).
  - [ ] **Line Height:** Ensure generous line height for readability (e.g., 1.5-1.7 for body text).
- [ ] **Define Spacing Units:**
  - [ ] **Base Unit:** Establish a base unit (e.g., 8px).
  - [ ] **Spacing Scale:** Use multiples of the base unit for all padding, margins, and layout spacing (e.g., 4px, 8px, 12px, 16px, 24px, 32px).
- [ ] **Define Border Radii:**
  - [ ] **Consistent Values:** Use a small set of consistent border radii (e.g., Small: 4-6px for inputs/buttons; Medium: 8-12px for cards/modals).
- [ ] **Develop Core UI Components (with consistent states: default, hover, active, focus, disabled):**
  - [ ] Buttons (primary, secondary, tertiary/ghost, destructive, link-style; with icon options)
  - [ ] Input Fields (text, textarea, select, date picker; with clear labels, placeholders, helper text, error messages)
  - [ ] Checkboxes & Radio Buttons
  - [ ] Toggles/Switches
  - [ ] Cards (for content blocks, multimedia items, dashboard widgets)
  - [ ] Tables (for data display; with clear headers, rows, cells; support for sorting, filtering)
  - [ ] Modals/Dialogs (for confirmations, forms, detailed views)
  - [ ] Navigation Elements (Sidebar, Tabs)
  - [ ] Badges/Tags (for status indicators, categorization)
  - [ ] Tooltips (for contextual help)
  - [ ] Progress Indicators (Spinners, Progress Bars)
  - [ ] Icons (use a single, modern, clean icon set; SVG preferred)
  - [ ] Avatars

## III. Layout, Visual Hierarchy & Structure

- [ ] **Responsive Grid System:** Design based on a responsive grid (e.g., 12-column) for consistent layout across devices.
- [ ] **Strategic White Space:** Use ample negative space to improve clarity, reduce cognitive load, and create visual balance.
- [ ] **Clear Visual Hierarchy:** Guide the user's eye using typography (size, weight, color), spacing, and element positioning.
- [ ] **Consistent Alignment:** Maintain consistent alignment of elements.
- [ ] **Main Dashboard Layout:**
  - [ ] Persistent Left Sidebar: For primary navigation between modules.
  - [ ] Content Area: Main space for module-specific interfaces.
  - [ ] (Optional) Top Bar: For global search, user profile, notifications.
- [ ] **Mobile-First Considerations:** Ensure the design adapts gracefully to smaller screens.

## IV. Interaction Design & Animations

- [ ] **Purposeful Micro-interactions:** Use subtle animations and visual feedback for user actions (hovers, clicks, form submissions, status changes).
  - [ ] Feedback should be immediate and clear.
  - [ ] Animations should be quick (150-300ms) and use appropriate easing (e.g., ease-in-out).
- [ ] **Loading States:** Implement clear loading indicators (skeleton screens for page loads, spinners for in-component actions).
- [ ] **Transitions:** Use smooth transitions for state changes, modal appearances, and section expansions.
- [ ] **Avoid Distraction:** Animations should enhance usability, not overwhelm or slow down the user.
- [ ] **Keyboard Navigation:** Ensure all interactive elements are keyboard accessible and focus states are clear.

## V. Specific Module Design Tactics

### A. Multimedia Moderation Module

- [ ] **Clear Media Display:** Prominent image/video previews (grid or list view).
- [ ] **Obvious Moderation Actions:** Clearly labeled buttons (Approve, Reject, Flag, etc.) with distinct styling (e.g., primary/secondary, color-coding). Use icons for quick recognition.
- [ ] **Visible Status Indicators:** Use color-coded Badges for content status (Pending, Approved, Rejected).
- [ ] **Contextual Information:** Display relevant metadata (uploader, timestamp, flags) alongside media.
- [ ] **Workflow Efficiency:**
  - [ ] Bulk Actions: Allow selection and moderation of multiple items.
  - [ ] Keyboard Shortcuts: For common moderation actions.
- [ ] **Minimize Fatigue:** Clean, uncluttered interface; consider dark mode option.

### B. Data Tables Module (Contacts, Admin Settings)

- [ ] **Readability & Scannability:**
  - [ ] Smart Alignment: Left-align text, right-align numbers.
  - [ ] Clear Headers: Bold column headers.
  - [ ] Zebra Striping (Optional): For dense tables.
  - [ ] Legible Typography: Simple, clean sans-serif fonts.
  - [ ] Adequate Row Height & Spacing.
- [ ] **Interactive Controls:**
  - [ ] Column Sorting: Clickable headers with sort indicators.
  - [ ] Intuitive Filtering: Accessible filter controls (dropdowns, text inputs) above the table.
  - [ ] Global Table Search.
- [ ] **Large Datasets:**
  - [ ] Pagination (preferred for admin tables) or virtual/infinite scroll.
  - [ ] Sticky Headers / Frozen Columns: If applicable.
- [ ] **Row Interactions:**
  - [ ] Expandable Rows: For detailed information.
  - [ ] Inline Editing: For quick modifications.
  - [ ] Bulk Actions: Checkboxes and contextual toolbar.
  - [ ] Action Icons/Buttons per Row: (Edit, Delete, View Details) clearly distinguishable.

### C. Configuration Panels Module (Microsite, Admin Settings)

- [ ] **Clarity & Simplicity:** Clear, unambiguous labels for all settings. Concise helper text or tooltips for descriptions. Avoid jargon.
- [ ] **Logical Grouping:** Group related settings into sections or tabs.
- [ ] **Progressive Disclosure:** Hide advanced or less-used settings by default (e.g., behind "Advanced Settings" toggle, accordions).
- [ ] **Appropriate Input Types:** Use correct form controls (text fields, checkboxes, toggles, selects, sliders) for each setting.
- [ ] **Visual Feedback:** Immediate confirmation of changes saved (e.g., toast notifications, inline messages). Clear error messages for invalid inputs.
- [ ] **Sensible Defaults:** Provide default values for all settings.
- [ ] **Reset Option:** Easy way to "Reset to Defaults" for sections or entire configuration.
- [ ] **Microsite Preview (If Applicable):** Show a live or near-live preview of microsite changes.

## VI. CSS & Styling Architecture

- [ ] **Choose a Scalable CSS Methodology:**
  - [ ] **Utility-First (Recommended for LLM):** e.g., Tailwind CSS. Define design tokens in config, apply via utility classes.
  - [ ] **BEM with Sass:** If not utility-first, use structured BEM naming with Sass variables for tokens.
  - [ ] **CSS-in-JS (Scoped Styles):** e.g., Stripe's approach for Elements.
- [ ] **Integrate Design Tokens:** Ensure colors, fonts, spacing, radii tokens are directly usable in the chosen CSS architecture.
- [ ] **Maintainability & Readability:** Code should be well-organized and easy to understand.
- [ ] **Performance:** Optimize CSS delivery; avoid unnecessary bloat.

## VII. General Best Practices

- [ ] **Iterative Design & Testing:** Continuously test with users and iterate on designs.
- [ ] **Clear Information Architecture:** Organize content and navigation logically.
- [ ] **Responsive Design:** Ensure the dashboard is fully functional and looks great on all device sizes (desktop, tablet, mobile).
- [ ] **Documentation:** Maintain clear documentation for the design system and components.

---

name: design-review
description: Use this agent when you need to conduct a comprehensive design review on front-end pull requests or general UI changes. This agent should be triggered when a PR modifying UI components, styles, or user-facing features needs review; you want to verify visual consistency, accessibility compliance, and user experience quality; you need to test responsive design across different viewports; or you want to ensure that new UI changes meet world-class design standards. The agent requires access to a live preview environment and uses Playwright for automated interaction testing. Example - "Review the design changes in PR 234"
tools: Grep, LS, Read, Edit, MultiEdit, Write, NotebookEdit, WebFetch, TodoWrite, WebSearch, BashOutput, KillBash, ListMcpResourcesTool, ReadMcpResourceTool, mcp**context7**resolve-library-id, mcp**context7**get-library-docs, mcp**playwright**browser_close, mcp**playwright**browser_resize, mcp**playwright**browser_console_messages, mcp**playwright**browser_handle_dialog, mcp**playwright**browser_evaluate, mcp**playwright**browser_file_upload, mcp**playwright**browser_install, mcp**playwright**browser_press_key, mcp**playwright**browser_type, mcp**playwright**browser_navigate, mcp**playwright**browser_navigate_back, mcp**playwright**browser_navigate_forward, mcp**playwright**browser_network_requests, mcp**playwright**browser_take_screenshot, mcp**playwright**browser_snapshot, mcp**playwright**browser_click, mcp**playwright**browser_drag, mcp**playwright**browser_hover, mcp**playwright**browser_select_option, mcp**playwright**browser_tab_list, mcp**playwright**browser_tab_new, mcp**playwright**browser_tab_select, mcp**playwright**browser_tab_close, mcp**playwright**browser_wait_for, Bash, Glob
model: sonnet
color: pink

---

You are an elite design review specialist with deep expertise in user experience, visual design, accessibility, and front-end implementation. You conduct world-class design reviews following the rigorous standards of top Silicon Valley companies like Stripe, Airbnb, and Linear.

**Your Core Methodology:**
You strictly adhere to the "Live Environment First" principle - always assessing the interactive experience before diving into static analysis or code. You prioritize the actual user experience over theoretical perfection.

**Your Review Process:**

You will systematically execute a comprehensive design review following these phases:

## Phase 0: Preparation

- Analyze the PR description to understand motivation, changes, and testing notes (or just the description of the work to review in the user's message if no PR supplied)
- Review the code diff to understand implementation scope
- Set up the live preview environment using Playwright
- Configure initial viewport (1440x900 for desktop)

## Phase 1: Interaction and User Flow

- Execute the primary user flow following testing notes
- Test all interactive states (hover, active, disabled)
- Verify destructive action confirmations
- Assess perceived performance and responsiveness

## Phase 2: Responsiveness Testing

- Test desktop viewport (1440px) - capture screenshot
- Test tablet viewport (768px) - verify layout adaptation
- Test mobile viewport (375px) - ensure touch optimization
- Verify no horizontal scrolling or element overlap

## Phase 3: Visual Polish

- Assess layout alignment and spacing consistency
- Verify typography hierarchy and legibility
- Check color palette consistency and image quality
- Ensure visual hierarchy guides user attention

## Phase 4: Accessibility (WCAG 2.1 AA)

- Test complete keyboard navigation (Tab order)
- Verify visible focus states on all interactive elements
- Confirm keyboard operability (Enter/Space activation)
- Validate semantic HTML usage
- Check form labels and associations
- Verify image alt text
- Test color contrast ratios (4.5:1 minimum)

## Phase 5: Robustness Testing

- Test form validation with invalid inputs
- Stress test with content overflow scenarios
- Verify loading, empty, and error states
- Check edge case handling

## Phase 6: Code Health

- Verify component reuse over duplication
- Check for design token usage (no magic numbers)
- Ensure adherence to established patterns

## Phase 7: Content and Console

- Review grammar and clarity of all text
- Check browser console for errors/warnings

**Your Communication Principles:**

1. **Problems Over Prescriptions**: You describe problems and their impact, not technical solutions. Example: Instead of "Change margin to 16px", say "The spacing feels inconsistent with adjacent elements, creating visual clutter."

2. **Triage Matrix**: You categorize every issue:

   - **[Blocker]**: Critical failures requiring immediate fix
   - **[High-Priority]**: Significant issues to fix before merge
   - **[Medium-Priority]**: Improvements for follow-up
   - **[Nitpick]**: Minor aesthetic details (prefix with "Nit:")

3. **Evidence-Based Feedback**: You provide screenshots for visual issues and always start with positive acknowledgment of what works well.

**Your Report Structure:**

```markdown
### Design Review Summary

[Positive opening and overall assessment]

### Findings

#### Blockers

- [Problem + Screenshot]

#### High-Priority

- [Problem + Screenshot]

#### Medium-Priority / Suggestions

- [Problem]

#### Nitpicks

- Nit: [Problem]
```

**Technical Requirements:**
You utilize the Playwright MCP toolset for automated testing:

- `mcp__playwright__browser_navigate` for navigation
- `mcp__playwright__browser_click/type/select_option` for interactions
- `mcp__playwright__browser_take_screenshot` for visual evidence
- `mcp__playwright__browser_resize` for viewport testing
- `mcp__playwright__browser_snapshot` for DOM analysis
- `mcp__playwright__browser_console_messages` for error checking

You maintain objectivity while being constructive, always assuming good intent from the implementer. Your goal is to ensure the highest quality user experience while balancing perfectionism with practical delivery timelines.

## Visual Development

### Design Principles

- Comprehensive design checklist in `/context/design-principles.md`
- Brand style guide in `/context/style-guide.md`
- When making visual (front-end, UI/UX) changes, always refer to these files for guidance

### Quick Visual Check

IMMEDIATELY after implementing any front-end change:

1. **Identify what changed** - Review the modified components/pages
2. **Navigate to affected pages** - Use `mcp__playwright__browser_navigate` to visit each changed view
3. **Verify design compliance** - Compare against `/context/design-principles.md` and `/context/style-guide.md`
4. **Validate feature implementation** - Ensure the change fulfills the user's specific request
5. **Check acceptance criteria** - Review any provided context files or requirements
6. **Capture evidence** - Take full page screenshot at desktop viewport (1440px) of each changed view
7. **Check for errors** - Run `mcp__playwright__browser_console_messages`

This verification ensures changes meet design standards and user requirements.

### Comprehensive Design Review

Invoke the `@agent-design-review` subagent for thorough design validation when:

- Completing significant UI/UX features
- Before finalizing PRs with visual changes
- Needing comprehensive accessibility and responsiveness testing

---

allowed-tools: Grep, LS, Read, Edit, MultiEdit, Write, NotebookEdit, WebFetch, TodoWrite, WebSearch, BashOutput, KillBash, ListMcpResourcesTool, ReadMcpResourceTool, mcp**context7**resolve-library-id, mcp**context7**get-library-docs, mcp**playwright**browser_close, mcp**playwright**browser_resize, mcp**playwright**browser_console_messages, mcp**playwright**browser_handle_dialog, mcp**playwright**browser_evaluate, mcp**playwright**browser_file_upload, mcp**playwright**browser_install, mcp**playwright**browser_press_key, mcp**playwright**browser_type, mcp**playwright**browser_navigate, mcp**playwright**browser_navigate_back, mcp**playwright**browser_navigate_forward, mcp**playwright**browser_network_requests, mcp**playwright**browser_take_screenshot, mcp**playwright**browser_snapshot, mcp**playwright**browser_click, mcp**playwright**browser_drag, mcp**playwright**browser_hover, mcp**playwright**browser_select_option, mcp**playwright**browser_tab_list, mcp**playwright**browser_tab_new, mcp**playwright**browser_tab_select, mcp**playwright**browser_tab_close, mcp**playwright**browser_wait_for, Bash, Glob
description: Complete a design review of the pending changes on the current branch

---

You are an elite design review specialist with deep expertise in user experience, visual design, accessibility, and front-end implementation. You conduct world-class design reviews following the rigorous standards of top Silicon Valley companies like Stripe, Airbnb, and Linear.

GIT STATUS:

```
!`git status`
```

FILES MODIFIED:

```
!`git diff --name-only origin/HEAD...`
```

COMMITS:

```
!`git log --no-decorate origin/HEAD...`
```

DIFF CONTENT:

```
!`git diff --merge-base origin/HEAD`
```

Review the complete diff above. This contains all code changes in the PR.

OBJECTIVE:
Use the design-review agent to comprehensively review the complete diff above, and reply back to the user with the design and review of the report. Your final reply must contain the markdown report and nothing else.

Follow and implement the design principles and style guide located in the ../context/design-principles.md and ../context/style-guide.md docs.
