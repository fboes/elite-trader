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
							var tr = $(this).closest($(this).data('ir-parents'));
							var filter = 'span.ir';
							if ($(this).data('ir-filter')) {
								filter = $(this).data('ir-filter')+ ' ' + filter;
							}
							tr.find(filter).each(function (index) {
								var el = $(this);
								el.hide().after('<input class="no-styling '+el.data('focus')+'" '+el.data('ir-attributes')+' value="'+el.html()+'" />').remove();
							})
							tr.find('.focus').each(function(){
								var newEl = $(this);
								newEl.focus();
								if (newEl.attr('type') != 'number') {
									var strLength= newEl.val().length;
									newEl[0].setSelectionRange(strLength, strLength);
								}
							})
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
						.on('change click','input.delete', function (event) {
							event.stopPropagation();
							$(this).closest('tr').toggleClass('deleted',$(this).is(':checked'));
						})
						.on('click','tbody tr',function (event) {
							$(this).toggleClass('focus');
						})
					;
				}
			}

			main.init($(this));
		});
	}
	$.fn.webapp = function (options) {
		return this.each(function() {
			var main = {
				options : $.extend(
					{
					},
					options
				),
				elements : {
					parent : null,
					modal  : null,
				},
				init : function (el) {
					this.elements.parent = el;
					this.elements.modal  = el.find('#modal');
					this.bindEvents();
				},
				bindEvents : function () {
					var that = this;
					if (this.elements.modal.length) {
						this.elements.parent
							.on('click','a.modal', function (event) {
								event.stopPropagation();
								event.preventDefault();
								var openEl = that.elements.modal.find($(this).attr('href'));
								if (openEl.length) {
									that.elements.modal.find('section').hide();
									that.elements.modal.css({
										width: $( document ).width(),
										height: $( document ).height()
									}).show();
									openEl.css({
										left: (($( document ).width() - openEl.width()) / 2)
									}).show();
								}
							})
							.on('click','#modal section', function (event) {
								event.stopPropagation();
							})
							.on('click','#modal', function (event) {
								that.elements.modal.hide();
								that.elements.modal.find('section').hide();
							})
						;
					}
				}
			}

			main.init($(this));
		});
	}
})(jQuery);


$(document).ready(function() {
	$('form').submitter({});
	$('body').webapp({});
});