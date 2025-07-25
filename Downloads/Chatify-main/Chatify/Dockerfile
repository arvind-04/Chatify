# Multi-stage Dockerfile for Chatify app

# Stage 1: Build React frontend
FROM node:18-alpine AS frontend-build
WORKDIR /app/frontend
COPY frontend/package*.json ./
RUN npm ci --only=production
COPY frontend/ ./
RUN npm run build

# Stage 2: Setup Node.js backend
FROM node:18-alpine AS backend
WORKDIR /app
COPY server/package*.json ./
RUN npm ci --only=production
COPY server/ ./

# Stage 3: Final production image
FROM node:18-alpine
WORKDIR /app

# Install production dependencies
COPY server/package*.json ./
RUN npm ci --only=production && npm cache clean --force

# Copy backend code
COPY server/ ./

# Copy built frontend
COPY --from=frontend-build /app/frontend/build ./public

# Create non-root user
RUN addgroup -g 1001 -S nodejs
RUN adduser -S chatify -u 1001
USER chatify

# Expose port
EXPOSE 3000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD node -e "require('http').get('http://localhost:3000', (res) => { process.exit(res.statusCode === 200 ? 0 : 1) })"

# Start the application
CMD ["npm", "start"]