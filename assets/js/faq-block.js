( function ( wp ) {
	if ( ! wp || ! wp.blocks || ! wp.element ) {
		return;
	}

	var registerBlockType = wp.blocks.registerBlockType;
	var el                = wp.element.createElement;
	var Fragment          = wp.element.Fragment;
	var __                = wp.i18n && wp.i18n.__ ? wp.i18n.__ : function ( s ) { return s; };
	var InspectorControls = wp.blockEditor ? wp.blockEditor.InspectorControls : wp.editor.InspectorControls;
	var PanelBody         = wp.components.PanelBody;
	var TextControl       = wp.components.TextControl;
	var ServerSideRender  = wp.serverSideRender;

	registerBlockType( 'woo-faq/product-faq', {
		title: __( 'Product FAQ Accordion', 'product-faq-for-woocommerce' ),
		description: __( 'Display responsive WooCommerce Product FAQs with automatic Google FAQPage JSON-LD schema.', 'product-faq-for-woocommerce' ),
		icon: 'editor-help',
		category: 'woocommerce',
		keywords: [
			__( 'faq', 'product-faq-for-woocommerce' ),
			__( 'woocommerce', 'product-faq-for-woocommerce' ),
			__( 'product faq', 'product-faq-for-woocommerce' ),
			__( 'accordion', 'product-faq-for-woocommerce' )
		],
		attributes: {
			productId: {
				type: 'string',
				default: ''
			},
			category: {
				type: 'string',
				default: ''
			},
			title: {
				type: 'string',
				default: ''
			}
		},
		edit: function ( props ) {
			var attributes    = props.attributes;
			var setAttributes = props.setAttributes;

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{
							title: __( 'FAQ Source & Display Settings', 'product-faq-for-woocommerce' ),
							initialOpen: true
						},
						el( TextControl, {
							label: __( 'Custom Heading (Optional)', 'product-faq-for-woocommerce' ),
							help: __( 'Leave blank to use your default heading from Product FAQ → Settings.', 'product-faq-for-woocommerce' ),
							value: attributes.title,
							placeholder: __( 'Frequently Asked Questions', 'product-faq-for-woocommerce' ),
							onChange: function ( val ) {
								setAttributes( { title: val } );
							}
						} ),
						el( TextControl, {
							label: __( 'Specific Product ID (Optional)', 'product-faq-for-woocommerce' ),
							help: __( 'Leave blank on Single Product templates to auto-detect the current product, or enter a WooCommerce Product ID (e.g. 123).', 'product-faq-for-woocommerce' ),
							value: attributes.productId,
							placeholder: __( 'Auto-detect current product', 'product-faq-for-woocommerce' ),
							onChange: function ( val ) {
								setAttributes( { productId: val.replace( /[^0-9]/g, '' ) } );
							}
						} ),
						el( TextControl, {
							label: __( 'Product Category Slug or ID (Optional)', 'product-faq-for-woocommerce' ),
							help: __( 'Filter general page FAQs to a specific WooCommerce category slug (e.g. hoodies) or term ID.', 'product-faq-for-woocommerce' ),
							value: attributes.category,
							placeholder: __( 'e.g. hoodies', 'product-faq-for-woocommerce' ),
							onChange: function ( val ) {
								setAttributes( { category: val } );
							}
						} )
					)
				),
				el(
					'div',
					{ className: 'woo-faq-block-editor-preview' },
					ServerSideRender
						? el( ServerSideRender, {
								block: 'woo-faq/product-faq',
								attributes: attributes
						  } )
						: el(
								'div',
								{
									style: {
										padding: '16px',
										border: '1px solid #e2e8f0',
										borderRadius: '8px',
										background: '#f8fafc'
									}
								},
								__( 'Product FAQ Accordion will render on the frontend.', 'product-faq-for-woocommerce' )
						  )
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp );
