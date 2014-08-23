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
								var value = el.html().replace(/(\d+),(\d+)/g,'$1$2');
								el.hide().after('<input class="no-styling '+el.data('focus')+'" '+el.data('ir-attributes')+' value="'+value+'" />').remove();
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
					parent   : null,
					modal    : null,
					messages : null
				},
				init : function (el) {
					var that = this;
					this.elements.parent = el;
					this.elements.modal  = el.find('#modal');
					this.elements.messages = el.find('#messages');
					if (this.elements.messages.length) {
						var timer = setTimeout(function () {
							that.elements.messages.slideUp('fast');
						}, 2000);
					}
					this.bindEvents();
				},
				bindEvents : function () {
					var that = this;
					if (this.elements.modal.length) {
						this.elements.parent
							.on('click','a.modal', function (event) {
								event.stopPropagation();
								event.preventDefault();
								var links = $(this).attr('href').split('#');
								if (!links[1]) {
									links[1] = links[0];
									links[0] = null;
								}
								var openEl = that.elements.modal.find('#' + links[1]);
								if (!openEl.length && links[0]) {
									$.get(links[0],function(data,status){
										that.elements.modal.find('aside').append(
											data
											.replace(/<section/,'<section id="'+links[1]+'"')
											.replace(/action="#"/, 'action="'+document.location.href+'"')
										);
										that.openModal(that.elements.modal.find('#' + links[1]));
									})
								}
								else if (openEl.length) {
									that.openModal(openEl);
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
				},
				openModal : function (openEl) {
					if (openEl.length) {
						this.elements.modal.find('section').hide();
						this.elements.modal.css({
							width: $( document ).width(),
							height: $( document ).height()
						}).show();
						openEl.css({
							left: (($( document ).width() - openEl.outerWidth()) / 2)
						}).show().find(':input:first').focus();
					}
				}
			}

			main.init($(this));
		});
	}
})(jQuery);


$(document).ready(function() {
	$('form, #modal').submitter({});
	$('body').webapp({});
});