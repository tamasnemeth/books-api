<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Entities\Author;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BookController $bookController
    ) {}

    /**
     * GET /api/authors
     * Returns a list of all authors.
     */
    public function index()
    {
        $authors = $this->entityManager->getRepository(Author::class)->findAll();

        return response()->json(
            array_map(fn($author) => $this->formatAuthor($author), $authors)
        );
    }

    /**
     * POST /api/authors
     * Creates a new author.
     */
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

    /**
     * GET /api/authors/{id}
     * Returns details of a specific author.
     */
    public function show(int $id)
    {
        $author = $this->entityManager->find(Author::class, $id);

        if (!$author) {
            return response()->json(['message' => 'Author not found'], 404);
        }

        return response()->json($this->formatAuthor($author));
    }

    /**
     * PUT /api/authors/{id}
     * Updates an existing author.
     */
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

    /**
     * DELETE /api/authors/{id}
     * Deletes an author.
     */
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

    /**
     * Formats an Author entity into an array.
     */
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
