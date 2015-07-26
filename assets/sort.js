$(function() 
{
    $("{blocks_js_ids}").sortable(
    {
        connectWith: '.connectedSortable',
        update : function () 
        { 
            $.ajax(
            {
                type: "POST",
                url: "/blocks/sort/sort",
                data: 
                {
                    {blocks_js_data}
                },
                success: function(html)
                {

                    $('.manip_success').show().delay(1000).fadeOut();

                    
                }
            });
        } 
    }).disableSelection();
});