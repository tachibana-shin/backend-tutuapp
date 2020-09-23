<?php

require_once __DIR__."/../modules/ProductSimilarity.php";

    $products        = json_decode(file_get_contents('products-data.json'));

    $selectedId      = 8;

    $selectedProduct = $products[0];



    $selectedProducts = array_filter($products, function ($product) use ($selectedId) { return $product->id === $selectedId; });

    if (count($selectedProducts)) {

        $selectedProduct = $selectedProducts[array_keys($selectedProducts)[0]];

    }



    $productSimilarity = new ProductSimilarity($products);

    $similarityMatrix  = $productSimilarity->calculateSimilarityMatrix();

    $products          = $productSimilarity->getProductsSortedBySimularity($selectedId, $similarityMatrix);

var_dump($products);
 ?>