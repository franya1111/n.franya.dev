# ===== Stage 1: Dependencies =====
FROM node:20-alpine AS deps

WORKDIR /app

# Install bun for faster installs (consistent with project setup)
RUN npm install -g bun

# Copy package manifests
COPY package.json bun.lock* ./

# Install dependencies (frozen lockfile for reproducibility)
RUN bun install --frozen-lockfile

# ===== Stage 2: Builder =====
FROM node:20-alpine AS builder

WORKDIR /app

RUN npm install -g bun

COPY --from=deps /app/node_modules ./node_modules
COPY . .

# Build the Next.js standalone output
# Note: .env is needed for build-time env vars (e.g. DATABASE_URL)
ENV NEXT_TELEMETRY_DISABLED=1
RUN bun run build

# ===== Stage 3: Runner =====
FROM node:20-alpine AS runner

WORKDIR /app

ENV NODE_ENV=production
ENV NEXT_TELEMETRY_DISABLED=1
ENV PORT=3000
ENV HOSTNAME=0.0.0.0

# Create non-root user for security
RUN addgroup --system --gid 1001 nodejs \
 && adduser --system --uid 1001 nextjs

# Copy standalone server, static assets and public folder
COPY --from=builder --chown=nextjs:nodejs /app/.next/standalone ./
COPY --from=builder --chown=nextjs:nodejs /app/.next/static ./.next/static
COPY --from=builder --chown=nextjs:nodejs /app/public ./public

# Create data directory for SQLite (so we can mount a volume)
RUN mkdir -p /app/data && chown -R nextjs:nodejs /app/data

USER nextjs

EXPOSE 3000

CMD ["node", "server.js"]
