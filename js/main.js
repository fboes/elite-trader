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
								var strLength= newEl.val().length;
								newEl.focus();
								newEl[0].setSelectionRange(strLength, strLength);
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
})(jQuery);


$(document).ready(function() {
	$('form').submitter({});
});