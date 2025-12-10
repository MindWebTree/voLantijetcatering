<?php

namespace Webkul\RestApi\Http\Controllers\V1\Shop\Catalog;

use Illuminate\Http\Request;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\RestApi\Http\Resources\V1\Shop\Catalog\ProductResource;
use Illuminate\Support\Facades\Validator;

class ProductController extends CatalogController
{
    /**
     * Is resource authorized.
     *
     * @return bool
     */
    public function isAuthorized()
    {
        return false;
    }

    /**
     * Repository class name.
     *
     * @return string
     */
    public function repository()
    {
        return ProductRepository::class;
    }

    /**
     * Resource class name.
     *
     * @return string
     */
    public function resource()
    {
        return ProductResource::class;
    }

    /**
     * Returns a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function allResources(Request $request)
    {

        $results = $this->getRepositoryInstance()->getAll($request->input('category_id'));

        return $this->getResourceCollection($results);
    }

    /**
     * Returns product's additional information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function additionalInformation(Request $request, $id)
    {
        $resource = $this->getRepositoryInstance()->findOrFail($id);

        $additionalInformation = app(\Webkul\Product\Helpers\View::class)
            ->getAdditionalData($resource);

        return response([
            'data' => $additionalInformation,
        ]);
    }

    /**
     * Returns product's additional information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function configurableConfig(Request $request, $id)
    {
        $resource = $this->getRepositoryInstance()->findOrFail($id);

        $configurableConfig = app(\Webkul\Product\Helpers\ConfigurableOption::class)
            ->getConfigurationConfig($resource);

        return response([
            'data' => $configurableConfig,
        ]);
    }

    /**
     * Is product wishlisted.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function isWishlisted(Request $request, $productId)
    {
        $product = $this->getRepositoryInstance()->findOrFail($productId);

        $wishlistHelper = app(\Webkul\Customer\Helpers\Wishlist::class);

        return response([
            'data' => [
                'is_wishlisted' => $wishlistHelper->getWishlistProduct($product) ? true : false
            ],
        ]);
    }



    // sandeep add funtion for search page
    public function search_products(Request $request){

        //  $validator = Validator::make($request->all(),[
        //     'query' => 'required|min:3', 
        //  ]);

        //  if($validator->fails()){
        //     return response()->json([
        //         'success' => false,
        //         'errors' => $validator->errors(),
        //     ], 422);
        //  }


        $results = collect();
        $errorMessage = null;
        $searchTerm = request()->input('query', '');

        // $productRepository = app(ProductRepository::class);

        if (!empty($searchTerm)) {
            if (strlen($searchTerm) >= 3) {
                // request()->query->add([
                //     'name'  => $searchTerm,
                //     'sort'  => 'created_at',
                //     'order' => 'desc',
                // ]);

                $request->merge([
                    'name'  => $searchTerm,
                    'sort'  => 'created_at',
                    'order' => 'desc',
                ]);
    
                // Fetch results from the repository instance
                $getItems = $this->getRepositoryInstance()->getAll();
                $results =  $this->getResourceCollection($getItems);
                
                // $results =  $productRepository->getAll();
            } else {
                $errorMessage = 'Search term must be at least 3 characters long.';
            }
        } else {
            $getItems = $this->getRepositoryInstance()->getAll();
            $results =  $this->getResourceCollection($getItems);
        }

        return response()->json([
            'results' => $results->count() ? $results : null,
            'errorMessage' => $errorMessage,
        ]);
    }

}
