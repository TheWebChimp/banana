ClientModule = Class.extend({
	onInit: function() {
		//
	},
	onReady: function() {
		//
	}
});

ClientModuleBites = Class.extend({
	onInit: function() {
		//
	},
	onReady: function() {
		var obj = this;
		//
		$('select[name=syntax]').on('change', function() {
			var el = $(this),
				val = el.val(),
				codemirror = $('.codemirror'),
				editor = codemirror.data('editor');
			editor.setOption("mode", val);
		});

		$('.btn-theme').on('click', function(e) {
			e.preventDefault();
			var el = $(this),
				theme = el.data('theme'),
				codemirror = $('.codemirror'),
				editor = codemirror.data('editor');
			if (! el.hasClass('active') ) {
				editor.setOption("theme", theme);
				$('.btn-theme').removeClass('active');
				el.addClass('active');
			}
		});

		$(".btn-copy").zclip({
			path: constants.siteUrl + '/assets/ZeroClipboard.swf',
			copy:$('#content').val(),
			afterCopy: function(){
				$.alert('The bite contents have been copied to the clipboard');
			}
		});


	}
});

ClientModuleKeyring = Class.extend({
	onInit: function() {
		//
	},
	onReady: function() {
		var obj = this;
		//
		$('.btn-preview').on('click', function() {
			var preview = $('.preview-area'),
				mardown = $('.markdown textarea').val();
			preview.text('Generating preview...');
			marked(mardown, { sanitize: false }, function(err, html) {
				preview.html( html );
			});
		});
	}
});

ClientModuleToDo = Class.extend({
	onInit: function() {
		//
	},
	onReady: function() {
		var obj = this;
		//
		$('.btn-categories').on('click', function(e) {
			var el = $(this),
				list = $('.categories');
			e.preventDefault();
			if ( list.hasClass('edit') ) {
				list.removeClass('edit');
				el.removeClass('active');
			} else {
				list.addClass('edit');
				el.addClass('active');
			}
		});
		//
		$('.categories').on('click', '.btn-delete', function(e) {
			var el = $(this),
				category = el.closest('.list-group-item'),
				list = el.closest('.list-group');
			e.preventDefault();
			$.ajax({
				url: constants.siteUrl + '/todo/delete-category/' + category.data('id'),
				type: 'post',
				data: {
					token: $('meta[name=token]').attr('content')
				},
				dataType: 'json',
				success: function(response) {
					window.location.reload();
				}
			});
		});
		//
		$('.todo .btn-toggle').on('click', function(e) {
			e.preventDefault();
			var el = $(this),
				group = el.closest('.btn-group'),
				list = $('.todos'),
				show = el.data('show'),
				active = show == 'All' ? list.children('.todo') : list.find('[data-status="'+ show +'"]');
			group.find('.active').not(el).removeClass('active');
			el.addClass('active');
			list.children('.todo').not(active).hide();
			active.show();
		});
		//
		$('.todo .todos .todo a').on('click', function(e) {
			e.stopPropagation();
			return true;
		});
		//
		$('.todo .with-extras input[name=name]').on('focus', function() {
			$('.extras').removeClass('hide');
		});
		//
		$('.todo .todos .todo').on('click', function(e) {
			e.preventDefault();
			var el = $(this),
				todo = el.closest('.todo');
			if ( todo.hasClass('in') ) {
				todo.removeClass('in');
			} else {
				todo.addClass('in');
			}
		});
		//
		$('.btn-preview').on('click', function() {
			var preview = $('.preview-area'),
				mardown = $('textarea[name=details]').val();
			preview.text('Generating preview...');
			marked(mardown, { sanitize: false }, function(err, html) {
				preview.html( html );
			});
		});
		//
		$('.attachments').on('click', '.btn-remove', function(e) {
			e.preventDefault();
			var el = $(this),
				attachment = el.closest('.attachment'),
				token = $('meta[name=token]').attr('content'),
				id = el.data('id');
			$.ajax({
				url: constants.siteUrl + '/tickets/detach',
				type: 'post',
				data: {
					id: id,
					token: token
				},
				dataType: 'json',
				success: function(response) {
					if (response && response.result == 'success') {
						attachment.detach();
					} else {
						$.alert('An error has ocurred, please try again later.');
					}
				}
			});
		});
	}
});

ClientModuleTickets = ClientModule.extend({
	onInit: function() {
		//
	},
	onReady: function() {

		if(constants.mvc.action == 'calendar') {

			var calendar = $("#tickets-calendar").calendar({
				tmpl_path: constants.siteUrl + '/parts/calendar/',
				events_source: function () { return []; },
				onAfterViewLoad: function(view) {
					$('.page-header h3').text(this.getTitle());
					$('.btn-group button').removeClass('active');
					$('button[data-calendar-view="' + view + '"]').addClass('active');
				},
			});

			calendar.setLanguage('es-MX');
			calendar.view();

			$('.btn-group button[data-calendar-nav]').each(function() {
				var $this = $(this);
				$this.click(function() {
					calendar.navigate($this.data('calendar-nav'));
				});
			});

			$('.btn-group button[data-calendar-view]').each(function() {
				var $this = $(this);
				$this.click(function() {
					calendar.view($this.data('calendar-view'));
				});
			});

			$('#first_day').change(function(){
				var value = $(this).val();
				value = value.length ? parseInt(value) : null;
				calendar.setOptions({first_day: value});
				calendar.view();
			});
		}

		$('#add-label-form').ajaxForm({
			dataType: 'json',
			beforeSubmit: function(data, form) {
				return form.validate({
					success: function() {
						form.loading();
						form.find('button[type=submit]').loading({ text: 'Creating...' });
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
			},
			success: function(response, status, xhr, form) {
				if (response && response.data) {
					form.loading('done');
					form.find('button[type=submit]').loading('done');
					form.find('.extras').addClass('hide');
					form.resetForm();
					var labels = form.closest('.panel').find('.labels');
					labels.append('<a href="#" class="list-group-item"><strong class="pull-right">0</strong><button class="btn btn-link btn-xs pull-right hide"><i class="fa fa-times"></i></button><span class="color" style="background: ' + response.data.description + '"></span> ' + response.data.name + '</a>');
				} else {
					$.alert('An error has ocurred');
				}
			}
		});
		//
		$('.tickets-index input[name=label]').on('focus', function() {
			$('.extras').removeClass('hide');
		});
		//
		$('.tickets-index input[name=filter]').on('change', function() {
			var el = $(this),
				form = el.closest('form');
			form.submit();
		});
		$('.tickets-index input[name=sort]').on('change', function() {
			var el = $(this),
				form = el.closest('form');
			form.submit();
		});
		$('.attachment .type-image').magnificPopup({ type: 'image' });
		//
		$('.btn-preview').on('click', function() {
			var preview = $('.preview-area'),
				mardown = $('textarea[name=details]').val();
			preview.text('Generating preview...');
			marked(mardown, { sanitize: false }, function(err, html) {
				preview.html( html );
			});
		});
		//
		$('.attachments').on('click', '.btn-remove', function(e) {
			e.preventDefault();
			var el = $(this),
				attachment = el.closest('.attachment'),
				token = $('meta[name=token]').attr('content'),
				id = el.data('id');
			$.ajax({
				url: constants.siteUrl + '/tickets/detach',
				type: 'post',
				data: {
					id: id,
					token: token
				},
				dataType: 'json',
				success: function(response) {
					if (response && response.result == 'success') {
						attachment.detach();
					} else {
						$.alert('An error has ocurred, please try again later.');
					}
				}
			});
		});
	}
});

Client = Class.extend({
	module: null,
	modules: {
		tickets: new ClientModuleTickets(),
		keyring: new ClientModuleKeyring(),
		bites: new ClientModuleBites(),
		todo: new ClientModuleToDo()
	},
	utils: {
		fileSize: function(bytes) {
			var exp = Math.log(bytes) / Math.log(1024) | 0;
			var result = (bytes / Math.pow(1024, exp)).toFixed(2);
			return result + ' ' + (exp == 0 ? 'bytes': 'KMGTPEZY'[exp - 1] + 'B');
		},
		upload: function(file, options) {
			var options = options || {},
				onStarted = options.onStarted || $.noop,
				onError = options.onError || $.noop,
				onProgress = options.onProgress || $.noop,
				onComplete = options.onComplete || $.noop,
				url = options.url || constants.ajaxUrl,
				formData = new FormData();
			onStarted(file);
			formData.append('file', file);
			$.ajax({
				url: url,
				type: 'post',
				contentType: false,
				processData: false,
				dataType: 'json',
				xhr: function() {
					var xhrobj = $.ajaxSettings.xhr();
					if (xhrobj.upload) {
						xhrobj.upload.addEventListener('progress', function(event) {
							var percent = 0;
							var position = event.loaded || event.position;
							var total = event.total;
							if (event.lengthComputable) {
								percent = Math.ceil(position / total * 100);
							}
							onProgress(percent);
						}, false);
					}
					return xhrobj;
				},
				data: formData,
				success: function(response) {
					onProgress(100);
					onComplete(response);
				}
			});
		}
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
		window.ZeroClipboard = ZeroClipboard;
	},
	onReady: function() {
		var obj = this;

		$('.bites-embed .btn-menu').on('click', function(e) {
			e.preventDefault();
			var el = $(this),
				media = $('.media');
			media.toggleClass('visible');
		});

		$('.codemirror').each(function() {
			var el = $(this),
				mode = el.data('mode'),
				readOnly = el.data('readonly') || false,
				textarea = el.find('textarea');
			var editor = CodeMirror.fromTextArea(textarea[0], {
				styleActiveLine: true,
				autoCloseBrackets: true,
				matchTags: { bothTags: true },
				matchBrackets: true,
				// lineWrapping: true,
				readOnly: readOnly,
				lineNumbers: true,
				theme: 'neo',
				mode: mode
			});
			el.data('editor', editor);
		});

		$('input[name=color]').minicolors({
			theme: 'bootstrap',
			letterCase: 'uppercase',
		});

		$('[data-toggle=fullscreen]').on('click', function(e) {
			var container = $('.fullscreen-container');
			e.preventDefault();
			container.toggleClass('active');
			if ( container.hasClass('active') ) {
				$('[data-toggle=fullscreen]').addClass('active');
			} else {
				$('[data-toggle=fullscreen]').removeClass('active');
			}
		});

		$('[data-chart=line]').each(function() {
			var el = $(this),
				theme = el.data('theme') || 'theme2',
				title = el.data('title') || '',
				data = el.find('textarea');
			console.log( $.parseJSON( data.val() ) );
			var chart = new CanvasJS.Chart(el.attr('id'), {
				theme: theme,
				title: {
					text: title
				},
				axisY: {
					gridColor: '#EEEEEE',
					tickColor: '#EEEEEE',
					labelFontFamily: 'Open Sans, sans-serif',
					labelFontSize: 12,
					labelFontWeight: 400,
					interval: 1,
					minimum: 0,
				},
				axisX: {
					gridColor: '#EEEEEE',
					tickColor: '#EEEEEE',
					labelFontFamily: 'Open Sans, sans-serif',
					labelFontSize: 12,
					labelFontWeight: 400,
					interval: 1,
					valueFormatString: "#"
				},
				legend: {
					verticalAlign: "bottom",
					horizontalAlign: "center"
				},
				data: $.parseJSON( data.val() )
			});
			//
			chart.render();
			el.find('.canvasjs-chart-credit').detach();
			//
		});

		// Lazy loading for images
		$('img.lazyload').lazyload();

		// Push dataTransfer property for 'drop' event
		$.event.props.push('dataTransfer');

		$('[data-update]').on('click', function() {
			var el = $(this),
				target = $( el.data('update') );
			if (target.length) {
				target.val( el.data('value') );
				target.trigger('change');
			}
		});

		// Form validation
		$('form[data-submit=validate]').on('submit', function() {
			var form = $(this);
			return form.validate({
				success: function() {
					//
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

		// Drag & Drop file upload support
		var handleDrag = function(e) {
			e.stopPropagation();
			e.preventDefault();
			// Eat the event so the browser doesn't process the drag & drop operation
			return false;
		};
		$('.dropfiles').on('dragover', handleDrag);
		$('.dropfiles').on('dragenter', handleDrag);
		$('.dropfiles input').on('change', function(e) {
			var el = $(this),
				attachments = $('.attachments'),
				attachment = null;
			obj.utils.upload(el[0].files[0], {
				url: constants.siteUrl + '/tickets/upload',
				onStarted: function(file) {
					attachment = $('<div class="attachment"><i class="fa fa-file"></i> '+ file.name +' <span class="text-muted"> ('+ obj.utils.fileSize(file.size) +') -</span> <span class="status"></span></div>');
					attachments.append(attachment);
				},
				onProgress: function(percent) {
					attachment.children('.status').text(percent + '%');
				},
				onComplete: function(response) {
					if (response && response.attachment) {
						attachment.children('.status').html('<a href="#" class="btn-remove" data-id="'+ response.attachment.id +'">Remove</a>');
						attachment.append('<input type="hidden" name="attachments[]" value="'+ response.attachment.id +'"/>');
					}
				}
			});
		});
		$('.dropfiles').on('drop', function(e) {
			e.stopPropagation();
			e.preventDefault();
			var el = $(this),
				attachments = $('.attachments'),
				files = e.dataTransfer.files;
			for (var i = 0, f; f = files[i]; i++) {
				(function(file) {
					var attachment = null;
					obj.utils.upload(file, {
						url: constants.siteUrl + '/tickets/upload',
						onStarted: function(file) {
							attachment = $('<div class="attachment"><i class="fa fa-file"></i> '+ file.name +' <span class="text-muted"> ('+ obj.utils.fileSize(file.size) +') -</span> <span class="status"></span></div>');
							attachments.append(attachment);
						},
						onProgress: function(percent) {
							attachment.children('.status').text(percent + '%');
						},
						onComplete: function(response) {
							if (response && response.attachment) {
								attachment.children('.status').html('<a href="#" class="btn-remove" data-id="'+ response.attachment.id +'">Remove</a>');
								attachment.append('<input type="hidden" name="attachments[]" value="'+ response.attachment.id +'"/>');
							}
						}
					});
				})(f);
			}
			return false;
		});
	}
});

var client = new Client();