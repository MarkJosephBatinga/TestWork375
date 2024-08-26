(function($) {
    $(document).ready(function() {
        // Attach an input event handler to the search field
        $('#city-search-field').on('input', function() {
            var search = $(this).val(); // Get the value of the search field
            
            $.ajax({
                url: citySearchParams.ajax_url,
                method: 'POST',
                data: {
                    action: 'search_cities',
                    search: search
                },
                success: function(response) {
                    var $resultsContainer = $('#city-search-results');
                    $resultsContainer.empty(); // Clear previous results
                    
                    if (response.success) {
                        // Iterate over each city in the response data
                        $.each(response.data, function(index, city) {
                            // Append each city as a row in the results container
                            $resultsContainer.append(
                                '<tr>' +
                                '<td>' + city.country_name + '</td>' +
                                '<td>' + city.city_name + '</td>' +
                                '<td>' + city.latitude + '</td>' +
                                '<td>' + city.longitude + '</td>' +
                                '</tr>'
                            );
                        });
                    } else {
                        // Display a message if no cities were found
                        $resultsContainer.append('<tr><td colspan="4">' + response.data + '</td></tr>');
                    }
                }
            });
        });
    });
})(jQuery);
