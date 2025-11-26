#!/bin/bash

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}    🔄 Database Reset Script${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

echo -e "${YELLOW}🗑️  Dropping database schema...${NC}"
docker exec -it books_app php artisan doctrine:schema:drop --force
echo -e "${GREEN}   ✓ Schema dropped${NC}"
echo ""

echo -e "${YELLOW}🏗️  Creating database schema...${NC}"
docker exec -it books_app php artisan doctrine:schema:create
echo -e "${GREEN}   ✓ Schema created${NC}"
echo ""

echo -e "${YELLOW}🌱 Seeding database...${NC}"
docker exec -it books_app php artisan db:seed
echo ""

echo -e "${GREEN}✅ Database reset completed!${NC}"
echo ""
