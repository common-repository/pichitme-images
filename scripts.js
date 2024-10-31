jQuery(document).ready(function ($){
// for debug : trace every event
/*var originalTrigger = wp.media.view.MediaFrame.Post.prototype.trigger;
wp.media.view.MediaFrame.Post.prototype.trigger = function(){
    console.log('Event Triggered:', arguments);
    originalTrigger.apply(this, Array.prototype.slice.call(arguments));
}*/

 
// custom state : this controller contains your application logic
wp.media.controller.Custom = wp.media.controller.State.extend({
 
    initialize: function(){
        // this model contains all the relevant data needed for the application
        this.props = new Backbone.Model({ custom_data: '' });
        this.props.on( 'change:custom_data', this.refresh, this );
    },
    
    // called each time the model changes
    refresh: function() {
        // update the toolbar
    	this.frame.toolbar.get().refresh();
	},
	
	// called when the toolbar button is clicked
	customAction: function(){
	    console.log(this.props.get('custom_data'));
	}
    
});
 
// custom toolbar : contains the buttons at the bottom
wp.media.view.Toolbar.Custom = wp.media.view.Toolbar.extend({
	initialize: function() {
		_.defaults( this.options, {
		    event: 'custom_event',
		    close: false,
			items: {
			    custom_event: {
			        text: wp.media.view.l10n.customButton, // added via 'media_view_strings' filter,
			        style: 'primary',
			        priority: 80,
			        requires: false,
			        click: this.customAction
			    }
			}
		});
 
		wp.media.view.Toolbar.prototype.initialize.apply( this, arguments );
	},
 
    // called each time the model changes
	refresh: function() {
	    // you can modify the toolbar behaviour in response to user actions here
	    // disable the button if there is no custom data
		var custom_data = this.controller.state().props.get('custom_data');
		this.get('custom_event').model.set( 'disabled', ! custom_data );
		
	    // call the parent refresh
		wp.media.view.Toolbar.prototype.refresh.apply( this, arguments );
	},
	
	// triggered when the button is clicked
	customAction: function(){
	    this.controller.state().customAction();
	}
});
 
// custom content : this view contains the main panel UI
wp.media.view.Custom = wp.media.View.extend({
	className: 'media-custom attachments-browser',
	
	// bind view events
	events: {
		'input':  'custom_update',
		'keyup':  'custom_update',
		'change': 'custom_update'
	},
 
	initialize: function() {
	    
	    _.defaults( this.options, {
		    event: 'custom_event',
		    close: false,
			items: {
			    custom_event: {
			        text: wp.media.view.l10n.customContent, // added via 'media_view_strings' filter,
			        requires: false,
			        click: this.customAction
			    }
			}
		});
	},
	
	render: function(){
		$('.media-custom').html('<img src="/wp-content/plugins/pichitme-images/animation.gif" />');
	    $.post('/wp-content/plugins/pichitme-images/pichit-template.php', function( data ) {
		   $('.media-custom').html(data); 
	    });
	},
	
	custom_update: function( event ) {
		
	}
});
 
 
// supersede the default MediaFrame.Post view
var oldMediaFrame = wp.media.view.MediaFrame.Post;
wp.media.view.MediaFrame.Post = oldMediaFrame.extend({
 
    initialize: function() {
        oldMediaFrame.prototype.initialize.apply( this, arguments );
        
        this.states.add([
            new wp.media.controller.Custom({
                id:         'my-action',
                menu:       'default', // menu event = menu:render:default
                content:    'custom',
				title:      wp.media.view.l10n.customMenuTitle, // added via 'media_view_strings' filter
				priority:   1,
				toolbar:    'main-my-action', // toolbar event = toolbar:create:main-my-action
				type:       'link'
            })
        ]);
 
        this.on( 'content:render:custom', this.customContent, this );
        this.on( 'toolbar:create:main-my-action', this.createCustomToolbar, this );
        this.on( 'toolbar:render:main-my-action', this.renderCustomToolbar, this );
    },
    
    createCustomToolbar: function(toolbar){
        toolbar.view = new wp.media.view.Toolbar.Custom({
		    controller: this
	    });
    },
 
    customContent: function(){
        
        // this view has no router
        this.$el.addClass('hide-router');
 
        // custom content view
        var view = new wp.media.view.Custom({
            controller: this,
            model: this.state().props
        });
 
        this.content.set( view );
    }
 
});
//Toggle the active class for the images on PicHit media window
$(document).on('click','.media-custom .attachment', function () {
	$('.media-custom .attachment').removeClass('details selected');
	$(this).toggleClass('details selected');
});


//Check if insert button should be disabled.
function checkCustomButton () {
	if($('.attachment').hasClass('selected')) {
		$('.media-button-custom_event').removeAttr("disabled");
	}else {
		$('.media-button-custom_event').attr("disabled","disabled");
	}
}
setInterval(checkCustomButton, 100);

//Insert image on to page/post
$(document).on('click','.media-button-custom_event', function () {
	//If visual mode
	if ((typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden()) {
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, $('.media-custom .selected').children().children().children('.centered').html());
	}else{
		//If text mode
		var position = $(".wp-editor-area").getCursorPosition()
		var content = $('.wp-editor-area').val();
		var newContent = content.substr(0, position) + $('.media-custom .selected').children().children().children('.centered').html() + content.substr(position);
		$('.wp-editor-area').val(newContent);
	}
	//Hide mediawindow on Successful insert
	$('#__wp-uploader-id-2').hide();
});
setInterval(checkCustomButton, 100);

//Detect current mouse position in text view.
(function ($, undefined) {
    $.fn.getCursorPosition = function () {
        var el = $(this).get(0);
        var pos = 0;
        if ('selectionStart' in el) {
            pos = el.selectionStart;
        } else if ('selection' in document) {
            el.focus();
            var Sel = document.selection.createRange();
            var SelLength = document.selection.createRange().text.length;
            Sel.moveStart('character', -el.value.length);
            pos = Sel.text.length - SelLength;
        }
        return pos;
    }
})(jQuery);

//Initiate the pichit menu on first click.
$(document).on('click','.pichit-images ', function () {
	/*$('.media-menu .media-menu-item.active').removeClass('active');
	$('.media-menu .media-menu-item').first().addClass('active');
	$('.media-button-insert').addClass('media-button-custom_event').html('Pichit');
	$('.attachments-browser').html('<img src="/wp-content/plugins/pichit/animation.gif.gif" />');
	$('.attachments-browser').addClass('media-custom');
	*/
	$('.media-menu .media-menu-item').first().trigger('click');
});

$(document).on('click','.load-standard', function () {
	var object = $(this);
	object.children().addClass('spin');
	var length = $('.attachment').size();
	$.post('/wp-content/plugins/pichitme-images/pitchit-loads.php', {load:length}, function( data ) {
		object.before(data);
		object.children().removeClass('spin');
	});
});
$(document).on('keyup','.media-custom .media-toolbar-primary input', function (e) {
	if(e.which == 13){
		$this = $(this);
		var value = $this.val();
		$this.next().after('<img src="/wp-content/plugins/pichitme-images/button-image.png" class="temp_image spin" />');
		$.post('/wp-content/plugins/pichitme-images/pichit-search.php', {value:value}, function( data ) {
			$('.media-custom .attachments').html(data);
			$('.temp_image').remove();
		});
	}
});
$(document).on('click', '.pichit-search', function () {
	$this = $(this).prev();
	var value = $this.val();
	$this.next().after('<img src="/wp-content/plugins/pichitme-images/button-image.png" class="temp_image spin" />');
	$.post('/wp-content/plugins/pichitme-images/pichit-search.php', {value:value}, function( data ) {
		$('.media-custom .attachments').html(data);
		$('.temp_image').remove();
	});
});
$(document).on('click', '.load-search', function () {
	var object = $(this);
	var keyword = object.attr('data-keyword');
	object.children().addClass('spin');
	var length = $('.attachment').size();
	$.post('/wp-content/plugins/pichitme-images/pitchit-load-search.php', {load:length,keyword:keyword}, function( data ) {
		object.before(data);
		object.children().removeClass('spin');
	});
});



});