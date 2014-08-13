(function ($) {
	$.fn.submitter = function (options) {
		return this.each(function() {
			var main = {
				options : $.extend(
					{
					},
					options
				),
				elements : {
					parent : null
				},
				init : function (el) {
					this.elements.parent = el;
					this.bindEvents();
				},
				bindEvents : function () {
					var that = this;
					// Your stuff here
					this.elements.parent
						/*.on('blur', 'input', function (event) {
							var el = $(this);
							$.post( that.elements.parent.attr('action'), {
									'type'  : 'ajax',
									'value' : el.val(),
									'name'  : el.attr('name')
								}, function( data ) {
									el.removeClass('altered');
								}
							);
						})
						.on('focus','input', function (event) {
							$(this).addClass('altered');
						})*/
						.on('click focus','span.ir',function (event){
							event.stopPropagation();
							$(this).data('focus','focus');
							var tr = $(this).parents($(this).data('ir-parents'));
							tr.find('span.ir').each(function (index) {
								var el = $(this);
								el.hide().after('<input class="no-styling '+el.data('focus')+'" '+el.data('ir-attributes')+'" value="'+el.html()+'" />').remove();
							})
							tr.find('.focus').each(function(){
								var newEl = $(this);
								newEl.focus().setCursorPosition(newEl.val().length * 2);
								newEl.removeClass('focus');
							})
						})
						.on('click','tr',function (event) {
							$(this).toggleClass('focus');
						})
						.on('change','.show-on-empty', function (event) {
							var el = $($(this).data('show-on-empty'));
							if ($(this).val() && el.is(":visible") ) {
								el.slideUp('fast');
							}
							else if (!$(this).val() && el.is(":hidden")  ) {
								el.slideDown('fast');
							}
						})
					;
				}
			}

			main.init($(this));
		});
	}
	$.fn.setCursorPosition = function(pos) {
		if (this.setSelectionRange) {
		  this.setSelectionRange(pos, pos);
		} else if (this.createTextRange) {
		  var range = this.createTextRange();
		  range.collapse(true);
		  if(pos < 0) {
		    pos = $(this).val().length + pos;
		  }
		  range.moveEnd('character', pos);
		  range.moveStart('character', pos);
		  range.select();
		}
	}
})(jQuery);


$(document).ready(function() {
	$('form').submitter({});
});