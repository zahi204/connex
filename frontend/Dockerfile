# syntax=docker/dockerfile:1.7

# ---------- Build stage ----------
FROM node:22-alpine AS builder

WORKDIR /app

# Use npm (lockfile: package-lock.json). pnpm-lock.yaml exists too but we stick to one.
COPY package.json package-lock.json* ./
RUN npm ci --no-audit --no-fund || npm install --no-audit --no-fund

COPY . .

# Nuxt build consumes NUXT_PUBLIC_* at build time only for some cases,
# but since apiBaseUrl uses runtimeConfig it's read at runtime — OK to leave unset here.
RUN npm run build

# ---------- Runtime stage ----------
FROM node:22-alpine AS runtime

WORKDIR /app

ENV NODE_ENV=production \
    HOST=0.0.0.0 \
    PORT=3000 \
    NITRO_HOST=0.0.0.0 \
    NITRO_PORT=3000

COPY --from=builder /app/.output ./.output

EXPOSE 3000

CMD ["node", ".output/server/index.mjs"]
