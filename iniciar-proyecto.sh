#!/bin/bash

# LEVANTAR BACKEND
# Arranca Laravel Sail en segundo plano.
cd ~/Proyecto/backend
./vendor/bin/sail up -d

# LEVANTAR FRONTEND
# Arranca Vue/Vite.
cd ~/Proyecto/frontend
npm run dev
