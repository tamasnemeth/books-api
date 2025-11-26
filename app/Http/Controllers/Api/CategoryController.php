<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Entities\Category;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BookController $bookController
    ) {}

    /**
     * GET /api/categories
     * Returns a list of all categories.
     */
    public function index()
    {
        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        return response()->json(
            array_map(fn($category) => $this->formatCategory($category), $categories)
        );
    }

    /**
     * POST /api/categories
     * Creates a new category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = new Category();
        $category->setName($request->name);
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return response()->json($this->formatCategory($category), 201);
    }

    /**
     * GET /api/categories/{id}
     * Returns details of a specific category.
     */
    public function show(int $id)
    {
        $category = $this->entityManager->find(Category::class, $id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($this->formatCategory($category));
    }

    /**
     * PUT /api/categories/{id}
     * Updates an existing category.
     */
    public function update(Request $request, int $id)
    {
        $category = $this->entityManager->find(Category::class, $id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        if ($request->has('name')) {
            $category->setName($request->name);
        }
        $this->entityManager->flush();

        return response()->json($this->formatCategory($category));
    }

    /**
     * DELETE /api/categories/{id}
     * Deletes a specific category.
     */
    public function destroy(int $id)
    {
        $category = $this->entityManager->find(Category::class, $id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return response()->json(['message' => 'Category deleted successfully']);
    }

    /**
     * Formats a Category entity into an array.
     */
    private function formatCategory(Category $category): array
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'books' => array_map(fn($book) => $this->bookController->formatBook($book), $category->getBooks()->toArray()),
            'created_at' => $category->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $category->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
