<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use Doctrine\ORM\EntityManagerInterface;

class StatisticsController extends Controller
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CurrencyService $currencyService
    ) {}

    /**
     * GET /api/statistics/expensive-books
     * Returns a list of books with prices above the average price.
     */
    public function expensiveBooks()
    {
        $query = $this->entityManager->createQuery(
            'SELECT b, a, c
             FROM App\Entities\Book b
             JOIN b.author a
             JOIN b.category c
             WHERE b.priceHuf > (
                 SELECT AVG(b2.priceHuf) FROM App\Entities\Book b2
             )
             ORDER BY b.priceHuf DESC'
        );

        $books = $query->getResult();

        $avgPriceQuery = $this->entityManager->createQuery(
            'SELECT AVG(b.priceHuf) as avgPrice FROM App\Entities\Book b'
        );
        $avgPrice = $avgPriceQuery->getSingleScalarResult();
        $avgPrices = $this->currencyService->formatPrice($avgPrice);

        return response()->json([
            'average_price' => [
                'huf' => $avgPrices['huf'],
                'eur' => $avgPrices['eur']
            ],
            'count' => count($books),
            'books' => array_map(fn($book) => $this->formatBook($book), $books)
        ]);
    }

    /**
     * GET /api/statistics/popular-categories
     * Returns the names of the three most popular categories and their average prices.
     */
    public function popularCategories()
    {
        $query = $this->entityManager->createQuery(
            'SELECT c.id, c.name, COUNT(b.id) as bookCount, AVG(b.priceHuf) as avgPrice
             FROM App\Entities\Category c
             JOIN App\Entities\Book b WITH b.category = c
             GROUP BY c.id, c.name
             ORDER BY bookCount DESC'
        );

        $query->setMaxResults(3);
        $results = $query->getResult();

        $categories = array_map(function($result) {
            $prices = $this->currencyService->formatPrice($result['avgPrice']);
            return [
                'id' => $result['id'],
                'name' => $result['name'],
                'book_count' => (int)$result['bookCount'],
                'average_price' => [
                    'huf' => $prices['huf'],
                    'eur' => $prices['eur']
                ]
            ];
        }, $results);

        return response()->json([
            'count' => count($categories),
            'categories' => $categories
        ]);
    }

    /**
     * GET /api/statistics/top-fantasy-and-sci-fi
     * Returns the top three most expensive books in the Fantasy and Science Fiction categories.
     */
    public function topFantasyAndSciFi()
    {
        $query = $this->entityManager->createQuery(
            'SELECT b, a, c
             FROM App\Entities\Book b
             JOIN b.author a
             JOIN b.category c
             WHERE c.name LIKE :fantasy OR c.name LIKE :scifi
             ORDER BY b.priceHuf DESC'
        );

        $query->setParameter('fantasy', '%Fantasy%');
        $query->setParameter('scifi', '%Science Fiction%');
        $query->setMaxResults(3);

        $books = $query->getResult();

        return response()->json([
            'count' => count($books),
            'books' => array_map(fn($book) => $this->formatBookSmall($book), $books)
        ]);
    }

    private function formatBookSmall($book): array
    {
        $prices = $this->currencyService->formatPrice($book->getPriceHuf());
        return [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'price' => [
                'huf' => $prices['huf'],
                'eur' => $prices['eur']
            ],
        ];
    }

    private function formatBook($book): array
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
