Banana = function(options) {
	var obj = {
		init: function(options) {
			// Parseamos la direccion actual para obtener el estado mvc
			var curUrl = location.href;
			var siteUrl = constants.siteUrl;
			siteUrl = siteUrl.replace(/(http|https):\/\//i, '');
			curUrl = curUrl.replace(/(http|https):\/\//i, '')
						   .replace(siteUrl, '')
						   .replace(/^\//, '')
						   .replace(location.search, '');
			var parts = curUrl.split('/');
			this.mvc = {
				controller: parts[0] || 'index',
				action: parts[1] || 'index',
				id: parts[2] || null
			};
			// Esperamos a que se termine de cargar la página
			this.onPageReady = options.onPageReady || $.noop;
			$(document).ready(function() {
				// Establecemos el idioma de los dialogos
				bootbox.setDefaults({
					locale: 'es'
				});
				// Y ahora llamamos al callback
				obj.onPageReady.call(obj);
			});
		},
	};
	// Ejecutamos constructor
	obj.init(options);
	return obj;
};

var banana = Banana({
	onPageReady: function() {
		// Bindeamos eventos y acciones específicos a la página
		switch (this.mvc.controller) {
			// Usuarios ---------------------------------------------------------------------------
			case 'users':
				switch (this.mvc.action) {
					case 'login':
						$('#login_form').on('submit', function() {
							return $(this).validate({
								error: function(fields) {
									fields.each(function(index, el) {
										var field = $(el);
										field.closest('.form-group').addClass('has-error');
										field.on('focus', function() {
											field.closest('.form-group').removeClass('has-error');
											field.off('focus');
										});
									});
									$('.row').effect('shake', { distance: 10 });
								}
							});
						});
						break;
					case 'index':
						//
						break;
				}
				break;
		}
		// Y los generales
		// $('')
	}
});