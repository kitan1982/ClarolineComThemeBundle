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
    
    $('#cursus-list').on('click', '.register-to-cursus-btn', function () {
        var cursusId = $(this).data('cursus-id');
        
        window.Claroline.Modal.confirmRequest(
            Routing.generate(
                'formalibre_claroline_com_theme_cursus_hierarchy_register',
                {'cursusId': cursusId}
            ),
            doNothing,
            cursusId,
            Translator.trans('register_to_cursus_confirmation_message', {}, 'remoteCursus'),
            Translator.trans('register_to_cursus_confirmation_title', {}, 'remoteCursus')
        );
    });
    
    var doNothing = function () {};
    
    resizePanels();
})();