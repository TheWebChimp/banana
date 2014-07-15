AdminModule = Class.extend({
	onInit: function() {
		//
	},
	onReady: function() {
		//
	}
});

AdminModuleAttachments = AdminModule.extend({
	templates: null,
	onInit: function() {
		Dropzone.options.formAttachment = {
			thumbnailWidth: 150,
			thumbnailHeight: 150,
			dictDefaultMessage: 'Drop files (or click) here to upload'
		};
	},
	onReady: function() {
		var module = this;
		$('.media-list .media a').on('click', function(e) {
			var el = $(this),
				media = el.closest('.media'),
				id = media.data('id'),
				form = $('#form-attachment');
			e.preventDefault();
			form.loading();
			$.ajax({
				url: constants.siteUrl + '/admin/attachments/show/' + id + '.json',
				type: 'get',
				dataType: 'json',
				success: function(response) {
					var id = $('input[name="id"]'),
						name = $('input[name="name"]'),
						description = $('textarea[name="description"]'),
						action = form.data('action')
						btnDelete = $('.btn-delete');
					form.loading('done');
					form.attr('action', action + response.id)
					id.val( response.id );
					name.val( response.name );
					description.val( response.description );
					btnDelete.attr('href', btnDelete.data('href') + response.id);
				}
			});
		});
		$('#form-attachment').ajaxForm({
			beforeSubmit: function() {
				var form = $('#form-attachment'),
					button = form.find('.btn-submit');
				form.loading();
				button.loading({ text: 'Updating...' });
			},
			success: function() {
				var form = $('#form-attachment'),
					button = form.find('.btn-submit');
				form.loading('done');
				button.loading('done');
			}
		});
	}
});

AdminModuleUsers = AdminModule.extend({
	onInit: function() {
		//
	},
	onReady: function() {
		$('#form-user').on('submit', function() {
			var form = $(this),
				button = form.find('.btn-submit');
			return form.validate({
				success: function() {
					form.loading();
					button.loading({ text: 'Saving user...' });
				},
				error: function(fields) {
					fields.each(function() {
						var field = $(this);
						field.closest('.form-group').addClass('has-error');
						field.on('focus', function() {
							field.closest('.form-group').removeClass('has-error');
							field.off('focus');
						});
					});
				}
			});
		});
	}
});

Admin = Class.extend({
	module: null,
	modules: {
		attachments: new AdminModuleAttachments(),
		users: new AdminModuleUsers()
	},
	init: function() {
		var obj = this;
		if ( typeof obj.modules[constants.mvc.controller] !== undefined ) {
			obj.module = obj.modules[constants.mvc.controller];
		}
		if (obj.module) {
			obj.module.onInit();
		}
		jQuery(document).ready(function($) {
			obj.onReady();
			if (obj.module) {
				obj.module.onReady();
			}
		});
		//
		$.fn.validate.types.confirm = function(options) {
			var element = options.element || null;
			var param = options.param || null;
			var compare;
			if ( element && !element.is(':disabled') ) {
				if ( typeof(param) == 'string' ) {
					compare = $(param);
				} else {
					compare = param;
				}
				if ( compare === null || ( compare.val() && element.val() == '' ) || element.val() !== compare.val() ) {
					return false
				}
			}
			return true;
		};
	},
	onReady: function() {

		// Lazy loading for images
		$('img.lazyload').lazyload();

		// Hook global events/plugins here
		$('.sortable-list .items').sortable({
			axis: 'y'
		});
	}
});

var admin = new Admin();