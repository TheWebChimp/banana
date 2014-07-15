/**
 * X-PropertyListItem Component
 * @author  biohzrdmx
 * @version 1.0
 */
xtag.register('x-property-list-item', {
	el: null,
	lifecycle:{
		created: function(){
			this.el = $(this);
			this.el.html('<span class="caption">'+this.el.data('caption')+'</span>');
		},
		attributeChanged: function(){
		}
	},
	events: {
		//
	},
	accessors: {
		//
	},
	methods: {
		//
	}
});

/**
 * X-PropertyList Component
 * @author  biohzrdmx
 * @version 1.0
 * @uses    X-PropertyListItem
 */
xtag.register('x-property-list', {
	el: null,
	lifecycle:{
		created: function(){
			this.el = $(this);
			if ( this.el.data('sortable') == true ) {
				this.el.sortable();
			}
			if ( this.el.data('data-add') ) {
				//
			}
		},
		attributeChanged: function(){
		}
	},
	events: {
		//
	},
	accessors: {
		//
	},
	methods: {
		addItem: function(caption) {
			var item = $('<x-property-list-item></x-property-list-item>');
			item.data('caption', caption);
			this.el.append(item);
		},
		removeItem: function() {
			console.log('removeItem');
		}
	}
});