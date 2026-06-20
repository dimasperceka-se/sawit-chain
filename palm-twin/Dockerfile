# Palm Oil Digital Twin — single-image deploy (API + client on one port)
FROM node:20-alpine

WORKDIR /app

# Install deps (build needs dev + runtime deps)
COPY package*.json ./
RUN npm install

# App source
COPY . .

# Sub-path to serve under (default "/"). Pass --build-arg APP_BASE=/twin/ to
# serve behind a reverse-proxy sub-path; vite.config.ts reads process.env.APP_BASE.
ARG APP_BASE=/
ENV APP_BASE=${APP_BASE}

# Build the client bundle into dist/public
RUN npm run build

ENV NODE_ENV=production
ENV PORT=5003
EXPOSE 5003

# Server runs via tsx; serves dist/public + /api
CMD ["npm", "start"]
