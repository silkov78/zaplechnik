FROM node:18-alpine

WORKDIR /app

# Copy package files first for better caching
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy application code
COPY . .

# Expose port (if your app uses one)
EXPOSE 3000

# Start the application
CMD ["npm", "start"]