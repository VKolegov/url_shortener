<?php

namespace App\Http\Controllers;

use App\Helpers\SlugGenerator;
use App\Http\Requests\CreateShortUrlRequest;
use App\Http\Requests\PaginatedRequest;
use App\Http\Requests\UpdateShortUrlRequest;
use App\Http\Resources\ShortUrlResource;
use App\Http\Resources\ShortUrlsCollection;
use App\Repositories\ShortUrlRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShortUrlsController extends Controller
{
    private ShortUrlRepository $repository;

    public function __construct()
    {
        $this->repository = new ShortUrlRepository();
    }

    public function index(PaginatedRequest $request): ShortUrlsCollection
    {
        $paginated = $this->repository->getPaginated(
            page: $request->get('page', 1),
            itemsPerPage: $request->get('perPage', 20),
        );


        return new ShortUrlsCollection($paginated);
    }

    public function show(int $id): ShortUrlResource
    {
        $model = $this->repository->getModelById($id);

        if (!$model) {
            abort(404);
        }

        return new ShortUrlResource($model);
    }

    public function create(CreateShortUrlRequest $request): JsonResponse
    {
        $data = $request->validated();

        $model = $this->repository->create(
            url: $data['destination_url'],
            slug: $data['slug'] ?? null,
            name: $data['name'] ?? null,
        );

        return (new ShortUrlResource($model))
            ->response()
            ->setStatusCode(201);
    }

    public function update(int $id, UpdateShortUrlRequest $request): ShortUrlResource
    {
        $model = $this->repository->updateModelById(
            id: $id,
            attributes: $request->validated(),
        );

        if (!$model) {
            abort(404);
        }

        return new ShortUrlResource($model);
    }

    public function getFreeSlug(Request $r): string
    {
        $r->validate([
            'length' => ['int', 'min:3', 'max:16']
        ]);

        return (new SlugGenerator(
            $r->get('length', 3)
        ))->generate();
    }
}
