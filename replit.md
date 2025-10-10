# B2B Marketing Assessment - 4R-System

## Overview

**Standalone B2B Marketing Assessment** built on the Brixon 4R-System framework (Reach, Relate, Respond, Retain). A fully functional, production-ready HTML/CSS/JavaScript application that evaluates a company's marketing maturity across four key phases.

**Key Features:**
- ✅ **Fully functional** - Works immediately without any configuration
- 🇩🇪 **German language** - Complete questionnaire and results in German
- 🎨 **Modern UI** - Clean design with black/white/yellow (#f7e74f) branding
- 📊 **Scoring System** - 121 points max across 4 phases with radar chart visualization
- 🔗 **Optional Webhook** - Send results to external systems (Make.com, Zapier, n8n)
- 💾 **localStorage Backup** - All data saved locally, even without webhook
- 📱 **Responsive Design** - Works on all devices

**Access:** Navigate to `/assessment/` to use the standalone application.

The assessment captures context (company size, lead volume), evaluates marketing capabilities through 35+ scored questions, and provides personalized results with visual scoring and recommendations.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture

**Framework**: React 18 with TypeScript and Vite as the build tool. The application uses a component-based architecture with wouter for client-side routing.

**UI Component System**: Implements shadcn/ui (New York variant) with Radix UI primitives as the foundation. All components follow a consistent design system with CSS custom properties for theming. The configuration uses Tailwind CSS with custom spacing units (4, 6, 8, 12, 16, 24) and custom border radius values.

**Styling Strategy**: Tailwind CSS with a comprehensive custom theme defined in `tailwind.config.ts`. Uses CSS variables (HSL color space) for dynamic theming with support for light/dark modes. Custom utility classes include `hover-elevate` and `active-elevate-2` for interactive states. Typography system uses two font families: "area-extended" for headlines/display text and "area-normal" for body/UI text.

**State Management**: React Query (TanStack Query) for server state with infinite stale time and disabled refetching. Local component state managed with React hooks. Form handling uses React Hook Form with Zod resolvers for validation.

### Backend Architecture

**Server Framework**: Express.js running on Node.js with ESM module system. The server implements request logging middleware that captures API calls, response times, and truncates long log lines.

**API Structure**: RESTful API with routes prefixed with `/api`. Currently implements a minimal route structure with placeholder for CRUD operations. Error handling middleware catches and formats errors with appropriate HTTP status codes.

**Development vs Production**: Vite integration in development mode with HMR support. Production builds serve static assets from `dist/public`. The server setup includes runtime error overlays and development banners in Replit environment.

### Data Storage Solutions

**ORM**: Drizzle ORM configured for PostgreSQL with Neon serverless driver. Schema migrations stored in `./migrations` directory. Database connection via `DATABASE_URL` environment variable.

**Schema Design**: Currently implements a simple user table with UUID primary keys (generated via `gen_random_uuid()`), username (unique), and password fields. Zod schemas generated via drizzle-zod for type-safe validation.

**Storage Abstraction**: Implements an `IStorage` interface pattern with in-memory implementation (`MemStorage`) for development. Supports user CRUD operations (getUser, getUserByUsername, createUser). Production would swap to a database-backed implementation.

### Authentication & Authorization

**Current State**: Basic user schema exists but no authentication middleware is implemented. The schema includes username/password fields suggesting planned credential-based auth.

**Session Management**: Package includes `connect-pg-simple` for PostgreSQL-backed session storage, though not yet wired up in the codebase.

### Design System & Branding

**Color System**: 
- Primary palette: Black (#000000), White (#FFFFFF), Accent Yellow (#f7e74f / HSL 54 93% 64%)
- Dark mode support with custom backgrounds (7% and 12% lightness)
- Semantic color tokens for UI states (primary, secondary, destructive, muted, accent)

**Typography Scale**: 
- Section titles: 32-40px (area-extended, bold)
- Questions: 20-24px (area-extended, medium)
- Body: 16px (area-normal)
- Labels: 14px (area-normal)

**Layout Patterns**: 
- Questionnaire: `max-w-3xl` centered containers
- Results: `max-w-5xl` for multi-column layouts
- Consistent padding scale (p-8 to p-12 for cards)
- Vertical progression with fade transitions

### Standalone Assessment Application

**Location**: `/public/assessment/index.html` (accessible at `/assessment/`)
**Technology**: Pure HTML5/CSS3/JavaScript (no frameworks)
**File Size**: ~75KB (single self-contained file)

**Questionnaire Structure**: 
- Context questions: Company size, lead volume, challenges
- Reach phase: 35 points max (customer acquisition, targeting, channels)
- Relate phase: 32 points max (content, messaging, engagement)
- Respond phase: 27 points max (lead nurturing, conversion, sales alignment)
- Retain phase: 27 points max (customer retention, loyalty, advocacy)
- Total: 121 points maximum

**Scoring System**: 
- Automated calculation across all 4 phases
- Percentage-based maturity levels (0-20%, 21-40%, 41-60%, 61-80%, 81-100%)
- Phase-specific interpretations and recommendations
- Visual radar chart using Canvas API

**Data Handling**: 
- **localStorage**: All submissions automatically saved with timestamp
- **Console Logging**: Full data object logged for debugging
- **Optional Webhook**: POST to configured URL (Make.com, Zapier, n8n compatible)
- **Graceful Degradation**: Works perfectly without webhook configuration

**Typography**: 
- Uses Work Sans (Google Fonts) - loads instantly, no configuration needed
- Fallback to system fonts for maximum compatibility

**WordPress Integration**:
- Maximum 4 files (index.html, README.md, QUICK_START.md, WEBHOOK_EXAMPLE.json)
- Copy-paste ready for WordPress pages or iframe embedding
- No dependencies on server-side code

## External Dependencies

### Third-Party UI Libraries
- **Radix UI**: Complete suite of headless UI primitives (@radix-ui/react-*) for accessible components
- **shadcn/ui**: Pre-built component library configured with "new-york" style variant
- **Lucide React**: Icon library for UI elements
- **cmdk**: Command menu component
- **Embla Carousel**: Carousel/slider functionality
- **Vaul**: Drawer component library

### Development & Build Tools
- **Vite**: Frontend build tool and dev server with React plugin
- **TypeScript**: Type safety across client and server
- **Tailwind CSS**: Utility-first CSS framework with PostCSS
- **ESBuild**: Used for server-side bundling in production builds
- **Drizzle Kit**: Database schema management and migrations

### Data & State Management
- **TanStack Query (React Query)**: Server state management and caching
- **React Hook Form**: Form state management
- **Zod**: Schema validation library
- **Drizzle Zod**: Integration between Drizzle ORM and Zod validation

### Database & Backend
- **Neon Database**: Serverless PostgreSQL (@neondatabase/serverless)
- **Drizzle ORM**: Type-safe database queries
- **Express.js**: Web server framework

### Styling & Utilities
- **Tailwind Merge**: Utility for merging Tailwind classes
- **class-variance-authority (CVA)**: Type-safe component variants
- **clsx**: Conditional className utility
- **date-fns**: Date manipulation library

### WordPress Integration
The standalone assessment (`public/assessment/index.html`) is designed for WordPress embedding via iframe or direct HTML insertion. Webhook URLs can be configured for Make.com, Zapier, n8n, or Webhook.site for data collection.

### Replit-Specific Integrations
- Development runtime error modal (@replit/vite-plugin-runtime-error-modal)
- Code cartographer for development (@replit/vite-plugin-cartographer)
- Development banner plugin (@replit/vite-plugin-dev-banner)