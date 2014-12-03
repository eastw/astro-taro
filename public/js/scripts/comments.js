$.widget("ui.usercombobox", {
	options:{
			dataSource: "",
			dataType: "",
			minLength: 0
		},
    _create: function() {
        var self = this,
            select = this.element.hide(),
            selected = select.children(":selected"),
            value = selected.val() ? selected.text() : "";
            var input = this.input = $("<input id=\"usercombobox\" >").insertAfter(select).val(value).autocomplete({
	            delay: 0,
	            minLength: self.options.minLength,
	            source: function(request, response) {
	                $.ajax({
	                    url: self.options.dataSource,
	                    type: "POST",
	                    dataType: self.options.dataType,
	                    data: {
	                        query: request.term
	                    },
	                    success: function(data) {
	                    	if(data.users != undefined){
		                    	response($.map(data.users, function(item) {
		                            return {
		                                label: item.email +' - '+  item.fullname,
		                                value: item.email,
		                                id: item.id
		                            }
		                        }));
	                    	}
	                    }
	                })
	            },
				//selected index
	            select: function(event, ui) {
	            	var html = '<option selected="selected" value="'+ui.item.id+'">'+ui.item.value+'</option>';
	            	$('#user').html(html);
	            },
            }).addClass("ui-widget ui-widget-content ui-corner-left");
        
        input.data("autocomplete")._renderItem = function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append("<a>" + item.label + "</a>").appendTo(ul);
        };
        
        
        this.button = $("<button type='button'>&nbsp;</button>").attr("tabIndex", -1).attr("title", "Show All Items").insertAfter(input).button({
            icons: {
                primary: "ui-icon-triangle-1-s"
            },
            text: false
        }).removeClass("ui-corner-all").addClass("ui-corner-right ui-button-icon").click(function() {
            // close if already visible
            if (input.autocomplete("widget").is(":visible")) {
                input.autocomplete("close");
                return;
            }

            // work around a bug (likely same cause as #5265)
            $(this).blur();

            // pass empty string as value to search for, displaying all results
            input.autocomplete("search", input.val());
            input.focus();
        });
    },

    destroy: function() {
        this.input.remove();
        this.button.remove();
        this.element.show();
        $.Widget.prototype.destroy.call(this);
    }
});

$.widget("ui.resourcecombobox", {
	options:{
			dataSource: "",
			dataType: "",
			minLength: 0
		},
    _create: function() {
        var self = this,
            select = this.element.hide(),
            selected = select.children(":selected"),
            value = selected.val() ? selected.text() : "";
            var input = this.input = $("<input id=\"resourcecombobox\">").insertAfter(select).val(value).autocomplete({
	            delay: 0,
	            minLength: self.options.minLength,
	            source: function(request, response) {
	                $.ajax({
	                    url: self.options.dataSource,
	                    type: "POST",
	                    dataType: self.options.dataType,
	                    data: {
	                        query: request.term,
	                        type: $('#type option:selected').val()
	                    },
	                    success: function(data) {
	                    	if(data.resource != undefined){
		                    	response($.map(data.resource, function(item) {
		                            return {
		                                label: item.title,
		                                value: item.title,
		                                id: item.id
		                            }
		                        }));
	                    	}
	                    }
	                })
	            },
				//selected index
	            select: function(event, ui) {
	            	var html = '<option selected="selected" value="'+ui.item.id+'">'+ui.item.value+'</option>';
	            	$('#resource').html(html);
	            },
            }).addClass("ui-widget ui-widget-content ui-corner-left");
        
        input.data("autocomplete")._renderItem = function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append("<a>" + item.label + "</a>").appendTo(ul);
        };
        
        this.button = $("<button type='button'>&nbsp;</button>").attr("tabIndex", -1).attr("title", "Show All Items").insertAfter(input).button({
            icons: {
                primary: "ui-icon-triangle-1-s"
            },
            text: false
        }).removeClass("ui-corner-all").addClass("ui-corner-right ui-button-icon").click(function() {
            // close if already visible
            if (input.autocomplete("widget").is(":visible")) {
                input.autocomplete("close");
                return;
            }

            // work around a bug (likely same cause as #5265)
            $(this).blur();

            // pass empty string as value to search for, displaying all results
            input.autocomplete("search", input.val());
            input.focus();
        });
    },

    destroy: function() {
        this.input.remove();
        this.button.remove();
        this.element.show();
        $.Widget.prototype.destroy.call(this);
    }
});