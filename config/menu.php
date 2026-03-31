<?php

return [
    [
        "title" => "Dashboard",
        "icon" => "fa-solid fa-house",
        "name" => "dashboard",
        "children" => [],
    ],
    [
        "title" => "Quotation",
        "image" => "/images/svg/quotation-black.svg",
        "children" => [
            [
                "title" => "Recently",
                "name" => "quotation.recently.index",
                "query" => [],
                "children" => [],
            ],
            [
                "title" => "History",
                "name" => "quotation.history.index",
                "query" => [],
                "children" => [],
            ],
        ],
    ],
    [
        "title" => "Purchase",
        "icon" => "fa-solid fa-cart-shopping",
        "children" => [
            [
                "title" => "Recently",
                "name" => "purchase.recently.index",
                "query" => [],
                "children" => [],
            ],
            [
                "title" => "SPO",
                "name" => "purchase.spo.index",
                "query" => [],
                "children" => [],
            ],
            [
                "title" => "History",
                "name" => "purchase.history.index",
                "query" => [],
                "children" => [],
            ],
        ],
    ],
    [
        "title" => "Purchase Template",
        "icon" => "fa-solid fa-file-invoice",
        "children" => [
            [
                "title" => "Template",
                "name" => "purchase-template.index",
                "query" => [],
                "children" => [],
            ],
            [
                "title" => "Template Mapping",
                "name" => "purchase-template.mapping.index",
                "query" => [],
                "children" => [],
            ],
        ],
    ],
    [
        "title" => "Stock Purchase",
        "icon" => "fa-solid fa-cart-shopping",
        "name" => "stock-purchase.index",
        "children" => [],
    ],
];
