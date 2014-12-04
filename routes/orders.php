<?php

$app->get("/product/search", function() use($app)
{
   $app->render("search_product.html.twig"); 
})->name('Productsearch');