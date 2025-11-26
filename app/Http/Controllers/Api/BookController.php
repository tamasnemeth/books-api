<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Entities\Book;
use App\Entities\Author;
use App\Entities\Category;
use App\Services\CurrencyService;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use DateTime;

class BookController extends Controller
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CurrencyService $currencyService
    ) {}

    public function index()
    {
        $books = $this->entityManager->getRepository(Book::class)->findAll();

        return response()->json(
            array_map(fn($book) => $this->formatBook($book), $books)
        );
    }

    public function store(Request $request)
    {
        logger('=== BOOK STORE START ===');
        logger('Request data:', $request->all());
        logger('Headers:', $request->headers->all());
        $request->validate([
            'title' => 'required|string|max:255',
            'author_id' => 'required|integer|exists:authors,id',
            'category_id' => 'required|integer|exists:categories,id',
            'release_date' => 'required|date',
            'price_huf' => 'required|numeric|min:0',
        ]);
        logger('Validation passed in BookController@store');

        $author = $this->entityManager->find(Author::class, $request->author_id);
        $category = $this->entityManager->find(Category::class, $request->category_id);
        logger('Author and Category found', ['author_id' => $request->author_id, 'category_id' => $request->category_id]);

        $book = new Book();
        $book->setTitle($request->title);
        $book->setAuthor($author);
        $book->setCategory($category);
        $book->setReleaseDate(new DateTime($request->release_date));
        $book->setPriceHuf($request->price_huf);

        logger('Book entity populated, saving to database...');

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        logger('Book saved to database with ID: ' . $book->getId());

        return response()->json($this->formatBook($book), 201);
    }

    public function show(int $id)
    {
        $book = $this->entityManager->find(Book::class, $id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        return response()->json($this->formatBook($book));
    }

    public function update(Request $request, int $id)
    {
        $book = $this->entityManager->find(Book::class, $id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'author_id' => 'sometimes|required|integer|exists:authors,id',
            'category_id' => 'sometimes|required|integer|exists:categories,id',
            'release_date' => 'sometimes|required|date',
            'price_huf' => 'sometimes|required|numeric|min:0',
        ]);

        if ($request->has('title')) {
            $book->setTitle($request->title);
        }

        if ($request->has('author_id')) {
            $author = $this->entityManager->find(Author::class, $request->author_id);
            $book->setAuthor($author);
        }

        if ($request->has('category_id')) {
            $category = $this->entityManager->find(Category::class, $request->category_id);
            $book->setCategory($category);
        }

        if ($request->has('release_date')) {
            $book->setReleaseDate(new DateTime($request->release_date));
        }

        if ($request->has('price_huf')) {
            $book->setPriceHuf($request->price_huf);
        }

        $this->entityManager->flush();

        return response()->json($this->formatBook($book));
    }

    public function destroy(int $id)
    {
        $book = $this->entityManager->find(Book::class, $id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return response()->json(['message' => 'Book deleted successfully']);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([
                'message' => 'Query parameter is required',
                'data' => []
            ], 400);
        }

        $dql = 'SELECT b, a, c
                FROM App\Entities\Book b
                JOIN b.author a
                JOIN b.category c
                WHERE b.title LIKE :query
                   OR a.name LIKE :query
                   OR c.name LIKE :query
                ORDER BY b.title ASC';

        $queryObj = $this->entityManager->createQuery($dql);
        $queryObj->setParameter('query', '%' . $query . '%');

        $books = $queryObj->getResult();

        return response()->json([
            'query' => $query,
            'count' => count($books),
            'data' => array_map(fn($book) => $this->formatBook($book), $books)
        ]);
    }


    public function formatBook(Book $book): array
    {
        $prices = $this->currencyService->formatPrice($book->getPriceHuf());

        return [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'author' => [
                'id' => $book->getAuthor()->getId(),
                'name' => $book->getAuthor()->getName(),
            ],
            'category' => [
                'id' => $book->getCategory()->getId(),
                'name' => $book->getCategory()->getName(),
            ],
            'release_date' => $book->getReleaseDate()->format('Y-m-d'),
            'price' => [
                'huf' => $prices['huf'],
                'eur' => $prices['eur']
            ],
            'created_at' => $book->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $book->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
