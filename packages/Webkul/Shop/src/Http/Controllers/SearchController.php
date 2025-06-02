<?php

namespace Webkul\Shop\Http\Controllers;

use Webkul\Product\Repositories\ProductRepository;
use Webkul\Category\Repositories\CategoryRepository;

class SearchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @param  \Webkul\Category\Repositories\CategoryRepository  $categoryRepository,
     * @return void
     */
    public function __construct(protected productRepository $productRepository, protected CategoryRepository $categoryRepository,)
    {
        parent::__construct();
    }

    /**
     * Index to handle the view loaded with the search results
     * 
     * @return \Illuminate\View\View 
     */
    public function index()
    {
        // sandeep || add search logic
        $results = collect();
        $errorMessage = null;
        $searchTerm = request()->input('term', '');

        if (!empty($searchTerm)) {
            if (strlen($searchTerm) >= 3) {
                request()->query->add([
                    'name'  => $searchTerm,
                    'sort'  => 'created_at',
                    'order' => 'desc',
                ]);
                
                $results = $this->productRepository->getAll();
            } else {
                $errorMessage = 'Search term must be at least 3 characters long.';
            }
        } else {
            $results = $this->productRepository->getAll();
        }

        $categories = $this->categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);
        $categories = $categories->take(3);
        // return view($this->_config['view'])->with('results', $results->count() ? $results : null);
        return view($this->_config['view'])->with([
            'results' => $results->count() ? $results : null,
            'errorMessage' => $errorMessage,
            'categories' => $categories,
        ]);
    }
}

