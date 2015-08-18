(function () {
    'use strict';
    
    var maxHeights = [];
    
    function resizePanels()
    {
        $('.cursus-panel').each(function () {
            var index = $(this).data('index');
            var rootId = $(this).data('root-id');
            var height = $(this).outerHeight();
            
            if (maxHeights[rootId + '-' + index] === undefined ||
                height > maxHeights[rootId + '-' + index]) {
            
                maxHeights[rootId + '-' + index] = height;
            }
        });
        
        for (var key in maxHeights) {
            $('.cursus-panel-' + key).css('height', maxHeights[key]);
        }
    }
    
    resizePanels();
})();