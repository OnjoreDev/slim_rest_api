<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;
use App\Repositories\ProductRepository;
use Slim\Exception\HttpNotFoundException;

/**
 * Middleware to fetch a product by ID from the route arguments
 * and inject it into the request attributes.
 */
class GetProduct
{
    /**
     * Inject the ProductRepository via Constructor Property Promotion.
     */
    public function __construct(private ProductRepository $repository) {}

    /**
     * Execute the middleware logic.
     *
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     * @throws HttpNotFoundException if the product does not exist.
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Extract route information (like arguments defined in the URL) from the request
        $context = RouteContext::fromRequest($request);
        $route = $context->getRoute();
        
        // Retrieve the 'id' parameter from the route pattern (e.g., /products/{id})
        $id = $route->getArgument('id');

        // Attempt to fetch the product. We cast $id to (int) as route args are strings by default.
        $product = $this->repository->getById((int)$id);

        // Standard Slim 4 pattern: If the resource isn't found, trigger a 404 exception.
        if ($product === false) {
            throw new HttpNotFoundException($request, message: 'product not found');
        }

        // Attach the product object to the request. 
        // This allows subsequent middleware or the final controller to access it via $request->getAttribute('product').
        $request = $request->withAttribute('product', $product);

        // Pass the request further down the middleware stack
        return $handler->handle($request);
    }
}