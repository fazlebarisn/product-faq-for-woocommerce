<?php
defined( 'ABSPATH' ) || die( 'Nice Try!' );

$suite_url  = woo_faq_utm_url( 'plugins/', 'plugin-cross-promo', 'our-plugins-header' );
$speed_url  = woo_faq_utm_url( 'speed-security/', 'agency-services', 'our-plugins-banner' );
$hire_url   = woo_faq_utm_url( 'hire-us/', 'agency-services', 'our-plugins-hire' );
$faq_pro    = woo_faq_utm_url( 'plugins/', 'pro-upgrade', 'our-plugins-card', 'product-faq' );
$stock_pro  = woo_faq_utm_url( 'plugins/', 'pro-upgrade', 'our-plugins-stockmind', 'fbs-stockmind' );
?>
<div class="wrap fbs-plugins-wrap">
	<style>
		.wp-core-ui .fbs-plugins-wrap {
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
			padding: 20px 20px 30px 0 !important;
			max-width: 1240px !important;
			box-sizing: border-box !important;
		}
		.wp-core-ui .fbs-plugins-header {
			display: flex !important;
			align-items: flex-end !important;
			justify-content: space-between !important;
			gap: 20px !important;
			flex-wrap: wrap !important;
			margin-bottom: 2rem !important;
		}
		.wp-core-ui .fbs-plugins-header h1 {
			font-size: 2rem !important;
			font-weight: 800 !important;
			margin: 0 0 0.5rem 0 !important;
			color: #0f172a !important;
		}
		.wp-core-ui .fbs-plugins-header p {
			font-size: 1rem !important;
			color: #475569 !important;
			margin: 0 !important;
			max-width: 680px !important;
			line-height: 1.6 !important;
		}
		.wp-core-ui .fbs-plugins-grid {
			display: grid !important;
			grid-template-columns: repeat(auto-fit, minmax(330px, 1fr)) !important;
			gap: 22px !important;
			box-sizing: border-box !important;
		}
		.wp-core-ui .fbs-plugin-card {
			background: #ffffff !important;
			border: 1px solid #e2e8f0 !important;
			border-radius: 16px !important;
			overflow: hidden !important;
			box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04) !important;
			transition: all 0.22s ease !important;
			display: flex !important;
			flex-direction: column !important;
			box-sizing: border-box !important;
		}
		.wp-core-ui .fbs-plugin-card:hover {
			transform: translateY(-3px) !important;
			box-shadow: 0 14px 24px -6px rgba(15, 23, 42, 0.1) !important;
			border-color: #cbd5e1 !important;
		}
		.wp-core-ui .fbs-plugin-card--featured {
			border: 2px solid #10b981 !important;
		}
		.wp-core-ui .fbs-plugin-card-header {
			padding: 26px 24px 16px 24px !important;
			display: flex !important;
			align-items: center !important;
			gap: 14px !important;
		}
		.wp-core-ui .fbs-plugin-icon-wrap {
			width: 52px !important;
			height: 52px !important;
			border-radius: 12px !important;
			display: flex !important;
			align-items: center !important;
			justify-content: center !important;
			flex-shrink: 0 !important;
		}
		.wp-core-ui .fbs-plugin-icon-wrap svg {
			width: 26px !important;
			height: 26px !important;
		}
		.wp-core-ui .fbs-plugin-card-header h3 {
			font-size: 1.15rem !important;
			font-weight: 700 !important;
			color: #0f172a !important;
			margin: 0 !important;
			line-height: 1.35 !important;
		}
		.wp-core-ui .fbs-plugin-badge {
			display: inline-block !important;
			font-size: 10.5px !important;
			font-weight: 700 !important;
			padding: 2px 8px !important;
			border-radius: 999px !important;
			background: #ecfdf5 !important;
			color: #047857 !important;
			border: 1px solid #a7f3d0 !important;
			margin-top: 4px !important;
		}
		.wp-core-ui .fbs-plugin-card-body {
			padding: 0 24px 20px 24px !important;
			flex-grow: 1 !important;
		}
		.wp-core-ui .fbs-plugin-card-body p {
			font-size: 0.92rem !important;
			color: #475569 !important;
			line-height: 1.6 !important;
			margin: 0 !important;
		}
		.wp-core-ui .fbs-plugin-card-footer {
			padding: 0 24px 24px 24px !important;
			margin-top: auto !important;
			display: flex !important;
			gap: 10px !important;
		}
		.wp-core-ui .fbs-plugin-btn {
			display: inline-flex !important;
			align-items: center !important;
			justify-content: center !important;
			gap: 6px !important;
			flex: 1 !important;
			padding: 10px 14px !important;
			border-radius: 8px !important;
			font-size: 0.875rem !important;
			font-weight: 600 !important;
			text-decoration: none !important;
			box-sizing: border-box !important;
			transition: all 0.18s ease !important;
		}
		.wp-core-ui .fbs-plugin-btn--outline {
			background: #f8fafc !important;
			color: #1e293b !important;
			border: 1px solid #cbd5e1 !important;
		}
		.wp-core-ui .fbs-plugin-btn--outline:hover {
			background: #f1f5f9 !important;
			border-color: #94a3b8 !important;
		}

		.wp-core-ui .bg-secure { background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important; }
		.wp-core-ui .btn-secure { background: #10b981 !important; color: #052e16 !important; }
		.wp-core-ui .btn-secure:hover { background: #059669 !important; color: #ffffff !important; }

		.wp-core-ui .bg-stockmind { background: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%) !important; }
		.wp-core-ui .btn-stockmind { background: #0284c7 !important; color: #ffffff !important; }
		.wp-core-ui .btn-stockmind:hover { background: #0369a1 !important; }

		.wp-core-ui .bg-faq { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }
		.wp-core-ui .btn-faq { background: #4f46e5 !important; color: #ffffff !important; }
		.wp-core-ui .btn-faq:hover { background: #4338ca !important; }

		.wp-core-ui .bg-activity { background: linear-gradient(135deg, #4776e6 0%, #8e54e9 100%) !important; }
		.wp-core-ui .btn-activity { background: #6d28d9 !important; color: #ffffff !important; }
		.wp-core-ui .btn-activity:hover { background: #5b21b6 !important; }

		.wp-core-ui .bg-table { background: linear-gradient(135deg, #e11d48 0%, #fb7185 100%) !important; }
		.wp-core-ui .btn-table { background: #e11d48 !important; color: #ffffff !important; }
		.wp-core-ui .btn-table:hover { background: #be123c !important; }

		.wp-core-ui .bg-payout { background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%) !important; }
		.wp-core-ui .btn-payout { background: #0d9488 !important; color: #ffffff !important; }
		.wp-core-ui .btn-payout:hover { background: #0f766e !important; }
	</style>

	<div class="fbs-plugins-header">
		<div>
			<h1><?php esc_html_e( 'Official FBS Fusion Plugin Suite', 'product-faq-for-woocommerce' ); ?></h1>
			<p><?php esc_html_e( 'Explore our WordPress.org & Pro WooCommerce utilities — engineered for sub-second Core Web Vitals, fortress security, and higher store conversions.', 'product-faq-for-woocommerce' ); ?></p>
		</div>
		<a href="<?php echo esc_url( $suite_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary" style="background: #0f172a; border-color: #0f172a; border-radius: 8px; padding: 6px 16px; font-weight: 600;">
			<?php esc_html_e( 'View Full Suite on FBSFusion.com ↗', 'product-faq-for-woocommerce' ); ?>
		</a>
	</div>

	<div class="fbs-plugins-grid">

		<!-- Featured: FBS Secure Optimize -->
		<div class="fbs-plugin-card fbs-plugin-card--featured">
			<div class="fbs-plugin-card-header">
				<div class="fbs-plugin-icon-wrap bg-secure">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
						<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
				<div>
					<h3><?php esc_html_e( 'FBS Secure Optimize', 'product-faq-for-woocommerce' ); ?></h3>
					<span class="fbs-plugin-badge"><?php esc_html_e( '⚡ Recommended for WooCommerce Speed & Security', 'product-faq-for-woocommerce' ); ?></span>
				</div>
			</div>
			<div class="fbs-plugin-card-body">
				<p><?php esc_html_e( 'All-in-one Core Web Vitals & Security suite. Includes full-page HTML caching, 1-click Purge Cache, non-blocking Google Fonts, zero-CLS image dimensions, WAF firewall, login protection, and forensic malware scanning.', 'product-faq-for-woocommerce' ); ?></p>
			</div>
			<div class="fbs-plugin-card-footer">
				<a href="https://wordpress.org/plugins/fbs-secure-optimize/" target="_blank" rel="noopener noreferrer" class="fbs-plugin-btn btn-secure">
					<?php esc_html_e( 'Install Free on WP.org ↗', 'product-faq-for-woocommerce' ); ?>
				</a>
			</div>
		</div>

		<!-- Product FAQ for WooCommerce & Pro -->
		<div class="fbs-plugin-card">
			<div class="fbs-plugin-card-header">
				<div class="fbs-plugin-icon-wrap bg-faq">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
						<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
				<div>
					<h3><?php esc_html_e( 'Product FAQ for WooCommerce', 'product-faq-for-woocommerce' ); ?></h3>
					<span class="fbs-plugin-badge"><?php esc_html_e( '✔ Installed • Pro Available', 'product-faq-for-woocommerce' ); ?></span>
				</div>
			</div>
			<div class="fbs-plugin-card-body">
				<p><?php esc_html_e( 'Spec-aware AI FAQ generation, Google FAQPage JSON-LD schema, Gutenberg block, and rich HTML accordions. Upgrade to Pro for Customer Q&A, Live Search, Bulk AI, and Analytics.', 'product-faq-for-woocommerce' ); ?></p>
			</div>
			<div class="fbs-plugin-card-footer">
				<a href="<?php echo esc_url( $faq_pro ); ?>" target="_blank" rel="noopener noreferrer" class="fbs-plugin-btn btn-faq">
					<?php esc_html_e( 'Explore Pro Features ↗', 'product-faq-for-woocommerce' ); ?>
				</a>
			</div>
		</div>

		<!-- FBS StockMind -->
		<div class="fbs-plugin-card">
			<div class="fbs-plugin-card-header">
				<div class="fbs-plugin-icon-wrap bg-stockmind">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
						<path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
				<h3><?php esc_html_e( 'FBS StockMind', 'product-faq-for-woocommerce' ); ?></h3>
			</div>
			<div class="fbs-plugin-card-body">
				<p><?php esc_html_e( 'AI Inventory Forecasting & Repeat Order Engine for WooCommerce. Predict stockout dates, chat with your live inventory via AI, send Purchase Orders, and automate customer reorder reminders.', 'product-faq-for-woocommerce' ); ?></p>
			</div>
			<div class="fbs-plugin-card-footer">
				<a href="https://wordpress.org/plugins/fbs-stockmind/" target="_blank" rel="noopener noreferrer" class="fbs-plugin-btn fbs-plugin-btn--outline">
					<?php esc_html_e( 'Free on WP.org ↗', 'product-faq-for-woocommerce' ); ?>
				</a>
				<a href="<?php echo esc_url( $stock_pro ); ?>" target="_blank" rel="noopener noreferrer" class="fbs-plugin-btn btn-stockmind">
					<?php esc_html_e( 'StockMind Pro ↗', 'product-faq-for-woocommerce' ); ?>
				</a>
			</div>
		</div>

		<!-- Product Table for Group Products -->
		<div class="fbs-plugin-card">
			<div class="fbs-plugin-card-header">
				<div class="fbs-plugin-icon-wrap bg-table">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
						<rect x="3" y="3" width="18" height="18" rx="2" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<line x1="9" y1="3" x2="9" y2="21" stroke="#ffffff" stroke-width="2"/>
						<line x1="15" y1="3" x2="15" y2="21" stroke="#ffffff" stroke-width="2"/>
						<line x1="3" y1="9" x2="21" y2="9" stroke="#ffffff" stroke-width="2"/>
						<line x1="3" y1="15" x2="21" y2="15" stroke="#ffffff" stroke-width="2"/>
					</svg>
				</div>
				<h3><?php esc_html_e( 'Product Table For Group Products', 'product-faq-for-woocommerce' ); ?></h3>
			</div>
			<div class="fbs-plugin-card-body">
				<p><?php esc_html_e( 'Upgrade WooCommerce grouped products into clean, high-converting comparison tables so shoppers can select quantities and add bundles on a single screen.', 'product-faq-for-woocommerce' ); ?></p>
			</div>
			<div class="fbs-plugin-card-footer">
				<a href="https://wordpress.org/plugins/product-table-for-group-products/" target="_blank" rel="noopener noreferrer" class="fbs-plugin-btn btn-table">
					<?php esc_html_e( 'Install Free on WP.org ↗', 'product-faq-for-woocommerce' ); ?>
				</a>
			</div>
		</div>

		<!-- FBS Activity Tracker -->
		<div class="fbs-plugin-card">
			<div class="fbs-plugin-card-header">
				<div class="fbs-plugin-icon-wrap bg-activity">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
						<polyline points="22 12 18 12 15 21 9 3 6 12 2 12" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
				<h3><?php esc_html_e( 'FBS Activity Tracker', 'product-faq-for-woocommerce' ); ?></h3>
			</div>
			<div class="fbs-plugin-card-body">
				<p><?php esc_html_e( 'Real-time WordPress & WooCommerce security audit log. Track admin logins, product price/stock edits, plugin changes, and settings updates in a clean searchable log.', 'product-faq-for-woocommerce' ); ?></p>
			</div>
			<div class="fbs-plugin-card-footer">
				<a href="https://wordpress.org/plugins/fbs-activity-tracker/" target="_blank" rel="noopener noreferrer" class="fbs-plugin-btn btn-activity">
					<?php esc_html_e( 'Install Free on WP.org ↗', 'product-faq-for-woocommerce' ); ?>
				</a>
			</div>
		</div>

		<!-- Export Payout For AffiliateWP -->
		<div class="fbs-plugin-card">
			<div class="fbs-plugin-card-header">
				<div class="fbs-plugin-icon-wrap bg-payout">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
						<rect x="3" y="4" width="18" height="16" rx="2" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<line x1="16" y1="2" x2="16" y2="4" stroke="#ffffff" stroke-width="2"/>
						<line x1="8" y1="2" x2="8" y2="4" stroke="#ffffff" stroke-width="2"/>
						<line x1="3" y1="10" x2="21" y2="10" stroke="#ffffff" stroke-width="2"/>
						<path d="M12 14v4M10 16h4" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round"/>
					</svg>
				</div>
				<h3><?php esc_html_e( 'Export Payout For AffiliateWP', 'product-faq-for-woocommerce' ); ?></h3>
			</div>
			<div class="fbs-plugin-card-body">
				<p><?php esc_html_e( '1-click payout export utility for AffiliateWP. Filter affiliate payouts by date, status, or payment method and export clean CSV/Excel bookkeeping reports.', 'product-faq-for-woocommerce' ); ?></p>
			</div>
			<div class="fbs-plugin-card-footer">
				<a href="https://wordpress.org/plugins/export-payout-for-affiliatewp/" target="_blank" rel="noopener noreferrer" class="fbs-plugin-btn btn-payout">
					<?php esc_html_e( 'Install Free on WP.org ↗', 'product-faq-for-woocommerce' ); ?>
				</a>
			</div>
		</div>

	</div>

	<!-- Studio Services Callout -->
	<div style="margin-top: 28px; background: linear-gradient(135deg, #090d16 0%, #1e1b4b 65%, #0f172a 100%); border-radius: 16px; padding: 26px 30px; color: #ffffff; display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap;">
		<div style="max-width: 700px;">
			<h2 style="margin: 0 0 8px; font-size: 20px; font-weight: 800; color: #ffffff;">
				<?php esc_html_e( 'Need Guaranteed 95–100 WooCommerce Speed, Malware Cleanup, or Custom Development?', 'product-faq-for-woocommerce' ); ?>
			</h2>
			<p style="margin: 0; font-size: 14px; color: #cbd5e1; line-height: 1.6;">
				<?php esc_html_e( 'FBS Fusion engineers high-concurrency WooCommerce stores, fixes slow TTFB & 502 Bad Gateway crashes under heavy traffic, eradicates hidden backdoors, and builds custom WordPress & Laravel plugins.', 'product-faq-for-woocommerce' ); ?>
			</p>
		</div>
		<div style="display: flex; gap: 12px; flex-wrap: wrap;">
			<a href="<?php echo esc_url( $speed_url ); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 6px; padding: 11px 18px; border-radius: 9px; background: #10b981; color: #052e16; font-size: 13.5px; font-weight: 700; text-decoration: none;">
				<?php esc_html_e( '⚡ Speed & Security Services ↗', 'product-faq-for-woocommerce' ); ?>
			</a>
			<a href="<?php echo esc_url( $hire_url ); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 6px; padding: 11px 18px; border-radius: 9px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.24); color: #ffffff; font-size: 13.5px; font-weight: 700; text-decoration: none;">
				<?php esc_html_e( 'Request Free Store Audit ↗', 'product-faq-for-woocommerce' ); ?>
			</a>
		</div>
	</div>
</div>
