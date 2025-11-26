#!/bin/bash

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}    📚 Books API - Complete Setup Script${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# 0. .env file check
if [ ! -f .env ]; then
    echo -e "${YELLOW}📄 Creating .env file from .env.example...${NC}"
    if [ -f .env.example ]; then
        cp .env.example .env
        echo -e "${GREEN}   ✓ .env file created${NC}"
    else
        echo -e "${RED}   ✗ .env.example not found${NC}"
        echo -e "${YELLOW}   Please create .env file manually${NC}"
        exit 1
    fi
    echo ""
fi

# 1. Start Docker containers
echo -e "${YELLOW}🐳 Starting Docker containers...${NC}"
docker-compose up -d
if [ $? -eq 0 ]; then
    echo -e "${GREEN}   ✓ Containers started${NC}"
else
    echo -e "${RED}   ✗ Failed to start containers${NC}"
    exit 1
fi
echo ""

# Waiting for MySQL to be ready
echo -e "${YELLOW}⏳ Waiting for MySQL to be ready...${NC}"
MYSQL_READY=0
COUNTER=0
MAX_TRIES=30

while [ $MYSQL_READY -eq 0 ] && [ $COUNTER -lt $MAX_TRIES ]; do
    docker exec books_db mysql -u books_user -pbooks_pass -e "SELECT 1" books > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        MYSQL_READY=1
        echo -e "${GREEN}   ✓ MySQL ready${NC}"
    else
        echo -e "   Waiting... (${COUNTER}/${MAX_TRIES})"
        sleep 2
        COUNTER=$((COUNTER+1))
    fi
done

if [ $MYSQL_READY -eq 0 ]; then
    echo -e "${RED}   ✗ MySQL failed to start${NC}"
    exit 1
fi
echo ""

# 2. Installing Composer dependencies (if needed)
echo -e "${YELLOW}📦 Checking composer dependencies...${NC}"
docker exec -it books_app bash -c "[ -d vendor ] || composer install --no-interaction"
echo -e "${GREEN}   ✓ Dependencies ready${NC}"
echo ""

# 3. Generating Laravel application key (if not exists)
echo -e "${YELLOW}🔑 Generating application key...${NC}"
docker exec -it books_app php artisan key:generate --force
echo -e "${GREEN}   ✓ Application key generated${NC}"
echo ""

# 4. Clearing config cache
echo -e "${YELLOW}🗑️  Clearing cache...${NC}"
docker exec -it books_app php artisan config:clear
echo -e "${GREEN}   ✓ Cache cleared${NC}"
echo ""

# 5. Dropping existing database schema (if exists)
echo -e "${YELLOW}🗄️  Dropping existing database schema...${NC}"
docker exec -it books_app php artisan doctrine:schema:drop --force 2>/dev/null || true
echo -e "${GREEN}   ✓ Old schema dropped${NC}"
echo ""

# 6. Creating database schema
echo -e "${YELLOW}🏗️  Creating database schema...${NC}"
docker exec -it books_app php artisan doctrine:schema:create
if [ $? -eq 0 ]; then
    echo -e "${GREEN}   ✓ Database schema created${NC}"
else
    echo -e "${RED}   ✗ Failed to create schema${NC}"
    exit 1
fi
echo ""

# 7. Seeding database with test data
echo -e "${YELLOW}🌱 Seeding database with test data...${NC}"
docker exec -it books_app php artisan db:seed
echo ""

# 8. Final summary
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✅ Setup completed successfully!${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${BLUE}🌐 Application URLs:${NC}"
echo -e "   Web:  ${YELLOW}http://books.local:8080${NC}"
echo -e "   API:  ${YELLOW}http://books.local:8080/api${NC}"
echo ""
echo -e "${BLUE}📊 Database:${NC}"
echo -e "   Host: ${YELLOW}127.0.0.1:3307${NC}"
echo -e "   Name: ${YELLOW}books${NC}"
echo -e "   User: ${YELLOW}books_user${NC}"
echo -e "   Pass: ${YELLOW}books_pass${NC}"
echo ""
echo -e "${BLUE}📝 Useful commands:${NC}"
echo -e "   View logs:       ${YELLOW}docker-compose logs -f${NC}"
echo -e "   Enter container: ${YELLOW}docker exec -it books_app bash${NC}"
echo -e "   Stop containers: ${YELLOW}docker-compose down${NC}"
echo -e "   Re-run setup:    ${YELLOW}./setup.sh${NC}"
echo ""
