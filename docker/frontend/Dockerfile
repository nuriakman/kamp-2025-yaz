# Node.js taban imajı
FROM node:20

# Çalışma dizinini ayarla
WORKDIR /app

# Uygulama bağımlılıklarını yükle
COPY package*.json ./
RUN npm install

# Uygulama kodunu kopyala
COPY . .

# Uygulamayı başlat
CMD ["npm", "run", "dev"]
