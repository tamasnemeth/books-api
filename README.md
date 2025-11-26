# Books Api

## Install

Create Docker container

```bash
docker-compose up -d      
```
    
Enter the container

```bash
docker exec -it books_app bash
```

Create database tables
```bash
php artisan doctrine:schema:create
```


## Testing api endpoints

### User endpoints

Register new user

```bash
curl -X POST http://books.local:8080/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

Login user

```bash
curl -X POST http://books.local:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### Authors

Create new author

```bash
curl -X POST http://books.local:8080/api/authors \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "George R.R. Martin"
  }'
```

Edit author

```bash
curl -X PUT http://books.local:8080/api/authors/1 \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "George R.R. Martin"
  }'
```

Get all author

```bash
curl -X GET http://books.local:8080/api/authors \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA"
```

Get author

```bash
curl -X GET http://books.local:8080/api/authors/1 \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA"
```

### Categories

Create new category

```bash
curl -X POST http://books.local:8080/api/categories \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Fantasy"
  }'
```

Edit category

```bash
curl -X PUT http://books.local:8080/api/categories/1 \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Thriller"
  }'
```

Delete category

```bash
curl -X DELETE http://books.local:8080/api/categories/1 \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA"
```

Get all categories

```bash
curl -X GET http://books.local:8080/api/categories \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA"
```

Get category

```bash
curl -X GET http://books.local:8080/api/categories/1 \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA"
```

### Books

Create book

```bash
curl -X POST http://books.local:8080/api/books \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Book",
    "author_id": 1,
    "category_id": 1,
    "release_date": "2024-01-15",
    "price_huf": 5990
  }'
```

Update a book
```bash
curl -X PUT http://books.local:8080/api/books/1 \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Harry Potter és a Bölcsek Köve",
    "price_huf": 5490.00
  }'
```

Get all books

```bash
curl -X GET http://books.local:8080/api/books \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA"
```

Get a book

```bash
curl -X GET http://books.local:8080/api/books/1 \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA"
```

Delete book

```bash
curl -X DELETE http://books.local:8080/api/books/1 \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA"
```

Search

```bash
curl -X GET "http://books.local:8080/api/books/search?query=asimov" \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA"
```


### Statistics

Returns a list of books with prices above the average price.

```bash
curl -X GET http://books.local:8080/api/statistics/expensive-books \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA"
```


Returns the names of the three most popular categories and their average prices.

```bash
curl -X GET http://books.local:8080/api/statistics/popular-categories \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA"
```


Returns the top three most expensive books in the Fantasy and Science Fiction categories.

```bash
curl -X GET http://books.local:8080/api/statistics/top-fantasy-and-sci-fi \
  -H "Authorization: Bearer rQquCPLyPLXLunDZBqYbrI4uxtqqWwqF9RzwlsQITQ9AnGe2O6UPlMWWfuxE2pbR4RRrCoy9mpuLRXuA"
```
