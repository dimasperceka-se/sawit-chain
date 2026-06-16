# Palm Oil Digital Twin — single-image deploy (API + client on one port)
FROM node:20-alpine

WORKDIR /app

# Install deps (build needs dev + runtime deps)
COPY package*.json ./
RUN npm install

# App source
COPY . .

# Build the client bundle into dist/public
RUN npm run build

ENV NODE_ENV=production
ENV PORT=5003
EXPOSE 5003

# Server runs via tsx; serves dist/public + /api
CMD ["npm", "start"]
