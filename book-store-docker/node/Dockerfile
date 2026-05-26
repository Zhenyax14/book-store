# book-store-docker/node/Dockerfile

FROM node:20-alpine

WORKDIR /app

# Копируем только package.json для кэширования слоя зависимостей
# Если package.json не изменился — npm ci не будет запускаться повторно
COPY package*.json ./

RUN npm ci

# Открываем порт Vite dev server
EXPOSE 5173

# --host 0.0.0.0 обязателен — без него Vite слушает только
# localhost внутри контейнера и недоступен снаружи
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]