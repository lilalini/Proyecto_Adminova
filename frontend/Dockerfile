# ── Build stage ─────────────────────────────────────────────────────────
FROM node:20-alpine AS builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

# Build de producción apuntando al backend en Docker
RUN npm run build -- --configuration=production

# ── Serve stage ──────────────────────────────────────────────────────────
FROM nginx:alpine

COPY --from=builder /app/dist/frontend/browser /usr/share/nginx/html

COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
