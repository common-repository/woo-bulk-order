jQuery(document).ready(function ($) {

    /**
     * Disable automatic cart fragment refresh on page load
     * Author: Aakash
     * Dt: 17/09/2024
     */
    $(document.body).off('wc_fragment_refresh');
    $(document.body).off('wc_fragments_loaded');

    $("#jsGrid").jsGrid({
        width: "100%",
        height: "auto",
        editing: false,
        sorting: true,
        paging: false,
        pageSize: 10,
        noDataContent: "<div style='display: flex; justify-content: center; align-items: center; height: 100%;'><img src='" + wbo_params.loader_image + "' alt='Loading...' style='width: 150px; height: auto;'></div>",
        data: [],
        fields: [
            {
                name: "image",
                title: "Image",
                itemTemplate: function (value) {
                    return $("<img>")
                        .attr("src", value)
                        .attr("width", "50px")
                        .on("click", function () {
                            $("#modalImage").attr("src", value);
                            $("#imageModal").fadeIn();
                        });
                }
            },
            { name: "name", title: "Name" },
            { name: "price", title: "Price", align: "right" },
            {
                name: "quantity",
                title: "Quantity",
                itemTemplate: function (value) {
                    return $("<input>").attr("type", "number").attr("value", value).attr("min", "1").addClass("quantity-input");
                }
            },
            {
                name: "action",
                title: "Action",
                itemTemplate: function (_, item) {
                    return $("<button>").text("Add to Cart").addClass("add-to-cart-button").data("product-id", item.id);
                }
            }
        ]
    });

    /**
     * Function to load products, optionally with a category filter
     * Author: Aakash
     * Dt: 17/09/2024
     */
    function loadProducts(categoryId = '') {
        $.ajax({
            url: wbo_params.ajax_url,
            method: 'GET',
            data: {
                action: 'wbo_get_products',
                category_id: categoryId
            },
            success: function (response) {
                if (response.success) {
                    $("#jsGrid").jsGrid("option", "data", response.data);
                } else {
                    console.log('Failed to load products');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    }

    /**
     * Load products on page load
     */
    loadProducts();

    /**
     * Handle category filter change
     * Author: Aakash
     * Dt: 17/09/2024
     */
    $('#category-filter').on('change', function () {
        var categoryId = $(this).val();
        loadProducts(categoryId);
    });

    /**
     * Use the formatted price directly 
     * Author: Aakash
     * Dt: 17/09/2024
     */
    $(document).on('change', '.product-variations', function () {
        var $select = $(this);
        var selectedPrice = $select.find('option:selected').data('price');
        var $row = $select.parent().parent();
        $row.find('.woocommerce-Price-amount').parent().html(selectedPrice);
    });

    /**
     * Handle Add to cart functionality , Update cart fragments only when a product is added to cart
     * Author: Aakash
     * Dt: 17/09/2024
     */
    $(document).on("click", ".add-to-cart-button", function () {

        var $button = $(this);
        var productId = $button.data("product-id");
        var quantity = $button.closest("tr").find(".quantity-input").val();

        $button.prop('disabled', true);

        var $loadingGif = $("<img>")
            .attr("src", wbo_params.loader_image)
            .addClass("loading-gif")
            .css("width", "20px")
            .insertAfter($button);

        $button.prop('disabled', true);

        $.ajax({
            url: wbo_params.ajax_url,
            method: 'POST',
            data: {
                action: 'wbo_add_to_cart',
                product_id: productId,
                quantity: quantity
            },
            success: function (response) {
                if (response.success) {
                    $loadingGif.remove();

                    var $completeGif = $("<img>")
                        .attr("src", wbo_params.complete_image)
                        .addClass("complete-gif")
                        .css("width", "20px")
                        .insertAfter($button);

                    $.ajax({
                        url: wbo_params.ajax_url,
                        method: 'POST',
                        data: {
                            action: 'wbo_woocommerce_get_refreshed_fragments'
                        },
                        success: function (cartResponse) {
                            if (cartResponse && cartResponse.fragments) {
                                $.each(cartResponse.fragments, function (key, value) {
                                    $(key).replaceWith(value);
                                });
                            }
                        }
                    });

                    setTimeout(function () {
                        $completeGif.remove();
                    }, 2000);

                } else {
                    alert(response.data.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
            },
            complete: function () {
                $button.prop('disabled', false);
            }
        });
    });

    $(document).ready(function ($) {
        $(".close").on("click", function () {
            $("#imageModal").fadeOut();
        });

        $(window).on("click", function (event) {
            if ($(event.target).is("#imageModal")) {
                $("#imageModal").fadeOut();
            }
        });
    });

});
