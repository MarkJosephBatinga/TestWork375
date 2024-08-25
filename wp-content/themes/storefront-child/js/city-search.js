jQuery(document).ready(function($) {
    $('#city-search-form').on('submit', function(e) {
        e.preventDefault();

        var search = $('#city-search').val();

        $.ajax({
            url: citySearch.ajax_url,
            type: 'POST',
            data: {
                action: 'search_cities',
                search: search
            },
            success: function(response) {
                $('#cities-table-container').html(response);
            }
        });
    });
});
