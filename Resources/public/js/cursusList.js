(function () {
    'use strict';
    
    var maxHeights = [];
    var currentDatas = [];
    
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
        var cursusTitle = $(this).data('cursus-title');
        var cursusCode = $(this).data('cursus-code');
        var cursusDescription = $(this).data('cursus-description');
        var cursusIcon = $(this).data('cursus-icon');

        var cursusCodeTxt = (cursusCode === undefined || !cursusCode) ?
            '' : 
            '[' + cursusCode + ']';
        var cursusDescriptionTxt = (cursusDescription === undefined || !cursusDescription) ?
            '<div class="alert alert-warning">' + Translator.trans('no_description', {}, 'remoteCursus') + '</div>' : 
            cursusDescription;
        var cursusIconTxt = (cursusIcon === undefined || !cursusIcon) ?
            '' :
            '<img class="media-object img-responsive" src="' + cursusIcon + '" alt="">';
        currentDatas = [];
        currentDatas['mode'] = 'cursus';
        currentDatas['cursus_id'] = cursusId;
        
        $('#modal-rc-title').html(cursusTitle);
        $('#modal-rc-code').html(cursusCodeTxt);
        $('#modal-rc-description').html(cursusDescriptionTxt);
        $('#modal-rc-icon').html(cursusIconTxt);
        
        if (cursusIconTxt === '') {
            $('#modal-rc-icon').addClass('hidden');
        } else {
            $('#modal-rc-icon').removeClass('hidden');
        }
        $('#registration-confirmation-box').modal('show');
    });
    
    $('#cursus-list').on('click', '.register-to-course-btn', function () {
        var cursusId = $(this).data('cursus-id');
        var cursusTitle = $(this).data('cursus-title');
        var cursusCode = $(this).data('cursus-code');
        var cursusDescription = $(this).data('cursus-description');
        var cursusIcon = $(this).data('cursus-icon');

        var cursusCodeTxt = (cursusCode === undefined || !cursusCode) ?
            '' : 
            '[' + cursusCode + ']';
        var cursusDescriptionTxt = (cursusDescription === undefined || !cursusDescription) ?
            '<div class="alert alert-warning">' + Translator.trans('no_description', {}, 'remoteCursus') + '</div>' : 
            cursusDescription;
        var cursusIconTxt = (cursusIcon === undefined || !cursusIcon) ?
            '' :
            '<img class="media-object img-responsive" src="' + cursusIcon + '" alt="">';
        currentDatas = [];
        currentDatas['mode'] = 'course';
        currentDatas['cursus_id'] = cursusId;
        
        $('#modal-rc-title').html(cursusTitle);
        $('#modal-rc-code').html(cursusCodeTxt);
        $('#modal-rc-description').html(cursusDescriptionTxt);
        $('#modal-rc-icon').html(cursusIconTxt);
        
        if (cursusIconTxt === '') {
            $('#modal-rc-icon').addClass('hidden');
        } else {
            $('#modal-rc-icon').removeClass('hidden');
        }
        $('#registration-confirmation-box').modal('show');
    });
    
    $('#registration-confirmation-box').on('click', '#registration-confirm-btn', function () {
        
        $.ajax({
            url: Routing.generate(
                'formalibre_claroline_com_theme_cursus_hierarchy_register',
                {'cursusId': currentDatas['cursus_id']}
            ),
            type: 'POST',
            success: function () {
                console.log('registered');
            }
        });
    });
    
    var doNothing = function () {};
    
    resizePanels();
})();