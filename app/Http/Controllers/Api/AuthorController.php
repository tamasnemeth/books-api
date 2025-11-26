<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// use App\Entities\Book;
use App\Entities\Author;
// use App\Entities\Category;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
// use DateTime;

class AuthorController extends Controller
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BookController $bookController
    ) {}

    public function index()
    {
        $authors = $this->entityManager->getRepository(Author::class)->findAll();

        return response()->json(
            array_map(fn($author) => $this->formatAuthor($author), $authors)
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $author = new Author();
        $author->setName($request->name);

        $this->entityManager->persist($author);
        $this->entityManager->flush();

        return response()->json($this->formatAuthor($author), 201);
    }

    public function show(int $id)
    {
        $author = $this->entityManager->find(Author::class, $id);

        if (!$author) {
            return response()->json(['message' => 'Author not found'], 404);
        }

        return response()->json($this->formatAuthor($author));
    }

    public function update(Request $request, int $id)
    {
        $author = $this->entityManager->find(Author::class, $id);

        if (!$author) {
            return response()->json(['message' => 'Author not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        if ($request->has('name')) {
            $author->setName($request->name);
        }
        $this->entityManager->flush();

        return response()->json($this->formatAuthor($author));
    }

    public function destroy(int $id)
    {
        $author = $this->entityManager->find(Author::class, $id);

        if (!$author) {
            return response()->json(['message' => 'Author not found'], 404);
        }

        $this->entityManager->remove($author);
        $this->entityManager->flush();

        return response()->json(['message' => 'Author deleted successfully']);
    }

    private function formatAuthor(Author $author): array
    {
        return [
            'id' => $author->getId(),
            'name' => $author->getName(),
            'books' => array_map(fn($book) => $this->bookController->formatBook($book), $author->getBooks()->toArray()),
            'created_at' => $author->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $author->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
