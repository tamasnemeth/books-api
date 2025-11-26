# Books Api

## Install

Setup and run docker container & seeding database

```bash
./setup.sh
```
    
On your development machine, add this to the `/etc/hosts` file (Linux/Mac) or `C:\Windows\System32\drivers\etc\hosts` (Windows):

```bash
127.0.0.1 books.local
```

Reset database
```bash
./reset.sh
```

Enter the container

```bash
docker exec -it books_app bash
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
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "George R.R. Martin"
  }'
```

Edit author

```bash
curl -X PUT http://books.local:8080/api/authors/1 \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "George R.R. Martin"
  }'
```

Get all author

```bash
curl -X GET http://books.local:8080/api/authors \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

Get author

```bash
curl -X GET http://books.local:8080/api/authors/1 \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

### Categories

Create new category

```bash
curl -X POST http://books.local:8080/api/categories \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Fantasy"
  }'
```

Edit category

```bash
curl -X PUT http://books.local:8080/api/categories/1 \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Thriller"
  }'
```

Delete category

```bash
curl -X DELETE http://books.local:8080/api/categories/1 \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

Get all categories

```bash
curl -X GET http://books.local:8080/api/categories \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

Get category

```bash
curl -X GET http://books.local:8080/api/categories/1 \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

### Books

Create book

```bash
curl -X POST http://books.local:8080/api/books \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
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
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Harry Potter és a Bölcsek Köve",
    "price_huf": 5490.00
  }'
```

Get all books

```bash
curl -X GET http://books.local:8080/api/books \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

Get a book

```bash
curl -X GET http://books.local:8080/api/books/1 \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

Delete book

```bash
curl -X DELETE http://books.local:8080/api/books/1 \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

Search

```bash
curl -X GET "http://books.local:8080/api/books/search?query=asimov" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```


### Statistics

Returns a list of books with prices above the average price.

```bash
curl -X GET http://books.local:8080/api/statistics/expensive-books \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```


Returns the names of the three most popular categories and their average prices.

```bash
curl -X GET http://books.local:8080/api/statistics/popular-categories \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```


Returns the top three most expensive books in the Fantasy and Science Fiction categories.

```bash
curl -X GET http://books.local:8080/api/statistics/top-fantasy-and-sci-fi \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```
