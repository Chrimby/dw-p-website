# Repository Guidelines

## Project Structure & Module Organization
PixelPulse is a full-stack TypeScript workspace. `client/src` houses the Vite + React UI, organized by feature folders (`components`, `pages`, `hooks`). `server` hosts the Express entry point (`server/index.ts`), routing, and session storage helpers, while `shared/schema.ts` centralizes Drizzle ORM models shared across layers. Static assets live in `public` and design-reference files sit in `attached_assets`. Path aliases (`@`, `@shared`, `@assets`) are configured in `vite.config.ts`.

## Build, Test, and Development Commands
Use `npm run dev` to start the Express API through `tsx` with hot reload and serve the Vite client. Run `npm run build` to compile the client bundle to `dist/public` and bundle the Node server with esbuild. `npm run start` boots the compiled server from `dist/index.js` for production validation. `npm run check` executes `tsc` to enforce type safety, and `npm run db:push` applies Drizzle schema changes.

## Coding Style & Naming Conventions
Stick to TypeScript across client and server with ES module syntax. Favor functional React components and hook-based state. Use 2-space indentation, const-first declarations, and descriptive camelCase names; reserve PascalCase for components and schema objects. Keep shared logic in `shared/` or `client/src/lib/`. Tailwind utility classes power styling—follow spacing and typography guidance in `design_guidelines.md`.

## Testing Guidelines
Automated tests are not yet configured, so there is no coverage threshold. When adding tests, co-locate them beside the module using `.test.tsx` or `.test.ts`, and document manual QA steps in your PR. Prefer Vitest + React Testing Library for UI code and Supertest for Express routes; add minimal dependencies needed to keep runs fast. Update this section if you formalize a test runner.

## Commit & Pull Request Guidelines
Commits follow concise, sentence-case imperatives (see `git log`) describing the user-facing impact. Group related changes per commit. PRs should include a short summary, screenshots or videos for UI updates, linked Linear/GitHub issues, and bullet-point test notes. Highlight schema or config changes, call out any follow-up tasks, and confirm that `npm run check` and `npm run build` pass before requesting review.

## Design & Asset References
Consult `design_guidelines.md` for typography, spacing, and color rules before touching UI. Source imagery and fonts from `attached_assets/` and note any additions in your PR to keep design reviews efficient.
