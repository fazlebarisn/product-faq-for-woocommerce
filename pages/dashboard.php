<?php
defined( 'ABSPATH' ) || die( 'Nice Try!' );

$is_pro_active  = in_array( 'woo-product-faq-pro/product-faq-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) ), true );
$export_url     = wp_nonce_url( admin_url( 'admin-post.php?action=woo_faq_export_json' ), 'woo_faq_export_nonce', 'woo_faq_nonce' );
$pro_tab_url    = woo_faq_utm_url( 'plugins/', 'pro-upgrade', 'settings-pro-tab', 'product-faq' );
$pro_field_url  = woo_faq_utm_url( 'plugins/', 'pro-upgrade', 'settings-locked-field', 'product-faq' );
$pro_chart_url  = woo_faq_utm_url( 'plugins/', 'pro-upgrade', 'analytics-preview', 'product-faq' );
$speed_audit_url = woo_faq_utm_url( 'speed-security/', 'agency-services', 'settings-footer-audit' );
$hire_us_url    = woo_faq_utm_url( 'hire-us/', 'agency-services', 'settings-footer-hire' );
?>
<div class="wrap fbs-faq-admin">
	<h1><?php esc_html_e( 'Product FAQ Settings', 'product-faq-for-woocommerce' ); ?></h1>
	<?php settings_errors(); ?>

	<form method="post" action="options.php">
		<?php settings_fields( 'woofaq-settings-group' ); ?>

		<div class="woo-faq-settings-wrap">
			<div class="woo-faq-settings-nav">
				<button type="button" class="nav-tab" data-target="tab-general"><?php esc_html_e( 'General', 'product-faq-for-woocommerce' ); ?></button>
				<button type="button" class="nav-tab" data-target="tab-design"><?php esc_html_e( 'Design', 'product-faq-for-woocommerce' ); ?></button>
				<button type="button" class="nav-tab" data-target="tab-ai"><?php esc_html_e( '🤖 AI Assistant', 'product-faq-for-woocommerce' ); ?></button>
				<button type="button" class="nav-tab" data-target="tab-tools"><?php esc_html_e( '💾 Backup & Tools', 'product-faq-for-woocommerce' ); ?></button>
				<?php do_action( 'woo_faq_settings_tabs' ); ?>
				<?php if ( ! $is_pro_active ) : ?>
					<button type="button" class="nav-tab" data-target="tab-analytics-mock"><?php esc_html_e( '📊 Analytics', 'product-faq-for-woocommerce' ); ?> <span class="nav-lock-icon" style="font-size: 10px; margin-left: 2px;">🔒</span></button>
					<button type="button" class="nav-tab" data-target="tab-pro"><?php esc_html_e( '🚀 Pro Features', 'product-faq-for-woocommerce' ); ?></button>
				<?php endif; ?>
			</div>

			<div class="woo-faq-settings-content">
				<div id="tab-general" class="tab-content">
					<h2><?php esc_html_e( 'General Settings', 'product-faq-for-woocommerce' ); ?></h2>
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Product FAQ', 'product-faq-for-woocommerce' ); ?></th>
							<td><?php $menu_instance->ProductFaq(); ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'FAQ Position', 'product-faq-for-woocommerce' ); ?></th>
							<td><?php $menu_instance->faqPosition(); ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'FAQ Heading', 'product-faq-for-woocommerce' ); ?></th>
							<td><?php $menu_instance->Heading(); ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'FAQ Count Badge', 'product-faq-for-woocommerce' ); ?></th>
							<td><?php $menu_instance->ShowCountBadge(); ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Expand First FAQ by Default', 'product-faq-for-woocommerce' ); ?></th>
							<td><?php $menu_instance->OpenFirstFaq(); ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Google FAQ Schema (JSON-LD)', 'product-faq-for-woocommerce' ); ?></th>
							<td><?php $menu_instance->EnableSchema(); ?></td>
						</tr>
						<?php if ( ! $is_pro_active ) : ?>
							<tr valign="top" class="pro-locked-row">
								<th scope="row" style="position: relative;">
									<?php esc_html_e( 'Live FAQ Search', 'product-faq-for-woocommerce' ); ?>
									<span class="pro-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; font-size: 9px; font-weight: 600; padding: 2px 6px; border-radius: 4px; margin-left: 5px; text-transform: uppercase;">PRO 🔒</span>
								</th>
								<td>
									<div class="pro-locked-field" style="opacity: 0.6; pointer-events: none;">
										<label class="switch-mock" style="position: relative; display: inline-block; width: 44px; height: 24px; margin-bottom: 5px;">
											<input type="checkbox" disabled checked style="opacity: 0; width: 0; height: 0;">
											<span class="slider-mock" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #667eea; transition: .4s; border-radius: 24px;"></span>
										</label>
									</div>
									<span class="pro-field-desc" style="display: block; font-size: 12px; color: #6b7280; margin-top: 5px;">
										<?php esc_html_e( 'Add a live-search bar above accordions to let shoppers filter questions instantly.', 'product-faq-for-woocommerce' ); ?>
										<a href="<?php echo esc_url( $pro_field_url ); ?>" target="_blank" rel="noopener noreferrer" style="color: #667eea; font-weight: 500; text-decoration: none;"><?php esc_html_e( 'Explore Pro ↗', 'product-faq-for-woocommerce' ); ?></a>
									</span>
								</td>
							</tr>
							<tr valign="top" class="pro-locked-row">
								<th scope="row" style="position: relative;">
									<?php esc_html_e( 'Accordion Toggle Icon', 'product-faq-for-woocommerce' ); ?>
									<span class="pro-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; font-size: 9px; font-weight: 600; padding: 2px 6px; border-radius: 4px; margin-left: 5px; text-transform: uppercase;">PRO 🔒</span>
								</th>
								<td>
									<div class="pro-locked-field" style="opacity: 0.6; pointer-events: none;">
										<select disabled class="select-mock" style="width: 100%; max-width: 400px; height: 42px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 15px; padding: 8px 16px; background: #ffffff;">
											<option><?php esc_html_e( 'Chevron Icon (Default)', 'product-faq-for-woocommerce' ); ?></option>
										</select>
									</div>
									<span class="pro-field-desc" style="display: block; font-size: 12px; color: #6b7280; margin-top: 5px;">
										<?php esc_html_e( 'Choose from premium toggle layouts like Plus/Minus, Arrows, or custom icons.', 'product-faq-for-woocommerce' ); ?>
										<a href="<?php echo esc_url( $pro_field_url ); ?>" target="_blank" rel="noopener noreferrer" style="color: #667eea; font-weight: 500; text-decoration: none;"><?php esc_html_e( 'Explore Pro ↗', 'product-faq-for-woocommerce' ); ?></a>
									</span>
								</td>
							</tr>
						<?php endif; ?>
					</table>
				</div>

				<div id="tab-design" class="tab-content">
					<h2><?php esc_html_e( 'Design Settings', 'product-faq-for-woocommerce' ); ?></h2>
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Heading Font Color', 'product-faq-for-woocommerce' ); ?></th>
							<td><?php $menu_instance->HeadingColor(); ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Question Font Color', 'product-faq-for-woocommerce' ); ?></th>
							<td><?php $menu_instance->QuestionColor(); ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Answer Font Color', 'product-faq-for-woocommerce' ); ?></th>
							<td><?php $menu_instance->AnswerColor(); ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Heading Font Size', 'product-faq-for-woocommerce' ); ?></th>
							<td><?php $menu_instance->HeadingFontSize(); ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Question Font Size', 'product-faq-for-woocommerce' ); ?></th>
							<td><?php $menu_instance->QuestionFontSize(); ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Answer Font Size', 'product-faq-for-woocommerce' ); ?></th>
							<td><?php $menu_instance->AnswerFontSize(); ?></td>
						</tr>
						<?php if ( ! $is_pro_active ) : ?>
							<tr valign="top" class="pro-locked-row">
								<th scope="row" style="position: relative;">
									<?php esc_html_e( 'Active Accordion BG', 'product-faq-for-woocommerce' ); ?>
									<span class="pro-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; font-size: 9px; font-weight: 600; padding: 2px 6px; border-radius: 4px; margin-left: 5px; text-transform: uppercase;">PRO 🔒</span>
								</th>
								<td>
									<div class="pro-locked-field" style="opacity: 0.6; pointer-events: none;">
										<input type="text" class="color-field-mock" value="#e8edff" disabled style="background-color: #e8edff; border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 8px; width: 80px; font-weight: 500;">
									</div>
									<span class="pro-field-desc" style="display: block; font-size: 12px; color: #6b7280; margin-top: 5px;">
										<?php esc_html_e( 'Customize background color of expanded/active FAQ items.', 'product-faq-for-woocommerce' ); ?>
										<a href="<?php echo esc_url( $pro_field_url ); ?>" target="_blank" rel="noopener noreferrer" style="color: #667eea; font-weight: 500; text-decoration: none;"><?php esc_html_e( 'Explore Pro ↗', 'product-faq-for-woocommerce' ); ?></a>
									</span>
								</td>
							</tr>
							<tr valign="top" class="pro-locked-row">
								<th scope="row" style="position: relative;">
									<?php esc_html_e( 'Active Accordion Text', 'product-faq-for-woocommerce' ); ?>
									<span class="pro-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; font-size: 9px; font-weight: 600; padding: 2px 6px; border-radius: 4px; margin-left: 5px; text-transform: uppercase;">PRO 🔒</span>
								</th>
								<td>
									<div class="pro-locked-field" style="opacity: 0.6; pointer-events: none;">
										<input type="text" class="color-field-mock" value="#2271b1" disabled style="background-color: #2271b1; color: #ffffff; border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 8px; width: 80px; font-weight: 500;">
									</div>
									<span class="pro-field-desc" style="display: block; font-size: 12px; color: #6b7280; margin-top: 5px;">
										<?php esc_html_e( 'Customize text/title color of expanded/active FAQ items.', 'product-faq-for-woocommerce' ); ?>
										<a href="<?php echo esc_url( $pro_field_url ); ?>" target="_blank" rel="noopener noreferrer" style="color: #667eea; font-weight: 500; text-decoration: none;"><?php esc_html_e( 'Explore Pro ↗', 'product-faq-for-woocommerce' ); ?></a>
									</span>
								</td>
							</tr>
							<tr valign="top" class="pro-locked-row">
								<th scope="row" style="position: relative;">
									<?php esc_html_e( 'Card Border Radius', 'product-faq-for-woocommerce' ); ?>
									<span class="pro-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; font-size: 9px; font-weight: 600; padding: 2px 6px; border-radius: 4px; margin-left: 5px; text-transform: uppercase;">PRO 🔒</span>
								</th>
								<td>
									<div class="pro-locked-field" style="opacity: 0.6; pointer-events: none;">
										<input type="text" value="8px" disabled style="width: 80px; text-align: center; border-radius: 4px; border: 1px solid #d1d5db; padding: 4px 8px;">
									</div>
									<span class="pro-field-desc" style="display: block; font-size: 12px; color: #6b7280; margin-top: 5px;">
										<?php esc_html_e( 'Round the corners of your accordion cards for a modern container layout.', 'product-faq-for-woocommerce' ); ?>
										<a href="<?php echo esc_url( $pro_field_url ); ?>" target="_blank" rel="noopener noreferrer" style="color: #667eea; font-weight: 500; text-decoration: none;"><?php esc_html_e( 'Explore Pro ↗', 'product-faq-for-woocommerce' ); ?></a>
									</span>
								</td>
							</tr>
						<?php endif; ?>
					</table>
				</div>

				<div id="tab-ai" class="tab-content">
					<h2><?php esc_html_e( '🤖 AI FAQ Assistant Settings', 'product-faq-for-woocommerce' ); ?></h2>
					<p style="color: #4b5563; font-size: 14px; margin-bottom: 20px;">
						<?php esc_html_e( 'Uses WordPress’s native AI Client & Connectors to generate accurate, spec-aware FAQs from your WooCommerce product attributes, dimensions, price, and description in 1 click.', 'product-faq-for-woocommerce' ); ?>
					</p>
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'WordPress AI Connection', 'product-faq-for-woocommerce' ); ?></th>
							<td><?php $menu_instance->AiConnectorStatus(); ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Default Generation Tone', 'product-faq-for-woocommerce' ); ?></th>
							<td><?php $menu_instance->AiTone(); ?></td>
						</tr>
					</table>

					<div style="margin-top: 25px; padding: 18px; border-radius: 10px; background: linear-gradient(135deg, #f0f4ff 0%, #faf5ff 100%); border: 1px solid #e0e7ff; max-width: 650px;">
						<h4 style="margin: 0 0 8px 0; color: #3730a3; font-size: 15px; display: flex; align-items: center; gap: 6px;">
							<span>✨</span> <?php esc_html_e( 'WooCommerce Spec-Aware AI Generation (New in v1.6)', 'product-faq-for-woocommerce' ); ?>
						</h4>
						<p style="margin: 0; color: #4338ca; font-size: 13px; line-height: 1.55;">
							<?php
							echo wp_kses(
								__( 'When editing any WooCommerce product, open the <strong>FAQ</strong> tab and click <strong>"✨ Generate with AI"</strong>. The AI automatically reads your product’s <strong>Price, Stock Status, Weight &amp; Dimensions, Categories, and Product Attributes</strong> (e.g. Material, Size, Color) to craft factual, conversion-ready answers.', 'product-faq-for-woocommerce' ),
								[ 'strong' => [] ]
							);
							?>
						</p>
					</div>
				</div>

				<div id="tab-tools" class="tab-content">
					<h2><?php esc_html_e( '💾 Backup, Export & Import (JSON)', 'product-faq-for-woocommerce' ); ?></h2>
					<p style="color: #4b5563; font-size: 14px; margin-bottom: 24px;">
						<?php esc_html_e( 'Easily back up or migrate your Product FAQ design settings and Bulk Category/Tag FAQ groups between staging and production stores.', 'product-faq-for-woocommerce' ); ?>
					</p>

					<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 20px; max-width: 820px;">
						<!-- Export Card -->
						<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px;">
							<h3 style="margin: 0 0 8px; font-size: 16px; color: #0f172a;">⬇️ <?php esc_html_e( 'Export Configuration Backup', 'product-faq-for-woocommerce' ); ?></h3>
							<p style="margin: 0 0 18px; font-size: 13px; color: #475569; line-height: 1.55;">
								<?php esc_html_e( 'Download a clean .json file containing all your plugin settings, typography/colors, and Bulk FAQ category/tag groups.', 'product-faq-for-woocommerce' ); ?>
							</p>
							<a href="<?php echo esc_url( $export_url ); ?>" class="button button-secondary" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; padding: 6px 16px; height: auto;">
								<?php esc_html_e( '⬇️ Download JSON Backup', 'product-faq-for-woocommerce' ); ?>
							</a>
						</div>

						<!-- Import Card -->
						<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px;">
							<h3 style="margin: 0 0 8px; font-size: 16px; color: #0f172a;">⬆️ <?php esc_html_e( 'Import Configuration Backup', 'product-faq-for-woocommerce' ); ?></h3>
							<p style="margin: 0 0 14px; font-size: 13px; color: #475569; line-height: 1.55;">
								<?php esc_html_e( 'Select a previously exported Product FAQ .json file to restore settings and Bulk FAQ groups in 1 click.', 'product-faq-for-woocommerce' ); ?>
							</p>
							<input type="file" id="woo-faq-import-file" accept=".json,application/json" style="display: block; margin-bottom: 12px; font-size: 13px; max-width: 100%;" />
							<button type="button" id="woo-faq-import-btn" class="button button-secondary" style="font-weight: 600; padding: 6px 16px; height: auto;">
								<?php esc_html_e( '⬆️ Import JSON Backup', 'product-faq-for-woocommerce' ); ?>
							</button>
							<p id="woo-faq-import-status" style="margin: 10px 0 0; font-size: 12.5px; font-weight: 600;"></p>
						</div>
					</div>
				</div>

				<?php if ( ! $is_pro_active ) : ?>
					<div id="tab-analytics-mock" class="tab-content">
						<h2><?php esc_html_e( '📊 FAQ Engagement Analytics', 'product-faq-for-woocommerce' ); ?></h2>
						<div class="analytics-mock-container" style="position: relative; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; padding: 20px; background: #ffffff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
							<div class="mock-stats-grid" style="display: flex; gap: 15px; margin-bottom: 25px;">
								<div class="mock-stat-card" style="flex: 1; padding: 15px; border-radius: 8px; background: #f9fafb; border: 1px solid #f3f4f6;">
									<div style="font-size: 13px; color: #6b7280; font-weight: 500;"><?php esc_html_e( 'Total FAQ Views', 'product-faq-for-woocommerce' ); ?></div>
									<div style="font-size: 24px; font-weight: 600; color: #1f2937; margin-top: 5px;">12,840</div>
									<div style="font-size: 11px; color: #10b981; font-weight: 500; margin-top: 5px;">▲ 14.2% since last week</div>
								</div>
								<div class="mock-stat-card" style="flex: 1; padding: 15px; border-radius: 8px; background: #f9fafb; border: 1px solid #f3f4f6;">
									<div style="font-size: 13px; color: #6b7280; font-weight: 500;"><?php esc_html_e( 'Top FAQ Conversions', 'product-faq-for-woocommerce' ); ?></div>
									<div style="font-size: 24px; font-weight: 600; color: #1f2937; margin-top: 5px;">84.5%</div>
									<div style="font-size: 11px; color: #10b981; font-weight: 500; margin-top: 5px;">▲ 2.8% read completion</div>
								</div>
								<div class="mock-stat-card" style="flex: 1; padding: 15px; border-radius: 8px; background: #f9fafb; border: 1px solid #f3f4f6;">
									<div style="font-size: 13px; color: #6b7280; font-weight: 500;"><?php esc_html_e( 'Reduced Support Tickets', 'product-faq-for-woocommerce' ); ?></div>
									<div style="font-size: 24px; font-weight: 600; color: #1f2937; margin-top: 5px;">32% Est.</div>
									<div style="font-size: 11px; color: #10b981; font-weight: 500; margin-top: 5px;">▲ 124 support tickets saved</div>
								</div>
							</div>

							<div class="mock-chart-wrapper" style="height: 250px; background: #f9fafb; border-radius: 8px; border: 1px solid #f3f4f6; display: flex; align-items: flex-end; padding: 20px; position: relative;">
								<svg viewBox="0 0 500 150" style="width: 100%; height: 100%; position: absolute; left: 0; bottom: 0;">
									<defs>
										<linearGradient id="mock-grad" x1="0%" y1="0%" x2="0%" y2="100%">
											<stop offset="0%" stop-color="rgba(102, 126, 234, 0.25)" />
											<stop offset="100%" stop-color="rgba(102, 126, 234, 0.0)" />
										</linearGradient>
									</defs>
									<path d="M0 130 C 50 110, 100 80, 150 95 C 200 110, 250 40, 300 60 C 350 80, 400 30, 450 15 C 480 8, 500 5, 500 5 L500 150 L0 150 Z" fill="url(#mock-grad)"></path>
									<path d="M0 130 C 50 110, 100 80, 150 95 C 200 110, 250 40, 300 60 C 350 80, 400 30, 450 15 C 480 8, 500 5, 500 5" fill="none" stroke="rgba(102, 126, 234, 0.8)" stroke-width="3"></path>
								</svg>
								<div style="font-size: 11px; color: #9ca3af; position: absolute; left: 10px; top: 10px; font-weight: 500;"><?php esc_html_e( 'FAQ Views (Daily)', 'product-faq-for-woocommerce' ); ?></div>
							</div>

							<div class="analytics-mock-overlay" style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.72); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); display: flex; align-items: center; justify-content: center;">
								<div class="mock-upgrade-card" style="width: 390px; background: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); padding: 25px; text-align: center; box-sizing: border-box;">
									<div style="font-size: 32px; margin-bottom: 12px;">📊</div>
									<h4 style="font-size: 18px; font-weight: 600; color: #1f2937; margin: 0 0 10px 0;"><?php esc_html_e( 'Unlock FAQ Engagement Analytics', 'product-faq-for-woocommerce' ); ?></h4>
									<p style="font-size: 13px; color: #6b7280; line-height: 1.5; margin: 0 0 20px 0;">
										<?php esc_html_e( 'Track views, expansion rates, and see which product questions your customers read most to boost store conversions.', 'product-faq-for-woocommerce' ); ?>
									</p>
									<a href="<?php echo esc_url( $pro_chart_url ); ?>" target="_blank" rel="noopener noreferrer" class="button-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; text-decoration: none; border-radius: 8px; padding: 10px 24px; color: #ffffff !important; font-weight: 600; font-size: 14px; display: inline-block; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3) !important; border: none; cursor: pointer;">
										<?php esc_html_e( 'Explore Product FAQ Pro ↗', 'product-faq-for-woocommerce' ); ?>
									</a>
								</div>
							</div>
						</div>
					</div>

					<div id="tab-pro" class="tab-content">
						<h2><?php esc_html_e( '🚀 Upgrade to Product FAQ Pro', 'product-faq-for-woocommerce' ); ?></h2>
						<div class="pro-features-container">
							<div class="pro-hero-section">
								<div class="pro-hero-content">
									<h3><?php esc_html_e( 'Turn Pre-Sale Questions Into Instant WooCommerce Sales', 'product-faq-for-woocommerce' ); ?></h3>
									<p><?php esc_html_e( 'Unlock unlimited FAQs, Customer "Ask a Question" Q&A modals with AI auto-draft answers, Bulk Category AI generation, Live Instant Search, Archive Page FAQs, and Engagement Analytics.', 'product-faq-for-woocommerce' ); ?></p>
									<a href="<?php echo esc_url( $pro_tab_url ); ?>" target="_blank" rel="noopener noreferrer" class="pro-upgrade-button">
										<span class="pro-button-text"><?php esc_html_e( 'View Pro Features & Pricing ↗', 'product-faq-for-woocommerce' ); ?></span>
									</a>
								</div>
								<div class="pro-hero-image">
									<div class="pro-icon">⭐</div>
								</div>
							</div>

							<div class="pro-features-grid">
								<div class="pro-feature-card">
									<div class="pro-feature-icon">💬</div>
									<h4><?php esc_html_e( 'Customer Q&A + AI Auto-Draft', 'product-faq-for-woocommerce' ); ?></h4>
									<p><?php esc_html_e( 'Let shoppers submit questions right on product pages with instant AI-drafted answers ready for 1-click approval.', 'product-faq-for-woocommerce' ); ?></p>
								</div>

								<div class="pro-feature-card">
									<div class="pro-feature-icon">🔍</div>
									<h4><?php esc_html_e( 'Live Instant FAQ Search', 'product-faq-for-woocommerce' ); ?></h4>
									<p><?php esc_html_e( 'Add real-time search filtering above your FAQ accordions so shoppers find answers in milliseconds.', 'product-faq-for-woocommerce' ); ?></p>
								</div>

								<div class="pro-feature-card">
									<div class="pro-feature-icon">⚡</div>
									<h4><?php esc_html_e( 'Bulk Category AI Generator', 'product-faq-for-woocommerce' ); ?></h4>
									<p><?php esc_html_e( 'Generate tailored FAQs across entire WooCommerce product categories and unlimited FAQ groups.', 'product-faq-for-woocommerce' ); ?></p>
								</div>

								<div class="pro-feature-card">
									<div class="pro-feature-icon">🛍️</div>
									<h4><?php esc_html_e( 'Shop & Category Archive FAQs', 'product-faq-for-woocommerce' ); ?></h4>
									<p><?php esc_html_e( 'Render SEO-rich FAQ accordions directly on WooCommerce Shop, Category, and Tag archive pages.', 'product-faq-for-woocommerce' ); ?></p>
								</div>

								<div class="pro-feature-card">
									<div class="pro-feature-icon">🎨</div>
									<h4><?php esc_html_e( 'Advanced Card & Icon Styling', 'product-faq-for-woocommerce' ); ?></h4>
									<p><?php esc_html_e( 'Customize active background/text highlights, rounded card layouts, and custom toggle icons.', 'product-faq-for-woocommerce' ); ?></p>
								</div>

								<div class="pro-feature-card">
									<div class="pro-feature-icon">📊</div>
									<h4><?php esc_html_e( 'Engagement Analytics & Support', 'product-faq-for-woocommerce' ); ?></h4>
									<p><?php esc_html_e( 'See which FAQs shoppers click most, plus get direct priority support from FBS Fusion engineers.', 'product-faq-for-woocommerce' ); ?></p>
								</div>
							</div>

							<div class="pro-comparison-section">
								<h3><?php esc_html_e( 'Free vs Pro Comparison', 'product-faq-for-woocommerce' ); ?></h3>
								<div class="comparison-table">
									<div class="comparison-header">
										<div class="comparison-feature"><?php esc_html_e( 'Feature', 'product-faq-for-woocommerce' ); ?></div>
										<div class="comparison-free"><?php esc_html_e( 'Free (v1.6)', 'product-faq-for-woocommerce' ); ?></div>
										<div class="comparison-pro"><?php esc_html_e( 'Pro', 'product-faq-for-woocommerce' ); ?></div>
									</div>
									<div class="comparison-row">
										<div class="comparison-feature"><?php esc_html_e( 'Single Product FAQs, Rich HTML Answers & Gutenberg Block', 'product-faq-for-woocommerce' ); ?></div>
										<div class="comparison-free">✅ (Up to 3/product)</div>
										<div class="comparison-pro">✅ Unlimited</div>
									</div>
									<div class="comparison-row">
										<div class="comparison-feature"><?php esc_html_e( 'Spec-Aware AI FAQ Generator & Google FAQPage Schema', 'product-faq-for-woocommerce' ); ?></div>
										<div class="comparison-free">✅</div>
										<div class="comparison-pro">✅ + Bulk AI</div>
									</div>
									<div class="comparison-row">
										<div class="comparison-feature"><?php esc_html_e( 'Bulk Category / Tag FAQ Groups', 'product-faq-for-woocommerce' ); ?></div>
										<div class="comparison-free">✅ (Up to 2 groups)</div>
										<div class="comparison-pro">✅ Unlimited</div>
									</div>
									<div class="comparison-row">
										<div class="comparison-feature"><?php esc_html_e( 'Customer "Ask a Question" Q&A Portal + AI Auto-Draft', 'product-faq-for-woocommerce' ); ?></div>
										<div class="comparison-free">❌</div>
										<div class="comparison-pro">✅</div>
									</div>
									<div class="comparison-row">
										<div class="comparison-feature"><?php esc_html_e( 'Live Instant Search Bar & Shop/Category Archive FAQs', 'product-faq-for-woocommerce' ); ?></div>
										<div class="comparison-free">❌</div>
										<div class="comparison-pro">✅</div>
									</div>
									<div class="comparison-row">
										<div class="comparison-feature"><?php esc_html_e( 'FAQ Engagement Analytics & Priority Engineering Support', 'product-faq-for-woocommerce' ); ?></div>
										<div class="comparison-free">❌</div>
										<div class="comparison-pro">✅</div>
									</div>
								</div>
							</div>

							<div class="pro-cta-section">
								<h3><?php esc_html_e( 'Ready to Unlock the Complete Suite?', 'product-faq-for-woocommerce' ); ?></h3>
								<p><?php esc_html_e( 'Visit FBS Fusion to compare Free vs Pro and explore our full WooCommerce growth & optimization suite.', 'product-faq-for-woocommerce' ); ?></p>
								<a href="<?php echo esc_url( $pro_tab_url ); ?>" target="_blank" rel="noopener noreferrer" class="pro-cta-button">
									<?php esc_html_e( 'Explore Product FAQ Pro on FBSFusion.com ↗', 'product-faq-for-woocommerce' ); ?>
								</a>
								<div class="pro-guarantee">
									<span class="guarantee-icon">🛡️</span>
									<span><?php esc_html_e( '30-day money-back guarantee • Instant download', 'product-faq-for-woocommerce' ); ?></span>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php do_action( 'woo_faq_settings_tab_contents' ); ?>
			</div>
		</div>

		<?php submit_button(); ?>
	</form>

	<!-- Quiet, Non-Intrusive Studio Services Card (Scoped strictly to bottom of Plugin Settings) -->
	<div style="margin-top: 28px; background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); border-radius: 14px; padding: 22px 26px; color: #f8fafc; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);">
		<div style="max-width: 680px;">
			<span style="display: inline-block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #34d399; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); padding: 3px 10px; border-radius: 999px; margin-bottom: 8px;">
				<?php esc_html_e( 'Built by FBS Fusion Engineering', 'product-faq-for-woocommerce' ); ?>
			</span>
			<h3 style="margin: 0 0 6px; font-size: 17px; font-weight: 700; color: #ffffff;">
				<?php esc_html_e( 'Need Sub-Second WooCommerce Speed, 502 High-Traffic Rescue, or Custom Features?', 'product-faq-for-woocommerce' ); ?>
			</h3>
			<p style="margin: 0; font-size: 13.5px; color: #cbd5e1; line-height: 1.55;">
				<?php esc_html_e( 'Work directly with the engineers behind Product FAQ for WooCommerce. We help store owners hit 95–100 PageSpeed scores, eradicate hidden malware, and build custom WooCommerce & Laravel solutions.', 'product-faq-for-woocommerce' ); ?>
			</p>
		</div>
		<div style="display: flex; gap: 10px; flex-wrap: wrap;">
			<a href="<?php echo esc_url( $speed_audit_url ); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px; border-radius: 8px; background: #10b981; color: #052e16; font-size: 13px; font-weight: 700; text-decoration: none;">
				<?php esc_html_e( '⚡ Free Store Speed Audit ↗', 'product-faq-for-woocommerce' ); ?>
			</a>
			<a href="<?php echo esc_url( $hire_us_url ); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px; border-radius: 8px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.22); color: #ffffff; font-size: 13px; font-weight: 600; text-decoration: none;">
				<?php esc_html_e( '🛠️ Custom WooCommerce Dev ↗', 'product-faq-for-woocommerce' ); ?>
			</a>
		</div>
	</div>
</div>
