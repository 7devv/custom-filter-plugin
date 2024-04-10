jQuery(document).ready(function ($) {
    var $lengthSlider = $('#length');
    var $widthSlider = $('#width');
    var $lengthValue = $('#length-value');
    var $widthValue = $('#width-value');
    var $filterButton = $('#filter-button');
    var $filteredProductsContainer = $('#filtered-products-container');
    var $toggleFilterButton = $('#toggle-filter');
    var $customFilterFormWrapper = $('#custom-filter-form-wrapper');

    // Initialize progress bar color on page load
    updateProgressBar($lengthSlider[0]);
    updateProgressBar($widthSlider[0]);

    // Toggle filter form visibility
    $toggleFilterButton.on('click', function () {
        $customFilterFormWrapper.toggle();
    });

    // Update slider values on change
    $lengthSlider.on('input', function () {
        $lengthValue.text(this.value);
        updateProgressBar(this);
    });

    $widthSlider.on('input', function () {
        $widthValue.text(this.value);
        updateProgressBar(this);
    });

    // Function to update the progress bar color
    function updateProgressBar(slider) {
        var value = (slider.value - slider.min) / (slider.max - slider.min) * 100;
        var sliderType = slider.id === 'length' ? 'webkit' : 'moz';
        slider.style.setProperty(`--range-progress`, `${value}%`);
    }

    // Handle filter button click
    $filterButton.on('click', function () {
        var length = $lengthSlider.val();
        var width = $widthSlider.val();

        // Send Ajax request to filter products
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_products',
                length: length,
                width: width
            },
            beforeSend: function () {
                // Show loading spinner or message
                $filteredProductsContainer.html('<div class="loading">Loading...</div>');
            },
            success: function (response) {
                // Update the filtered products container with the response
                $filteredProductsContainer.html(response);
            },
            error: function (xhr, status, error) {
                // Handle error if needed
                console.log(error);
            }
        });
    });

    // Load initial products
    $.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        data: {
            action: 'filter_products',
            length: 1,
            width: 1
        },
        success: function (response) {
            $filteredProductsContainer.html(response);
        },
        error: function (xhr, status, error) {
            console.log(error);
        }
    });
});
