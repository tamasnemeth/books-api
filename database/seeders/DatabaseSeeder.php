<?php

namespace Database\Seeders;

use App\Entities\User;
use App\Entities\Author;
use App\Entities\Category;
use App\Entities\Book;
use App\Services\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use DateTime;

class DatabaseSeeder extends Seeder
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TokenService $tokenService
    ) {}

    public function run(): void
    {
        echo "🌱 Seeding database...\n\n";

        // 1. Create user
        echo "👤 Creating user...\n";
        $user = new User();
        $user->setName('Test User');
        $user->setEmail('test@example.com');
        $user->setPassword(Hash::make('password123'));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        echo "   ✓ User created: test@example.com / password123\n\n";

        // 2. Generating access token
        echo "🔑 Generating access token...\n";
        $token = $this->tokenService->createToken($user, 'seeder_token');
        echo "   ✓ Token: {$token}\n\n";

        // 3. Creating authors
        echo "✍️  Creating authors...\n";
        $authors = [
            'J.K. Rowling',
            'George R.R. Martin',
            'J.R.R. Tolkien',
            'Isaac Asimov',
            'Frank Herbert',
            'Brandon Sanderson'
        ];

        $authorEntities = [];
        foreach ($authors as $authorName) {
            $author = new Author();
            $author->setName($authorName);
            $this->entityManager->persist($author);
            $authorEntities[] = $author;
        }
        $this->entityManager->flush();
        echo "   ✓ Created " . count($authors) . " authors\n\n";

        // 4. Creating categories...
        echo "📚 Creating categories...\n";
        $categories = [
            'Fantasy',
            'Science Fiction',
            'Adventure',
            'Mystery',
            'Thriller'
        ];

        $categoryEntities = [];
        foreach ($categories as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $this->entityManager->persist($category);
            $categoryEntities[] = $category;
        }
        $this->entityManager->flush();
        echo "   ✓ Created " . count($categories) . " categories\n\n";

        // 5. Creating books...
        echo "📖 Creating books...\n";
        $books = [
            [
                'title' => 'Harry Potter and the Philosophers Stone',
                'author' => 0, // J.K. Rowling
                'category' => 0, // Fantasy
                'release_date' => '1997-06-26',
                'price' => 4990
            ],
            [
                'title' => 'Harry Potter and the Chamber of Secrets',
                'author' => 0,
                'category' => 0,
                'release_date' => '1998-07-02',
                'price' => 5490
            ],
            [
                'title' => 'A Game of Thrones',
                'author' => 1, // George R.R. Martin
                'category' => 0,
                'release_date' => '1996-08-06',
                'price' => 6990
            ],
            [
                'title' => 'The Hobbit',
                'author' => 2, // J.R.R. Tolkien
                'category' => 0,
                'release_date' => '1937-09-21',
                'price' => 3990
            ],
            [
                'title' => 'The Lord of the Rings',
                'author' => 2,
                'category' => 0,
                'release_date' => '1954-07-29',
                'price' => 7990
            ],
            [
                'title' => 'Foundation',
                'author' => 3, // Isaac Asimov
                'category' => 1, // Science Fiction
                'release_date' => '1951-06-01',
                'price' => 6490
            ],
            [
                'title' => 'Dune',
                'author' => 4, // Frank Herbert
                'category' => 1,
                'release_date' => '1965-08-01',
                'price' => 7990
            ],
            [
                'title' => 'The Way of Kings',
                'author' => 5, // Brandon Sanderson
                'category' => 0,
                'release_date' => '2010-08-31',
                'price' => 8990
            ],
            [
                'title' => 'Mistborn: The Final Empire',
                'author' => 5,
                'category' => 0,
                'release_date' => '2006-07-17',
                'price' => 5990
            ],
            [
                'title' => 'I, Robot',
                'author' => 3,
                'category' => 1,
                'release_date' => '1950-12-02',
                'price' => 4490
            ]
        ];

        foreach ($books as $bookData) {
            $book = new Book();
            $book->setTitle($bookData['title']);
            $book->setAuthor($authorEntities[$bookData['author']]);
            $book->setCategory($categoryEntities[$bookData['category']]);
            $book->setReleaseDate(new DateTime($bookData['release_date']));
            $book->setPriceHuf((string)$bookData['price']);
            $this->entityManager->persist($book);
        }
        $this->entityManager->flush();
        echo "   ✓ Created " . count($books) . " books\n\n";

        echo "✅ Database seeded successfully!\n\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📝 LOGIN CREDENTIALS:\n";
        echo "   Email:    test@example.com\n";
        echo "   Password: password123\n\n";
        echo "🔑 ACCESS TOKEN:\n";
        echo "   {$token}\n\n";
        echo "🧪 TEST API CALL:\n";
        echo "   curl -X GET http://books.local:8080/api/books \\\n";
        echo "     -H \"Authorization: Bearer {$token}\"\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    }
}
