(function( $ ) {
	$.widget( "ui.combobox", {
		_create: function() {
			var input,
				self = this,
				select = this.element.hide(),
				selected = select.children( ":selected" ),
				value = selected.val() ? selected.text() : "",
				wrapper = this.wrapper = $( "<span>" )
					.addClass( "ui-combobox" )
					.insertAfter( select );
				input = $( "<input>" )
					.appendTo( wrapper )
					.val( value )
					.attr('spellcheck', false)
					.addClass( "ui-combobox-input placeholder" )
					.autocomplete({
						delay: 0,
						minLength: 1,
						source: function( request, response ) {
							$.ajax({
							url: self.options.source,
							dataType: "json",
							data: {
								values: Search.getLookupValues(),
								target: select.attr('id'),
								lookup: request.term
							},
							error :function(jqXHR, textStatus, errorThrown) {
								alert(+textStatus+' '+errorThrown+' '+jqXHR.responseText);
							},
							complete :function (jqXHR, textStatus) {
								//
							},
							success: function( data ) {
								response( $.map( data, function( item ) {
									return {
										value: item.value,
										label: item.text
									}
								}));
							}
						});
					},

					select: function( event, ui ) {
						Search.setLookupValue($(select).attr('id'), ui.item.value);
						ui.item.selected = true;
						self.store=ui.item.label;
						self._trigger( "selected", event, {
							item: ui.item.label //option
						});
						//broadcast to original input
						self.element[0].dispatchEvent(new Event('change', { bubbles: true, cancelable: true } ));
					},

					change: function( event, ui ) {
						console.log('change');
						if ( !ui.item ) {
							var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
							valid = false;
							select.children( "option" ).each(function() {
								if ( $( this ).text().match( matcher ) ) {
									this.selected = valid = true;
									return false;
								}
							});
							if ( !valid ) {
								// remove invalid value, as it didn't match anything
								$( this ).val( "" );
								select.val( "" );
								input.data( "ui-autocomplete" ).term = "";
								return false;
							} 
						}
					}
				})
				.addClass( "ui-widget ui-widget-content ui-corner-left" );

			input.click(function() {
				Search.setLookupValue($(select).attr('id'), '');
				input.val('');
			});

			input.blur(function() {
				self.element[0].dispatchEvent(new Event('change', { bubbles: true, cancelable: true } ));
			});

			input.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.label + "</a>" )
					.appendTo( ul );
			};

			$( "<a>" )
				.attr( "tabIndex", -1 )
				.attr( "title", "Slå op i database" )
				.appendTo( wrapper )
				.button({
					icons: {
						primary: "ui-icon-triangle-1-s"
					},
					text: false
				})
				.removeClass( "ui-corner-all" )
				.addClass( "ui-corner-right ui-combobox-toggle" )
				.click(function() {
					// close if already visible
					if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
						input.autocomplete( "close" );
						return;
					}
					$( this ).blur();
					// pass empty string as value to search for, displaying all results
					// " " because of minlength=1
					input.autocomplete("search"," ");
					input.focus();
				});
		},
		destroy: function() {
			this.wrapper.remove();
			this.element.show();
			$.Widget.prototype.destroy.call( this );
		}
	});
})( jQuery );
